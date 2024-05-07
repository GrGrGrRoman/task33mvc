<?php 

namespace App\core;

use function App\d;

class Router
{
    protected $routes = [];
    protected $params = [];

    public function __construct()
    {
        $arr = require_once ROUTES;
        foreach ($arr as $key => $value)
        {
            $this->add($key, $value);
        }
    }

    public function add($route, $params)
    {
        $route = preg_replace('/{([a-z]+):([^\}]+)}/', '(?P<\1>\2)', $route);
        $route = '#^' . $route . '$#';
        $this->routes[$route] = $params;        
    }
    
    public function match()
    {        
        $url = strtolower(trim($_SERVER['REQUEST_URI'], '/'));
        
        foreach ($this->routes as $route => $params)
        {
            if (preg_match($route, $url, $matches))
            {
                foreach ($matches as $key => $match)
                {
                    if (is_string($key))
                    {
                        if (is_numeric($match))
                        {
                            $match = (int) $match;
                        }
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }
    
    public function start()
    {
        if ($this->match())
        {
            $controllerFile = CONTROLLER . DIRECTORY_SEPARATOR . ucfirst($this->params['controller']) . 'Controller.php';
            $className = 'App\controllers\\' . ucfirst($this->params['controller']) . 'Controller';
            if (file_exists($controllerFile))
            {
                $action = $this->params['action'] . 'Action';
                if (method_exists($className, $action))
                {
                    $controller = new $className($this->params);
                    $controller->$action();
                }
                else
                {
                    View::errorCode(404);
                }               
            }
            else
            {
                View::errorCode(404);
            }
        }
        else
        {
            View::errorCode(404);
        }
    }
}
