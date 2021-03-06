<?php

namespace SandBox\Router\Test;

use Dez\DependencyInjection\Container;
use Dez\EventDispatcher\Dispatcher;
use Dez\Http\Request;
use Dez\Router\Adapter\Xml;
use Dez\Router\EventRoute;
use Dez\Router\EventRouter;
use Dez\Router\Router;

error_reporting(1);
ini_set('display_errors', 'On');

include_once './../vendor/autoload.php';

$testRoutes = array(
    '/',
    '/index',
    '/index/index',
    '/index/test',
    '/products',
    '/' . md5(time()),
    '/products/index/',
    '/products/show/101',
    '/products/1094',
    '/offer/item/202/best-offer-202.html',
    '/auth/api.json/backend-token/test/123qwe/hash/crypt-data/500'
);

$di = new Container();

$di->set('router', function () {
    return new Router();
});

// require for router
$di->set('eventDispatcher', function () {
    $dispatcher = new Dispatcher();

    /*    $dispatcher->addListener( EventRoute::BEFORE_COMPILE, function( $event, $name ) {
            var_dump($name);
        } );

        $dispatcher->addListener( EventRoute::AFTER_COMPILE, function( $event, $name ) {
            var_dump($name);
        } );

        $dispatcher->addListener( EventRouter::BEFORE_ROUTE_ADD, function( $event, $name ) {
            var_dump($name);
        } );

        $dispatcher->addListener( EventRouter::AFTER_ROUTE_ADD, function( $event, $name ) {
            var_dump($name);
        } );

        $dispatcher->addListener( EventRouter::BEFORE_HANDLE, function( $event, $name ) {
            var_dump($name);
        } );

        $dispatcher->addListener( EventRouter::AFTER_HANDLE, function( $event, $name ) {
            var_dump($name);
        } );

        $dispatcher->addListener( EventRouter::ROUTE_FOUNDED, function( $event, $name ) {
            var_dump($name);
            $event->stop();
            var_dump( $event->getRouter()->getMatchedRoute() );
        } );*/

    return $dispatcher;
});

// require for router
$di->set('request', function () {
    return new Request();
});

try {
    /** @var $router Router */
    $router = $di->get('router');

//    $router
//        ->importFromArray([
//            '/test.php' => [
//                'matches' => [
//                    'controller' => 'test'
//                ]
//            ]
//        ])
//        ->importFromFileArray('./routes.php')
//        ->importFromJson('./routes.json')
//        ->importFromXml('./routes.xml');

//
//
    $router->add('/:token', [
        'controller' => 'auth',
        'action' => 'checkToken',
    ])->regex('token', '[a-f0-9]{32}');

    $router->add('/:controller/:action/:id');
    $router->add('/:controller/:id', [
        'action' => 'item',
        'module' => 'shopper',
        'namespace' => __NAMESPACE__
    ]);
    $router->add('/:controller');
    $router->add('/:controller/:action');
    $router->add('/:controller/:action/:token');
    $router->add('/:controller/:action.:format/:module-:do/:params/:statusCode')->regex('format', 'html|json');

    $router->add('/:id-:pseudoName.:format')->regex('id', '\d+')->regex('format', 'html|json');

//    $router->handle('/products/show/444');
//
//    if ($router->isFounded()) {
//        print "Module: {$router->getModule()}\nController: {$router->getController()}\nAction: {$router->getAction()}\n";
//        print implode("\n", $router->getMatches());
//    }
//    die;

    foreach ($testRoutes as $testRoute) {
        $router->handle($testRoute);
        if ($router->isFounded()) {
            var_dump(
                '----------',
                'found',
                $testRoute,
                $router->getNamespace() .'\\'. $router->getModule() . '@' . $router->getController() . '::' . $router->getAction(),
                $router->getMatches(),
                '----------'
            );
        } else {
            var_dump('not found ' . $testRoute);
        }
    }

} catch (\Exception $e) {
    header('content-type: text\plain');
    die($e->getMessage());
}
