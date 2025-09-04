<?php

use App\App;

require_once __DIR__ . '/vendor/autoload.php';

/**
 * This class contains util methods for the API
 */
class Utils
{
	static function cache(string $key, int $ttl, callable $callback): mixed
	{
		$file = sys_get_temp_dir() . "/cache_" . md5($key);

		if (file_exists($file) && time() - filemtime($file) < $ttl) {
			return unserialize(file_get_contents($file));
		}

		$data = $callback();

		file_put_contents($file, serialize($data));

		return $data;
	}

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
	const LIMIT = 10;
	const TTL = 5;

	abstract public function handle(): void;

	protected function response(array|string $data): void
	{
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode(['content' => $data]);
		exit;
	}

	protected function paginate($page){
		$page = max(1, $page);
    	
		$from = ($page - 1) * self::LIMIT;
    
		return ['from' => $from, 'to' => self::LIMIT];
	}
}

/**
 * Class to handle list all articles route
 */
class ListAllHandler extends Handler
{
	public function __construct(private App $app, private ?int $page = 1) {}

	public function handle(): void
	{
		$pagination = $this->paginate($this->page);
		
		$articles = array_slice($this->app->getListOfArticles(), $pagination['from'], $pagination['to']);
		
		$this->response($articles);
	}
}

/**
 * Class to handle list all articles starting for prefix route
 */
class PrefixSearchHandler extends Handler
{
	public function __construct(private App $app, private string $prefix, private ?int $page = 1) {}

	public function handle(): void
	{
		$filtered = Utils::cache('article_prefix_' . $this->prefix . '_page_' . $this->page, 5, function () {
	
            $prefix = mb_strtolower($this->prefix);

            $articles = array_filter(
                $this->app->getListOfArticles(),
                fn($article) => mb_stripos($article, $prefix) === 0
            );

			$pagination = $this->paginate($this->page);

            return array_slice($articles, $pagination['from'], $pagination['to']);
        });

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
	'prefix' => fn($value) => (new PrefixSearchHandler($app, $value, Utils::getParam('page')))->handle(),
	'title'  => fn($value) => (new TitleSearchHandler($app, $value))->handle(),
];

foreach ($routes as $param => $handler) {
	$search = Utils::getParam($param);

	if (!is_null($search)) {
		$handler($search);
	}
}

(new ListAllHandler($app, Utils::getParam('page')))->handle();
