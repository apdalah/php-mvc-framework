<?php
namespace Core;

/**
 * View Class
 * 
 * php version 7.3.8
*/

class View
{
	/**
	 * Render a view file
	 * 
	 * @param string $view The view file
	 * @return void 
	*/
	public static function render($view, $args = [])
	{
		extract($args, EXTR_SKIP);

		$view = str_replace('.', '/', $view);
		$file = '../App/Views/' . $view . '.php';

		if(is_readable($file))
		{
			require $file;
		}else
		{
			throw new \Exception("$file not found");
		}
	}

	public static function renderTemplate($viewTemplate, $args = [])
	{
		$twig = null;

		if($twig === null)
		{
			$viewTemplate = str_replace('.', '/', $viewTemplate) . '.php';
			$loader = new \Twig\Loader\FilesystemLoader('../App/Views');
			$twig = new \Twig\Environment($loader);
		}

		echo $twig->render($viewTemplate, $args);
	}
}