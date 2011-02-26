<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2011, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */
namespace lithium\console\command;

use lithium\net\http\Router;
use lithium\action\Request;
use lithium\core\Environment;

/**
 * The route command lets you inspect your routes and issue requests against the router.
 */
class Route extends \lithium\console\Command {

	/**
	 * Override the default 'development' environment.
	 *
	 * For example:
	 * {{{
	 * li3 route --env=production
	 * li3 route show /foo --env=test
	 * }}}
	 *
	 * @var string
	 */
	public $env = 'development';


	/**
	 * Load the routes file and set the environment.
	 */
	public function __construct($config = array()) {
		parent::__construct($config);

		Environment::set($this->env);
		require dirname(dirname(__DIR__)).'/config/routes.php';
	}

	/**
	 * Lists all connected routes to the router. See the `all()`
	 * method for details and examples.
	 *
	 * @return void
	 */
	public function run() {
		$this->all();
	}

	/**
	 * Lists all connected routes to the router. This is a convenience
	 * alias for the `show()` method.
	 *
	 * Example:
	 * {{{
	 * li3 route
	 * li3 route all
	 * }}}
	 *
	 * Will return an output similar to:
	 *
	 * {{{
	 * Template                        	Params
	 * --------                        	------
	 * /                               	{"controller":"pages","action":"view"}
	 * /pages/{:args}                  	{"controller":"pages","action":"view"}
	 * /test/{:args}                   	{"controller":"lithium\\test\\Controller","action":"index"}
	 * /test                           	{"controller":"lithium\\test\\Controller","action":"index"}
	 * /{:slug:[\w\-]+}                	{"controller":"posts","action":"show"}
	 * /{:controller}/{:action}/{:args}	{"action":"index"}
	 * }}}
	 *
	 * @return void
	 */
	public function all() {
		$routes = Router::get();
		$columns = array(
			array('Template', 'Params'),
			array('--------', '------')
		);

		foreach($routes As $route) {
			$info = $route->export();
			$columns[] = array($info['template'], json_encode($info['params']));
		}

		$this->columns($columns);
	}

	/**
	 * Returns the corresponding params for a given URL and an optional request
	 * method.
	 *
	 * Examples:
	 * {{{
	 * 1: li3 route show /foo
	 * 2: li3 route show post /foo/bar/1
	 * 3: li3 route show /test
	 * 4: li3 route show /test --env=production
	 * }}}
	 *
	 * Will return outputs similar to:
	 *
	 * {{{
	 * 1: {"controller":"foo","action":"index"	}
	 * 2: {"controller":"foo","action":"bar","args":["1"]}
	 * 3: {"controller":"lithium\\test\\Controller","action":"index"}
	 * 4: {"controller":"test","action":"index"}
	 * }}}
	 *
	 * @return void
	 */
	public function show() {

		$url = join(" ", $this->request->params['args']);
		$method = 'GET';

		if(empty($url)) {
			$this->stop(true, 'Please provide a valid URL');
		}

		if(preg_match('/^(GET|POST|PUT|DELETE) (.+)/i', $url, $matches)) {
			$method = strtoupper($matches[1]);
			$url = $matches[2];
		}

		$request = new Request(array('env' => array('REQUEST_METHOD' => $method)));
		$request->url = $url;

		$result = Router::process($request);
		if(empty($result->params)) {
			$this->out("No route found");
		} else {
			$this->out(json_encode($result->params));
		}
	}

}

?>