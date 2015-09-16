<?php

namespace Test;

use Dez\DependencyInjection\Container;
use Dez\EventDispatcher\Dispatcher;
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
    '/products/index/',
    '/products/show/101',
    '/offer/item/202/best-offer-202.html'
);

$di = new Container();

$di->set( 'router', function() {
    return new Router();
} );

// require for router
$di->set( 'eventDispatcher', function() {
    return new Dispatcher();
} );

try {
    /** @var Router $router */
    $router = $di->get( 'router' );

    $router->add( '/:module', [
        'module' => 1,
        'controller' => 'index',
        'action' => 'index',
    ], [ 'GET' ] );

    $router->add( '/{{name:([^/]+)}}/item/:int/best-offer-:int.{{format:(json|html|plain+)}}', [
        'module' => 1,
        'controller' => 'index',
        'action' => 'index',
    ], [ 'GET' ] );

    $router->add( '/:module/:controller', [
        'module' => 1,
        'controller' => 2,
        'action' => 'index',
    ], [ 'GET' ] );

    $router->add( '/:module/:controller/:action', [
        'module' => 1,
        'controller' => 2,
        'action' => 3,
    ], [ 'GET' ] );

    $router->add( '/:module/:controller/:action/:params', [
        'module' => 1,
        'controller' => 2,
        'action' => 3,
        'params' => 4,
    ], [ 'GET' ] );

    $router->add( '/index/index', 'index::home', [ 'GET', 'POST' ] );

    $router->add( '/page-{{page_id}}/:controller/category-{{name:([a-z]{10,20})}}/some-{{id::int}}-{{pseudo:([a-z_-]{10,})}}-{{format:(json|html|plain+)}}.php', [
        'id' => 1,
    ], [ 'GET' ] );

    $router->add( '/page-:controller/do_:action/:int/:params', [
        'id' => 1,
    ], [ 'GET' ] );

    $router->add( '/super-welcome.html', [
        'controller'    => 'promo',
        'action'        => 'page1',
        'params'        => [
            1,2,3
        ]
    ], [ 'GET', ] );

    foreach( $testRoutes as $route ) {

        $router->handle( $route );

        var_dump( $route, $router->getMatchedRoute() );

    }

} catch ( \Exception $e ) {
    header('content-type: text\plain');
    die($e->getMessage() ."\n-------------\n". $e->getTraceAsString());
}

die(var_dump( $router ));