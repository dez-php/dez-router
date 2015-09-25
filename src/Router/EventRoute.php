<?php

    namespace Dez\Router;

    use Dez\EventDispatcher\Event;

    /**
     * Class EventRoute
     * @package Dez\Router
     */
    class EventRoute extends Event {

        const BEFORE_COMPILE    = 'beforeCompileRoute';

        const AFTER_COMPILE     = 'afterCompileRoute';

        /**
         * @var
         */
        protected $route;

        /**
         * @param RouteInterface $router
         */
        public function __construct( RouteInterface $router ) {
            $this->setRoute( $router );
        }

        /**
         * @return RouteInterface
         */
        public function getRoute() {
            return $this->route;
        }

        /**
         * @param RouteInterface $router
         * @return static
         */
        public function setRoute( RouteInterface $router ) {
            $this->route = $router;
            return $this;
        }



    }