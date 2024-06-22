<?php

namespace App;

use Generator;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;

/**
 * Class Sample
 */
class CloudVisionSample
{
    /**
     * @var ImageAnnotatorClient
     */
    protected ImageAnnotatorClient $annotator;

    public function __construct()
    {
        $this->annotator = new ImageAnnotatorClient();
    }

    public function getText(string $img): Generator
    {
        $annotation = $this->annotator->textDetection(fopen($img, 'r'));
        foreach ($annotation->getTextAnnotations() as $text) {
            yield $text;
        }
    }
}