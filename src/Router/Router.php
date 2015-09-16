<?php

    namespace Dez\Router;

    use Dez\DependencyInjection\Injectable;
    use Dez\EventDispatcher\Dispatcher;

    class Router extends Injectable implements RouterInterface {

        protected $eventDispatcher;

        protected $controller       = '';

        protected $action           = '';

        protected $params           = [];

        protected $routes           = [];

        public function __construct() {

        }

        public function add( $pattern = '', $paths = null, $method = null ) {

            if( $method !== null && ! is_array( $method ) ) {
                $method     = [ $method ];
            }

            $this->getEventDispatcher()->dispatch( 'beforeRouteAdd', new EventRouter( $this ) );

            $route  = new Route( $pattern, $paths, $method );

            $this->routes[]     = $route;

            $this->getEventDispatcher()->dispatch( 'afterRouteAdd', new EventRouter( $this ) );

            return $route;
        }

        /**
         * @return Dispatcher $event
         * @throws Exception
         */
        public function getEventDispatcher() {
            if( ! $this->eventDispatcher ) {
                if( $this->getDi()->has( 'eventDispatcher' ) ) {
                    $this->eventDispatcher    = $this->getDi()->get( 'eventDispatcher' );
                } else {
                    throw new Exception( 'EventDispatcher must be registered in DependencyInjection for Router' );
                }
            }
            return $this->eventDispatcher;
        }



    }