<?php

namespace App;

use LogicException;
use Google\Cloud\Vision\V1\EntityAnnotation;

class Service
{
    /**
     * $output
     *
     * @var Output
     */
    protected Output $output;

    public function __construct()
    {
        $this->output = new Output();
    }

    public function __call($name, $arguments)
    {
        if (! method_exists($this, $name)) {
            throw new LogicException('Error');
        }
        $this->{$name}($arguments);
    }

    protected function get(): void
    {
        $this->output->display('index.twig');
    }

    protected function post(): void
    {
        if (! Security::isXmlHttpRequest()) {
            throw new LogicException('Error');
        } elseif (! $files = Input::getFiles()) {
            throw new LogicException('Error');
        }

        $sample = new CloudVisionSample();

        $data = [];
        foreach ($sample->getText($files['img']['tmp_name']) as $text) {
            /**
             * @var EntityAnnotation $text
             */
            $data[] = $text->getDescription().PHP_EOL;
        }

        $this->output->setHeader([
            'X-FRAME-OPTIONS: SAMEORIGIN',
            'X-Content-Type-Options: nosniff',
            'X-XSS-Protection: 1; mode=block',
            'application/json; charset=UTF-8'
        ]);

        $this->output->json($data);
    }
}