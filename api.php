<?php

use App\App;

require_once __DIR__ . '/vendor/autoload.php';

/**
 * This class contains util methods for the API
 */
class Utils
{
    static function getParam(string $name): ?string
    {
        if (!isset($_GET[$name])) {
            return null;
        }

        return trim($_GET[$name]);
    }
}

/**
 * This class contains base methods for handlers
 */
abstract class Handler
{
	abstract public function handle(): void;

	protected function response(array|string $data): void
	{
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(['content' => $data]);
		exit;
	}
}

/**
 * Class to handle list all articles route
 */
class ListAllHandler extends Handler
{
	public function __construct(private App $app) {}

	public function handle(): void
	{
		$this->response($this->app->getListOfArticles());
	}
}

/**
 * Class to handle list all articles starting for prefix route
 */
class PrefixSearchHandler extends Handler
{
	public function __construct(private App $app, private string $prefix) {}

	public function handle(): void
	{
		$prefix = mb_strtolower($this->prefix);

		$articles = array_filter(
			$this->app->getListOfArticles(),
			fn($article) => mb_stripos($article, $prefix) === 0
		);

		$filtered = array_slice($articles, 0, 50);

		$this->response(array_values($filtered));
	}
}

/**
 * Class to handle article by title
 */
class TitleSearchHandler extends Handler
{
	public function __construct(private App $app, private string $title) {}

	public function handle(): void
	{
		$this->response($this->app->fetch(['title' => $this->title]));
	}
}

$app = new App();
// TODO A: Improve readability and clean up the following code to prepare for adding new handlers and routes.
// TODO B: Address performance concerns.
// TODO C: Identify and solve any potential security vulnerabilities in this code.

$routes = [
    'prefix' => fn($value) => (new PrefixSearchHandler($app, $value))->handle(),
    'title'  => fn($value) => (new TitleSearchHandler($app, $value))->handle(),
];

foreach ($routes as $param => $handler) {
    $search = Utils::getParam($param);

    if (!is_null($search)) {
        $handler($search);
    }
}

(new ListAllHandler($app))->handle();