<?php 

namespace App\core;

use function App\d;

class View
{
    public $path;
    public $route;
    public $layout = 'default';

    public function __construct($route)
    {
        $this->route = $route;
        $this->path = $route['controller'] . DIRECTORY_SEPARATOR . $route['action'];
    }

    public function render($title, $vars = [])
    {
        extract($vars);

        $path = VIEW . DIRECTORY_SEPARATOR . $this->path . '.phtml';
        if (file_exists($path))
        {
            ob_start();
            include $path;
            $content = ob_get_clean();
            include VIEW . DIRECTORY_SEPARATOR . 'layouts' . DIRECTORY_SEPARATOR . $this->layout . '.phtml';
        }
        else
        {
            self::errorCode(404);
        }
    }

    public function redirect($url)
    {
        header('location: ' . $url);
    }
    
    public static function errorCode($code)
    {
        http_response_code($code);
        $path = VIEW . DIRECTORY_SEPARATOR . 'errors' . DIRECTORY_SEPARATOR . $code . '.phtml';
        if (file_exists($path))
        {
            include $path;
        }        
        die;
    }
}