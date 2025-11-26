<?php

namespace App;

use RuntimeException;
use Slim\Psr7\Request;

class Page
{

    private string $layout = 'layout';
    private ?Request $request;
    private ?string $title;

    public function __construct(?string $title = null, $request = null)
    {
        $this->title = $title;
        $this->request = $request;
    }

    public function setLayout(string $layout): self
    {
        $this->layout = $layout;
        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function render($template, $params = []): string
    {
        global $app;
        $content = $this->renderFile($template, array_merge($params, [
            'route' => $app->getRouteCollector()->getRouteParser(),
            'request' => $this->request,
        ]));

        return $this->renderFile($this->layout, [
            'hidden_title' => $params['hidden_title'] ?? false,
            'title' => $this->title,
            'content' => $content,
            'header' => $params['header'] ?? null
        ]);
    }

    public function renderFile(string $file, array $params = []): string
    {
        $viewPath = __DIR__ . "/views/$file.php";

        if (!file_exists($viewPath)) {
            throw new RuntimeException("View '$file' not found");
        }

        extract($params, EXTR_SKIP);

        ob_start();
        include $viewPath;
        return ob_get_clean();
    }

}