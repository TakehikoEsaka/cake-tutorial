<?php
// src/Controller/ArticlesController.php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Log\Log;

// www.example.com/articlesはこのArticlesControllerが使われる
// 名前は自動解決される
// 名前はURLと一致している必要がある
// Controllerは必ず必要で、アクセスするURLが違うとControllerがないといわれる
class ArticlesController extends AppController
{
    //  www.example.com/articles/index (www.example.com/articles)にアクセスすると呼ばれる
    // 名前はURLと一致している必要がある
    public function index()
    {
        // $this->Authorization->skipAuthorization();
        $articles = $this->paginate($this->Articles->find());

        // cakeではmethod名と一致したTemplates配下においたファイルを探しにいく
        // 名前は自動解決される

        // $this->set('変数名', 値); は、ビュー内で \$変数名 として値を利用できるようにします。
        // $this->set(compact('変数名')); は PHP の compact() 関数を使って、複数の変数を一度にビューに渡す便利な方法です。
        $this->set(compact('articles'));
    }

    public function view($slug)
    {
        // $this->Authorization->skipAuthorization();

        // $this.ArticleはDBのarticlesテーブルを見に行ってる
        // table名は自動で名前解決される
        $article = $this->Articles
            ->findBySlug($slug)
            // ->contain('Tags')
            ->firstOrFail();
        // debug($article);

        $this->set(compact('article'));
    }

    public function add()
    {
        $article = $this->Articles->newEmptyEntity();
        // $this->Authorization->authorize($article);

        // リクエストが HTTP POST リクエストであることを確認するために Cake\Http\ServerRequest::is() メソッドを 使用します。
        if ($this->request->is('post')) {
            // RequestデータをEntityの形式に変換(mershall)する
            // debug($this->request->getData());
            $article = $this->Articles->patchEntity($article, $this->request->getData());

            // Added: Set the user_id from the session.
            // $article->user_id = $this->request->getAttribute('identity')->getIdentifier();
            $article->user_id = 1;

            // DBに保存。返値は、？
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been saved.'));

                // Cake\Controller\Controller::redirect を使ってユーザーを記事一覧に戻します。
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add your article.'));
        } else {
            debug("it's not post request");
        }

        // 関連TableはOne->Manyのテーブルの順に記載する
        $tags = $this->Articles->Tags->find('list');
        debug($this->Articles->Tags);

        // ビューコンテキストに tags をセット
        $this->set('tags', $tags);
        $this->set('article', $article);
    }

    public function edit($slug)
    {
        // ここでSQLを生成している
        // containは関連付けられたtableに使えるようだ
        $article = $this->Articles
            ->findBySlug($slug)
            ->contain('Tags') // ArticlesTable.phpのinitializeで$this->belongsToMany('Tags')をつけると参照できる
            ->firstOrFail();
        // $this->Authorization->authorize($article);

        // debug($this->Articles->findBySlug($slug)->sql());

        if ($this->request->is(['post', 'put'])) {

            $this->Articles->patchEntity($article, $this->request->getData(), [
                // Added: Disable modification of user_id.
                'accessibleFields' => ['user_id' => false]
            ]);

            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been updated.'));
                return $this->redirect(['action' => 'index']);
            };

            // Log::debug($article->getErrors());
            $this->Flash->error(__('Unable to update your article.'));
        }

        // Get a list of tags.
        $tags = $this->Articles->Tags->find('list');

        // Set article & tags to the view context
        $this->set('tags', $tags);
        $this->set('article', $article);
    }

    public function delete($slug)
    {
        //  ユーザーが GET リクエストを使って記事を削除しようとすると、 allowMethod() は例外をスローします
        $this->request->allowMethod(['post', 'delete']);

        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        // $this->Authorization->authorize($article);

        if ($this->Articles->delete($article)) {
            $this->Flash->success(__('The {0} article has been deleted.', $article->title));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function tags(array $tags = [])
    {
        $this->Authorization->skipAuthorization();

        // Use the ArticlesTable to find tagged articles.
        $articles = $this->Articles->find('tagged', tags: $tags);

        // Pass variables into the view template context.
        $this->set([
            'articles' => $articles,
            'tags' => $tags
        ]);
    }
}
