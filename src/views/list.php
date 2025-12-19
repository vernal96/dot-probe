<?php
/** @var Slim\Routing\RouteParser $route */
/** @var array $items */
?>

<a href="<?= $route->urlFor('upload.form') ?>" class="btn btn-link p-0 mb-3">Загрузить фото</a>
<ul class="list-group">
    <?php foreach ($items as $name => $item): ?>
        <li class="list-group-item d-flex align-items-center justify-content-between">
            <div class="d-flex gap-3 align-items-center">
                <img src="<?= $item[0] ?>" width="100" class="rounded border"/>
                <img src="<?= $item[1] ?>" width="100" class="rounded border"/>
            </div>
            <form method="POST" action="<?= $route->urlFor('delete.form') ?>">
                <input type="hidden" value="<?= $name ?>" name="id"/>
                <button type="submit" class="btn btn-danger btn-sm">Удалить</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>