<?php 

namespace App;

use App\core\Router;
use App\lib\Db;

session_start();

function d($str)
{
    echo '<pre>';
    var_dump($str);
    echo '</pre>';
}

function dd($str)
{
    echo '<pre>';
    var_dump($str);
    echo '</pre>';
    die;
}

require_once 'config' . DIRECTORY_SEPARATOR . 'config.php';
require_once 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// проверка соединения с БД
/* $pdo = new Db();
if ($pdo != null)
  echo 'Соединение с БД установлено!';
//d($pdo);
else
  echo 'Нет соединения с БД!'; */

$router = new Router;
$router->start();
