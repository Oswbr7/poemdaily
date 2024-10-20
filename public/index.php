<?php
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Config\Adapter\Json as ConfigJson;
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
// Register an autoloader
$loader = new Loader();
$loader->registerDirs(
    [
        APP_PATH . '/controllers/',
        APP_PATH . '/models/'
    ]
);
$loader->register();
// Create a DI
$di = new FactoryDefault();
$config = new ConfigJson("../config/config.json");
// Setup the view component
$di->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);
// Setup a base URI
$di->set(
    'url',
    function () use ($config) {
        $url = new UrlProvider();
        $url->setBaseUri($config->company->domain);
        return $url;
    }
);
// Setup the router
$di->set(
    'router',
    function () {
        $router = new Phalcon\Mvc\Router(false); 

        // Definir la ruta para el registro
        $router->add(
            '/register',
            [
                'controller' => 'register',
                'action'     => 'index',
            ]
        )->setName('register');
        $router->add(
            '/login',
            [
                'controller' => 'login',
                'action'     => 'index',
            ]
        )->setName('login');
        $router->add(
            '/register/submit',
            [
                'controller' => 'register',
                'action'     => 'submit',
            ]
        )->setName('submit_register');
        // En tu configuración de enrutador
        $router->add(
            '/admin/dashboard',
            [
                'controller' => 'admin',
                'action'     => 'dashboard',
            ]
        )->setName('admin_dashboard');

        $router->add(
            '/client/dashboard',
            [
                'controller' => 'client',
                'action'     => 'dashboard',
            ]
        )->setName('cliente_dashboard');
        $router->add(
            '/client/like/{genre}/{poemID}',
            [
                'controller' => 'client',
                'action'     => 'like',
            ]
        )->setName('cliente_like');
        $router->add(
            '/client/favorite/{genre}/{poemID}',
            [
                'controller' => 'client',
                'action'     => 'favorite',
            ]
        )->setName('cliente_fav');
        $router->add(
            '/client/favview',
            [
                'controller' => 'client',
                'action'     => 'favview',
            ]
        )->setName('cliente_favview');
        $router->add(
            '/client/random',
            [
                'controller' => 'client',
                'action'     => 'random',
            ]
        )->setName('cliente_random');
        $router->add(
            '/client/poems',
            [
                'controller' => 'client',
                'action'     => 'poems',
            ]
        )->setName('cliente_poems');
        $router->add(
            '/login/login',
            [
                'controller' => 'login',
                'action'     => 'login',
            ]
        )->setName('login_submit');
        $router->add(
            '/admin/dashboard/getLovePoems',
            [
                'controller' => 'admin',
                'action'     => 'getLovePoems',
            ]
        )->setName('get_love_poems');
        $router->add(
            '/admin/dashboard/getHappyPoems',
            [
                'controller' => 'admin',
                'action'     => 'getHappyPoems',
            ]
        )->setName('get_happy_poems');
        $router->add(
            '/admin/dashboard/getHeartBreak',
            [
                'controller' => 'admin',
                'action'     => 'getHeartBreak',
            ]
        )->setName('get_heart_break');
        $router->add(
            '/admin/dashboard/getDistressPoems',
            [
                'controller' => 'admin',
                'action'     => 'getDistressPoems',
            ]
        )->setName('get_distress_poems');
        $router->add(
            '/admin/dashboard/getFriendShipPoems',
            [
                'controller' => 'admin',
                'action'     => 'getFriendShipPoems',
            ]
        )->setName('get_friend_ship_poems');
        $router->add(
            '/admin/dashboard/getAbsencePoems',
            [
                'controller' => 'admin',
                'action'     => 'getAbsencePoems',
            ]
        )->setName('get_absence_poems');
        $router->add(
            '/admin/dashboard/getDesolationPoems',
            [
                'controller' => 'admin',
                'action'     => 'getDesolationPoems',
            ]
        )->setName('get_desolation_poems');
        $router->add(
            '/client/logOut',
            [
                'controller' => 'admin',
                'action'     => 'logOut',
            ]
        )->setName('client_logout');
        return $router;
    }
);
// Setup the database service
$di->set(
    'db',
    function () {
        return new DbAdapter(
            [
                'host'     => 'localhost',
                'username' => 'root',
                'password' => '',
                'dbname'   => 'poemdaily2.0',
            ]
        );
    }
);
$application = new Application($di);
try {
    // Handle the request
    $response = $application->handle();
    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}