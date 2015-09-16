<?php

    namespace Dez\Router;

    use Dez\EventDispatcher\Event;

    /**
     * Class EventRouter
     * @package Dez\Router
     */
    class EventRouter extends Event {

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