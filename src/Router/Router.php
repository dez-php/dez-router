<?php

    namespace Dez\Router;

    use Dez\DependencyInjection\Injectable;
    use Dez\EventDispatcher\Dispatcher;

    class Router extends Injectable implements RouterInterface {

        protected $eventDispatcher  = null;

        protected $controller       = '';

        protected $action           = '';

        protected $params           = [];

        protected $routes           = [];

        protected $handledUri       = '/';

        protected $matchedRoute     = null;

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

        public function handle( $uri = null ) {

            $this->setMatchedRoute( null );

            if( $uri !== null && is_string( $uri ) ) {
                $this->setHandledUri( $uri );
            }

            foreach( $this->getRoutes() as $route ) {

                if( $route->isRegexAble() ) {
                    $founded    = preg_match_all( $route->getCompiledPattern(), $this->getHandledUri(), $matches );
                } else {
                    $founded    = $this->getHandledUri() === $route->getPattern();
                }

                if( ! $founded ) {
                    continue;
                } else {
                    $this->setMatchedRoute( $route );
                    break;
                }

            }

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

        /**
         * @return string
         */
        public function getController() {
            return $this->controller;
        }

        /**
         * @param string $controller
         * @return static
         */
        public function setController($controller) {
            $this->controller = $controller;
            return $this;
        }

        /**
         * @return string
         */
        public function getAction() {
            return $this->action;
        }

        /**
         * @param string $action
         * @return static
         */
        public function setAction($action) {
            $this->action = $action;
            return $this;
        }

        /**
         * @return array
         */
        public function getParams() {
            return $this->params;
        }

        /**
         * @param array $params
         * @return static
         */
        public function setParams($params) {
            $this->params = $params;
            return $this;
        }

        /**
         * @return Route[]
         */
        public function getRoutes() {
            return $this->routes;
        }

        /**
         * @param array $routes
         * @return static
         */
        public function setRoutes($routes) {
            $this->routes = $routes;
            return $this;
        }

        /**
         * @return string
         */
        public function getHandledUri() {
            return $this->handledUri;
        }

        /**
         * @param string $handledUri
         * @return static
         */
        public function setHandledUri( $handledUri ) {
            $this->handledUri = $handledUri;
            return $this;
        }

        /**
         * @return null|Route
         */
        public function getMatchedRoute() {
            return $this->matchedRoute;
        }

        /**
         * @param Route $matchedRoute
         * @return static
         */
        public function setMatchedRoute( $matchedRoute ) {
            $this->matchedRoute = $matchedRoute;
            return $this;
        }


    }