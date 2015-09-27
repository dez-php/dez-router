<?php

    namespace Dez\Router;

    use Dez\DependencyInjection\ContainerInterface;
    use Dez\DependencyInjection\Injectable;

    /**
     * @property Router router
     */
    abstract class RouterAdapter extends Injectable {

        /**
         * @var array
         */
        protected $arrayRoutes  = [];

        /**
         * @var string
         */
        protected $routesFile   = '';

        /**
         * RouterAdapter constructor.
         * @param array $arrayRoutes
         */
        public function __construct(array $arrayRoutes = [] ) {
            $this->setArrayRoutes( $arrayRoutes );
        }

        /**
         * @return array
         */
        public function getArrayRoutes() {
            return $this->arrayRoutes;
        }

        /**
         * @param array $arrayRoutes
         */
        public function setArrayRoutes( array $arrayRoutes ) {
            $this->arrayRoutes = $arrayRoutes;
        }

        /**
         * @return string
         */
        public function getRoutesFile() {
            return $this->routesFile;
        }

        /**
         * @param string $routesFile
         */
        public function setRoutesFile( $routesFile ) {
            $this->routesFile = $routesFile;
        }

        /**
         * @return Router
         * @throws Exception
         */
        public function loadRoutes() {

            if( ! $this->getDi() || ! ( $this->getDi() instanceof ContainerInterface ) ) {
                throw new Exception( 'DependencyInjection is require for '. static::class );
            }

            if( ! $this->getDi()->has( 'router' ) || ! ( $this->getDi()->get( 'router' ) instanceof RouterInterface ) ) {
                throw new Exception( 'Router must be registered in DependencyInjection for '. static::class );
            }

            $router     = $this->router;
            foreach( $this->getArrayRoutes() as $pseudoPattern => $routeParams ) {
                $route  = $router->add( $pseudoPattern, isset( $routeParams[ 'matches' ] ) ? $routeParams[ 'matches' ] : [] );
                if( isset( $routeParams[ 'methods' ] ) ) {
                    $route->via( $routeParams[ 'methods' ] );
                }
                if( isset( $routeParams[ 'regex' ] ) && count( $routeParams[ 'regex' ] ) > 0 ) {
                    foreach( $routeParams[ 'regex' ] as $name => $regex ) {
                        $route->regex( $name, $regex );
                    }
                }
            }

            return $router;
        }

        /**
         * @return mixed
         */
        abstract protected function parse();

    }