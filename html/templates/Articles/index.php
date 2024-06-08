<!-- File: src/Template/Articles/index.php -->
<?= $this->Html->link('Add Article', ['action' => 'add']) ?>
<h1>Articles</h1>
<table>
    <tr>
        <th>Title</th>
        <th>Created</th>
        <th>Action</th>
    </tr>

    <!-- Here is where we iterate through our $articles query object, printing out article info -->

    <?php foreach ($articles as $article) : ?>
        <tr>
            <td>
                <?= $this->Html->link($article->title, ['action' => 'view', $article->slug]) ?>

                <!-- この書式でphpコードの埋め込みができる -->
                <!-- ?=はechoをしているのと同じ形になる。だから基本コメントアウトはできない。やりたいなら?phpを使う -->
                <?= $article->body ?>
                <?php $article->body ?>

                <?php
                // debugもechoもviewで使える
                // debug($article);
                echo $article;
                ?>
            </td>
            <td>
                <?= $article->created->format(DATE_RFC850) ?>
            </td>
            <td>
                <?= $this->Html->link('Edit', ['action' => 'edit', $article->slug]) ?>

                <?=
                // postLinkはjsを使用して削除出来るようにする事が出来る
                $this->Form->postLink(
                    'Delete',
                    ['action' => 'delete', $article->slug],
                    ['confirm' => 'Are you sure?']
                )
                ?>


            </td>
        </tr>
    <?php endforeach; ?>
</table>