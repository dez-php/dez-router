<?php

namespace Test;

use Dez\DependencyInjection\Container;
use Dez\EventDispatcher\Dispatcher;
use Dez\Http\Request;
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

// require for router
$di->set( 'request', function() {
    return new Request();
} );

try {
    /** @var $router Router */
    $router = $di->get( 'router' );

    $router->add( '/products', [
        'controller'    => 'products',
        'action'        => 'item_detail',
    ] );
    $router->add( '/', [
        'controller' => 'index',
        'action'    => 'dashboard'
    ] );
    $router->add( '/:controller' );
    $router->add( '/:controller/:action' );

    $router->add( '/:controller/:action/:int/:any' );
    $route = $router->add( '/:controller/:action/:int/{catalog}-{name}-{id}.{format}' );
die(var_dump( $route ));
    $router->add( '/:controller/:action/:int' )->via( [ 'post', 'get' ] );
    $router->add( '/:controller/:action/:params' );

    $router->add( '/{lang}/{auth_id}/:module/:controller/:action.html', [
        'controller'    => 'products',
        'action'        => 'latest'
    ] )
        ->regex( 'lang', '[A-Z]{2}' )
        ->regex( 'auth_id', '\d+' )
        ->via( [ 'get', 'delete', 'post' ] );

    $router->handle();

//    die(var_dump( $router ));
//
//    if( $router->isFounded() ) {
//        $route  = $router->getMatchedRoute();
//        die(var_dump( $router ));
//    } else {
//        die('not found');
//    }

    foreach( $testRoutes as $testRoute ) {
        $router->handle( $testRoute );
        if( $router->isFounded() ) {
            var_dump( $testRoute, $router->getMatchedRoute()->getMatches() );
        } else {
            var_dump( $testRoute, 'not found' );
        }
    }

} catch ( \Exception $e ) {
    header('content-type: text\plain');
    die($e->getMessage() ."\n-------------\n". $e->getTraceAsString());
}
