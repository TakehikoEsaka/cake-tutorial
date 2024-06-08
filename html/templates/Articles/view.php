<h1><?= h($article->title); ?></h1>
<h1><?= h($article->body); ?></h1>
<p><b>Tags:</b> <?= $article->created->format(DATE_RFC850); ?>

    <!-- HTML->linkメソッドで、href = /articles/edit/{$article->slug}にaタグを作る事が出来る -->
<p><?= $this->Html->link('Edit', ['action' => 'edit', $article->slug]) ?></p>