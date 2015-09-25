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

    $router->add( '/:controller/:action.:format/:module-:do/:params/:statusCode' )->regex( 'format', 'html|json' );
    $router->handle( '/auth/api.json/backend-token/test/123qwe/hash/crypt-data/500' );

    die(var_dump(
        $router->getModule(),
        $router->getController(),
        $router->getAction(),
        $router->getMatches(),
        $router->getDirtyMatches(),
        $router->getRawMatches()
    ));



} catch ( \Exception $e ) {
    header('content-type: text\plain');
    die($e->getMessage() ."\n-------------\n". $e->getTraceAsString());
}
