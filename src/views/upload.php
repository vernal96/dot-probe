<?php
/** @var Slim\Routing\RouteParser $route */

/** @var Request $request */

use Slim\Psr7\Request;

$params = $request->getQueryParams();
$errors = $params['errors'] ?? [];
?>

<?php if ($errors) : ?>
    <?php foreach ($errors as $error) : ?>
        <div class="alert alert-danger">
            <?= $error ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<a href="<?= $route->urlFor('list') ?>" class="btn btn-link p-0 mb-3">Перейти к списку</a>
<form action="/upload-form" class="mb-4" method="post" enctype="multipart/form-data">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Отрицательное фото</label>
            <input type="file" class="form-control" name="anxious"/>
        </div>
        <div class="col-md-6">
            <label class="form-label">Нейтральное фото</label>
            <input type="file" class="form-control" name="neutral"/>
        </div>
    </div>
    <button class="btn btn-primary mt-3" type="submit">Загрузить</button>
</form>