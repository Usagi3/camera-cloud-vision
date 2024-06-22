<?php

namespace App;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Output
{
    /**
     * @var Environment
     */
    protected Environment $twig;

    /**
     * @var array
     */
    protected array $headers = [];

    public function __construct()
    {
        $this->twig = new Environment(
            new FilesystemLoader(APP_PATH. 'View/'), []
        );
    }

    public function setHeader(array $headers = []): void
    {
        foreach ($headers as $header) {
            $this->headers[] = $header;
        }
    }

    public function json(array $data = []): void
    {
        echo json_encode(
            ['code' => 200, 'data' => $data],
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
        );
    }

    public function display(string $twig, array $data = []): void
    {
        $this->twig->display($twig, $data);
    }
}