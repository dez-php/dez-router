<?php

    namespace Dez\Router;

    use Dez\DependencyInjection\Injectable;
    use Dez\EventDispatcher\Dispatcher;
    use Dez\EventDispatcher\EventInterface;
    use Dez\Http\Request;

    /**
     * @property EventInterface eventDispatcher
     * @property Request request
     */
    class Router extends Injectable implements RouterInterface {

        const URI_SOURCE_GET_VAR    = 1;

        const URI_SOURCE_SERVER     = 2;

        /**
         * @var string
         */
        protected $module           = '';

        /**
         * @var string
         */
        protected $controller       = '';

        /**
         * @var string
         */
        protected $action           = '';

        /**
         * @var bool
         */
        protected $founded          = false;

        /**
         * @var array
         */
        protected $routes           = [];

        /**
         * @var string
         */
        protected $targetUri        = '/';

        /**
         * @var int
         */
        protected $uriSource        = 0;

        /**
         * @var string
         */
        protected $uriGetVar        = '_route';

        /**
         * @var null
         */
        protected $matchedRoute     = null;

        /**
         * @throws Exception
         */
        public function __construct() {
            $this->setUriSource( self::URI_SOURCE_GET_VAR );
            $this->setController( 'index' )->setAction( 'index' );
        }

        /**
         * @param string $pattern
         * @param null $matches
         * @param null $methods
         * @return Route
         * @throws Exception
         * @throws \Dez\EventDispatcher\Exception
         */
        public function add( $pattern = '', $matches = null, $methods = null ) {
            $this->getEventDispatcher()->dispatch( 'beforeRouteAdd', new EventRouter( $this ) );

            $route  = new Route( $pattern, $matches, $methods );
            $route->setDi( $this->getDi() );
            $this->routes[]     = $route;

            $this->getEventDispatcher()->dispatch( 'afterRouteAdd', new EventRouter( $this ) );

            return $route;
        }

        /**
         * @param null $uri
         * @return $this
         * @throws Exception
         */
        public function handle( $uri = null ) {

            $this->setMatchedRoute( null );
            $this->setFounded( false );

            if( $uri === null ) {
                if( $this->getUriSource() == self::URI_SOURCE_GET_VAR ) {
                    $this->setTargetUri( $this->request->getQuery( $this->getUriGetVar(), '/' ) );
                } else {
                    $this->setTargetUri( $this->request->getServer( 'request_uri', '/' ) );
                }
            } else {
                $this->setTargetUri( $uri );
            }

            foreach( $this->getRoutes() as $route ) {
                if( $route->handleUri() === true ) {

                    if( $route->hasMatch( 'module' ) ) {
                        $this->setModule( $route->getMatch( 'module' ) );
                    }

                    if( $route->hasMatch( 'controller' ) ) {
                        $this->setController( $route->getMatch( 'controller' ) );
                    }

                    if( $route->hasMatch( 'action' ) ) {
                        $this->setAction( $route->getMatch( 'action' ) );
                    }

                    $this->setFounded( true );
                    break;
                }
            }

            return $this;
        }

        /**
         * @return Dispatcher $event
         * @throws Exception
         */
        public function getEventDispatcher() {
            if( ! $this->getDi()->has( 'eventDispatcher' ) ) {
                throw new Exception( 'EventDispatcher must be registered in DependencyInjection for Router' );
            }
            return $this->eventDispatcher;
        }

        /**
         * @return string
         */
        public function getModule() {
            return $this->module;
        }

        /**
         * @param string $module
         * @return static
         */
        public function setModule( $module ) {
            $this->module = $module;
            return $this;
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
        public function setController( $controller ) {
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
         * @return boolean
         */
        public function isFounded() {
            return $this->founded;
        }

        /**
         * @param boolean $founded
         * @return static
         */
        public function setFounded( $founded ) {
            $this->founded = $founded;
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
        public function setRoutes( $routes ) {
            $this->routes = $routes;
            return $this;
        }

        /**
         * @return string
         */
        public function getTargetUri() {
            return $this->targetUri;
        }

        /**
         * @param string $targetUri
         * @return static
         */
        public function setTargetUri( $targetUri ) {
            $this->targetUri = preg_replace( '~/+~u', '/', $targetUri );
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

        /**
         * @return int
         */
        public function getUriSource() {
            return $this->uriSource;
        }

        /**
         * @param int $uriSource
         * @return static
         * @throws Exception
         */
        public function setUriSource( $uriSource ) {
            if( ! in_array( $uriSource, [ self::URI_SOURCE_GET_VAR, self::URI_SOURCE_SERVER ] ) ) {
                throw new Exception( 'Incorrect URI source' );
            }
            $this->uriSource = $uriSource;
            return $this;
        }

        /**
         * @return string
         */
        public function getUriGetVar() {
            return $this->uriGetVar;
        }

        /**
         * @param string $uriGetVar
         * @return static
         */
        public function setUriGetVar( $uriGetVar ) {
            $this->uriGetVar = $uriGetVar;
            return $this;
        }

        /**
         * @param RouterInterface $router
         */
        public function merge( RouterInterface $router ) {

        }

    }