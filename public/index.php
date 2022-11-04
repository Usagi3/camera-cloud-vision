<?php
ini_set("display_errors", 1);
ini_set("error_reporting",E_ALL);
ini_set("error_log","../logs/error.log");

require_once __DIR__."/../vendor/autoload.php";

use Google\Cloud\Vision\V1\ImageAnnotatorClient;

define('BASE_PATH', __DIR__.'/../');
define('APP_PATH', BASE_PATH.'application/');
(new App\DotEnv(BASE_PATH))->load();

class Controller
{
    /**
     * $output
     *
     * @var Output
     */
    protected $output;

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
             * @var Google\Cloud\Vision\V1\EntityAnnotation $text
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

class Output
{
    /**
     * $twig
     *
     * @var Twig\Environment
     */
    protected $twig;

    /**
     * $headers
     *
     * @var array
     */
    protected $headers = [];

    public function __construct()
    {
        $this->twig = new Twig\Environment(
            new Twig\Loader\FilesystemLoader(APP_PATH. 'templates/'), []
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

class Security
{
    public static function isXmlHttpRequest()
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'
            && strpos($_SERVER['HTTP_ORIGIN'] ?? '', $_SERVER['SERVER_NAME']) !== false;
    }
}

class input
{
    public static function getFiles()
    {
        return $_FILES ?? [];
    }

    public static function requestMethod(): ?string
    {
        if (! $requestMethod = $_SERVER['REQUEST_METHOD'] ?? null) {
            throw new LogicException('Error');
        }
        return strtolower($requestMethod);
    }
}

/**
 * Class Sample
 *
 * @property Twig\Environment $twig
 */
class CloudVisionSample
{
    /**
     * $annotator
     *
     * @var ImageAnnotatorClient
     */
    protected $annotator;

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

$requestMethod = Input::requestMethod();

$controller = new Controller();
$controller->{$requestMethod}();
