# DezByte Router

## Initialization
After ``composer install`` register few components to DI

```php
$di = new Container();

$di->set( 'router', function() {
  return new Router();
} );

$di->set( 'eventDispatcher', function() {
  new Dispatcher();
} );

$di->set( 'request', function() {
  return new Request();
} );

// try to fetch router from container
try {
  /** @var $router Router */
  $router = $di->get( 'router' );
} catch ( \Exception $e ) {
  die($e->getMessage());
}
```

## Register routes

```php
$router->add( '/:controller' );
$router->add( '/:controller/:action' );
$router->add( '/:controller/:action/:id' );
$router->add( '/:controller/:action/:token' );
$router->add( '/:controller/:action.:format/:module-:do/:params/:statusCode' )
  ->regex( 'format', 'html|json' );
  
// or import from files
$router
  ->importFromArray( [
      '/test.php'  => [
          'matches'   => [
              'controller'    => 'test'
          ]
      ]
  ] )
  ->importFromFileArray( './routes.php' )
  ->importFromJson( './routes.json' )
  ->importFromXml( './routes.xml' );
```

## Sample files
### ``routes.json``

```js
{
  "/":{},
  "/:format/:module/:controller/:action": {
    "regex": {
      "format": "html|json"
    }
  }
}
```

### ``routes.xml``

```xml
<routes>
  <route match=":module">
    <route match=":controller">
      <route match=":action">
        <route match=":hash" hash="[a-z0-9]{32}"></route>
        <route match=":params"></route>
      </route>
    </route>
  </route>
  <route match=":token" controller="auth" action="checkToken" token="[a-f0-9]{40}"></route>
</routes>
```

### ``routes.php``

```php
return [
  '/dashboard'    => [
    'matches'       => [
      'module'        => 'user-panel',
      'controller'    => 'index',
      'action'        => 'dashboard',
    ]
  ]
];
```
