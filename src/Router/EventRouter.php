<?php

    namespace Dez\Router;

    use Dez\EventDispatcher\Event;

    /**
     * Class EventRouter
     * @package Dez\Router
     */
    class EventRouter extends Event {

        const BEFORE_ROUTE_ADD  = 'beforeRouteAdd';

        const AFTER_ROUTE_ADD   = 'afterRouteAdd';

        const BEFORE_HANDLE     = 'beforeHandleRoute';

        const AFTER_HANDLE      = 'afterHandleRoute';

        const ROUTE_FOUNDED     = 'routeFounded';

        /**
         * @var
         */
        protected $router;

        /**
         * @param RouterInterface $router
         */
        public function __construct( RouterInterface $router ) {
            $this->setRouter( $router );
        }

        /**
         * @return RouterInterface
         */
        public function getRouter() {
            return $this->router;
        }

        /**
         * @param RouterInterface $router
         * @return static
         */
        public function setRouter( RouterInterface $router ) {
            $this->router = $router;
            return $this;
        }



    }