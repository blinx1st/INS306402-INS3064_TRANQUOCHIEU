<?php
declare(strict_types=1);

session_start();

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'app');
define('PUBLIC_PATH', __DIR__);

require_once APP_PATH . '/core/helpers.php';
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Repository.php';
require_once APP_PATH . '/core/ResourceController.php';
require_once APP_PATH . '/core/Mailer.php';
require_once APP_PATH . '/core/Router.php';

(new Router())->dispatch();
