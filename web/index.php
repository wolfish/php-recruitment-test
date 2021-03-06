<?php

use Snowdog\DevTest\Component\Menu;
use Snowdog\DevTest\Component\RouteRepository;

session_start();

define('WEB_DIR', __DIR__);

$container = require __DIR__ . '/../app/bootstrap.php';

$routeRepository = RouteRepository::getInstance();

$dispatcher = \FastRoute\simpleDispatcher($routeRepository);

Menu::setContainer($container);

$route = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
switch ($route[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found");
        require __DIR__ . '/../src/view/404.phtml';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        header($_SERVER['SERVER_PROTOCOL'] . " 405 Method Not Allowed");
        require __DIR__ . '/../src/view/405.phtml';
        break;
    case FastRoute\Dispatcher::FOUND:
        $controller = $route[1];
        $parameters = $route[2];
        $container->call($controller, $parameters);
        break;
}

// Checking after call, because action can return 403 redirect
if (http_response_code() === 403) {
    if (!isset($_SESSION['login'])) {
        header('Location: /login');
    } else {
        header($_SERVER['SERVER_PROTOCOL'] . " 403 Forbidden");
        require __DIR__ . '/../src/view/403.phtml';
    }
}
