<?php

/** @var string $title */
/** @var string $content */
/** @var bool $hidden_title */

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= $header ?? '' ?>
</head>
<body class="bg-light p-4">
<div class="container">
    <?php if (!$hidden_title) : ?>
        <h1 class="mb-4"><?= $title ?></h1>
    <?php endif; ?>
    <?= $content ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>