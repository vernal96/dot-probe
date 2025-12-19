<?php

/** @var App $app */

use App\Page;
use App\UseCase\UploadPhoto;
use Slim\App;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(new Page('Главная', $request)->render('index', [
        'hidden_title' => true,
        'items' => new UploadPhoto()->get(),
        'header' => new Page()->renderFile('index-header')
    ]));
    return $response;
})->setName('index');

$app->get('/upload-form', function (Request $request, Response $response) {
    $response->getBody()->write(new Page('Загрузка фото', $request)->render('upload'));
    return $response;
})->setName('upload.form');

$app->get('/list', function (Request $request, Response $response) {
    $response->getBody()->write(new Page('Фотографии', $request)->render('list', [
        'items' => new UploadPhoto()->get()
    ]));
    return $response;
})->setName('list');

$app->post('/delete', function (Request $request, Response $response) {
    global $app;

    new UploadPhoto()->delete($_POST['id']);

    return $response
        ->withHeader('Location', $app->getRouteCollector()->getRouteParser()->urlFor('list'))
        ->withStatus(302);
})->setName('delete.form');

$app->post('/upload-form', function (Request $request, Response $response) {
    global $app;
    $errors = [];

    foreach ($request->getUploadedFiles() as $name => $file) {
        if (!$file->getClientFilename()) {
            $errors[] = "Файл $name не загружен";
        }
    }

    if (!$errors) {
        new UploadPhoto()->load();
    }

    return $response
        ->withHeader(
            'Location',
            $app->getRouteCollector()->getRouteParser()->urlFor(
                routeName: $errors ? 'upload.form' : 'list',
                queryParams: ['errors' => $errors]
            )
        )
        ->withStatus(302);
});