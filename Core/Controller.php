<?php
namespace Core;

/**
 * Base Controller
 * 
 * php version 7.3.8
*/
abstract class controller
{
	/**
	 * parameters form the matched route
	 * @var array
	*/
	protected $route_params = [];

	/**
	 * class constructor
	 * @param [array] $route_params [paramters from the route]
	 * 
	 * @return void
	*/
	public function __construct($route_params)
	{
		$this->route_params = $route_params;
	}

	/**
     * Magic method called when a non-existent or inaccessible method is
     * called on an object of this class. Used to execute before and after
     * filter methods on action methods. Action methods need to be named
     * with an "Action" suffix, e.g. indexAction, showAction etc.
     *
     * @param string $name  Method name
     * @param array $args Arguments passed to the method
     *
     * @return void
    */
	public function __call($name, $args)
	{
		$method = $name . 'Action';

		if($this->before() !== false)
		{
			if(method_exists($this, $method))
			{
				call_user_func_array([$this, $method], $args);
				$this->after();
			}else
			{
				echo "This method {$method} is not exist in controller " . get_class($this);
			}
		}
	}

	/**
	 * before filter - called before an action method
	 * 
	 * @return void
	*/
	protected function before()
	{

	}

	/**
	 * after filter - called after action method
	 * @return void
	*/
	protected function after()
	{
	
	}

} // end of controller