<?php

    namespace Dez\Router;

    use Dez\DependencyInjection\Injectable;
    use Dez\EventDispatcher\Dispatcher;

    class Router extends Injectable {

        protected $eventDispatcher;

        protected $controller       = '';

        protected $action           = '';

        protected $params           = [];

        protected $routes           = [];

        public function __construct() {

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