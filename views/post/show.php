<?php
use App\Connection;
use App\Model\{Post, Category};

$id = (int)$params['id'];
$slug = $params['slug'];

$pdo = Connection::getPdo();
$query = $pdo->prepare('SELECT * FROM post where id=:id');
$query->execute(['id' => $id]);
$query->setFetchMode(PDO::FETCH_CLASS, Post::class);
$post = $query->fetch();
/** @var Post|false */
if ($post === false) {
    throw new Exception('there is no post assigned to this id');
}

if ($post->getSlug() !== $slug) {
    $url = $router->url('post', ['slug' => $post->getSlug, 'id' => $id]);
    http_response_code(301);
    header('Location: ', $url);

}

$query = $pdo->prepare('
SELECT *
 FROM post_category pc
 JOIN category c ON pc.category_id = c.id
  where pc.post_id=:id');
$query->execute(['id' => $post->getId()]);
$query->setFetchMode(PDO::FETCH_CLASS, Category::class);
/** @var Category[] */
$categories = $query->fetchAll();
 ?>

<h5 class="card-title"><?= e($post->getName()) ?></h5>
<p class="text-muted"><?= $post->getCreatedAt()->format('d F Y') ?></p>
<?php foreach($categories as $k => $category): ?>
<?php if( $k > 0 ): 
echo ', ';
endif;
$category_url = $router->url('category', ['id' => $category->getID(), 'slug' => $category->getSlug()]);
 ?><a href="<?= $category_url ?>"><?= e($category->getName()) ?></a><?php
  endforeach ?>
<p><?= $post->getFormattedContent() ?></p>
