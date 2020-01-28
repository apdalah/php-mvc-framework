<?php
namespace Core;

/**
 * Base Router
 * 
 * php version 7.3.8
*/
class Router 
{

	/**
	 * An accossiative array that will include all routes
	 * @var array
	*/
	protected $routes = [];

	/**
	 * contain param(controller and action) for a specific route
	 * @var array
	*/
	protected $params = [];

	/**
	 * add a route to routes array
	 * @param [string] $route [the route URL]
	 * @param [array] $param [controller => controllerName, action => functionName]
	 * @return @void
	*/
	public function add($route, $params = [])
	{
		/**
		 * convert the route to a regular expression
		 * 1- escape forward slashes
		*/
		$route = preg_replace('/\//', '\\/', $route);

		/**
		 * 2- convert variables e.g. {controller}
		*/
		$route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

		/**
		 * 3- convert variables with custom regular expressions e.g. {id:\d+}
		*/
		$route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

		/**
		 * 4- Add start and end delimiters , and case insensitive flag
		*/
		$route = '/^' . $route . '$/i';

		$this->routes[$route] = $params;
	}

	/**
	 * get all app routes
	 * @return [array]
	*/
	public function getRoutes()
	{
		return $this->routes;
	}

	/**
	 * check if the route is created or not then setting the params if 
	 * the route is exist
	 * 
	 * @param  [string] $url [route name]
	 * @return [bool]      [return true if the route is exist]
	*/
	public function match($url)
	{

		foreach($this->routes as $route => $params)
		{
			if(preg_match($route, $url, $matches))
			{

				foreach($matches as $key => $match)
				{
					if(is_string($key))
					{
						$params[$key] = $match;
					}
				}

				$this->params = $params;
				return true;

			}
		}

		return false;
	}

	/**
	 * get controllerName and action for the currently matched route
	 * @return [array]
	*/
	public function getParams()
	{
		return $this->params;
	}

	/**
     * Dispatch the route, creating the controller object and running the
     * action method
     *
     * @param string $url The route URL
     *
     * @return void
    */
	public function dispatch($url)
	{
		$url = $this->removeQueryStringVariables($url);
		// check if the route (URL) is created
		if($this->match($url))
		{
			$controller = $this->params['controller'];
			$controller = $this->convertToStudlyCaps($controller);
			// $controller = "App\Controllers\\$controller";
			$controller = $this->getNamespace() . $controller;

			// check if the controller class is exist
			if(class_exists($controller))
			{
				$controller_object = new $controller($this->params);

				$action = $this->params['action'];
				$action = $this->convertToCamelCase($action);

				// check if the method (action) is exixst and public (can be called from outside the class)
				if(is_callable([$controller_object, $action]))
				{
					$controller_object->$action();

				} else {
                    //echo "Method $action (in controller $controller) not found";
                    throw new \Exception("Method $action (in controller $controller) not found");
                }
            } else {
                //echo "Controller class $controller not found";
                throw new \Exception("Controller class $controller not found");
            }
        } else {
            //echo 'No route matched.';
            throw new \Exception('No route matched.', 404);
        }
	}

	/**
	 * convert the string with hyphens to StudyCaps
	 * e.g. controller-name => ControllerName
	 * 
	 * @param  string $string
	 * @return string        string after converssion (class-controller => ClassController)
	*/
	protected function convertToStudlyCaps($string)
	{
		return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
	}

	/**
	 * convert the string with hyphens to camelCase
	 * e.g. method-name => methodName
	 * 
	 * @param  string $string
	 * @return string
	*/
	protected function convertToCamelCase($string)
	{
		return lcfirst($this->convertToStudlyCaps($string));
	}

	/**
     * Remove the query string variables from the URL (if any). As the full
     * query string is used for the route, any variables at the end will need
     * to be removed before the route is matched to the routing table. For
     * example:
     *
     *   URL                           $_SERVER['QUERY_STRING']  Route
     *   -------------------------------------------------------------------
     *   localhost                     ''                        ''
     *   localhost/?                   ''                        ''
     *   localhost/?page=1             page=1                    ''
     *   localhost/posts?page=1        posts&page=1              posts
     *   localhost/posts/index         posts/index               posts/index
     *   localhost/posts/index?page=1  posts/index&page=1        posts/index
     *
     * A URL of the format localhost/?page (one variable name, no value) won't
     * work however. (NB. The .htaccess file converts the first ? to a & when
     * it's passed through to the $_SERVER variable).
     *
     * @param string $url The full URL
     *
     * @return string The URL with the query string variables removed
    */
	protected function removeQueryStringVariables($url)
	{
		if($url != '')
		{
			$parts = explode('&', $url, 2);
			if(strpos($parts[0], '=') === false)
			{
				$url = $parts[0];
			}else
			{
				$url = '';
			}

			return $url;
		}
	}

	/**
	 * Get the namespace for the controller class , the namespace defined in 
	 * the route parameters 
	 * 
	 * @return string $namespace
	*/
	private function getNamespace()
	{
		$namespace = "App\Controllers\\";

		if(array_key_exists("namespace", $this->params))
		{
			$namespace .= $this->params['namespace'] . "\\";
		}

		return $namespace;
	}

} // end of the Router class