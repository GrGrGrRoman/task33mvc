<?php 

namespace App\config;

define('ROOT', dirname(__DIR__));
define('CONTROLLER', ROOT . DIRECTORY_SEPARATOR . 'controllers');
define('MODEL', ROOT . DIRECTORY_SEPARATOR . 'models');
define('VIEW', ROOT . DIRECTORY_SEPARATOR . 'views');
define('CORE', ROOT . DIRECTORY_SEPARATOR . 'core');
define('LIB', ROOT . DIRECTORY_SEPARATOR . 'lib');
define('UPLOAD_DIR', dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'pic');
define('MAX_FILE_SIZE', 500000);
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('ROUTES', ROOT . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes.php');
define('CONF', ROOT . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR);
