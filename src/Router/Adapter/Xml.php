<?php

    namespace Dez\Router\Adapter;

    use Dez\Router\Exception;
    use Dez\Router\Router;
    use Dez\Router\RouterAdapter;

    /**
     * Class Xml
     * @package Dez\Router\Adapter
     */
    class Xml extends RouterAdapter {

        /**
         * Xml constructor.
         * @param array $routesFile
         * @param Router $router
         * @throws Exception
         */
        public function __construct($routesFile, Router $router ) {
            if( ! file_exists( $routesFile ) ) {
                throw new Exception( 'Routes file not found ['. $routesFile .']' );
            }

            $this->setRoutesFile( $routesFile );
            $this->parse();

            parent::__construct( $this->getArrayRoutes(), $router );
        }

        /**
         * @return $this
         * @throws Exception
         */
        protected function parse() {
            $xmlContent = file_get_contents( $this->getRoutesFile() );

            if( empty( $xmlContent ) ) {
                throw new Exception( 'Content is empty XmlRoutes' );
            }

            $routes     = $this->buildArray( new \SimpleXMLElement( $xmlContent ), '', [] );

            $this->setArrayRoutes( $routes );

            return $this;
        }

        /**
         * @param \SimpleXMLElement $node
         * @param string $pseudoRegex
         * @param array $previousRegex
         * @return array
         */
        protected function buildArray(\SimpleXMLElement $node, $pseudoRegex = '', array $previousRegex = [] ) {
            $routes = [];

            if( $node->count() > 0 ) {
                foreach( $node as $i => $routeXml ) {
                    $route  = [];
                    foreach( $routeXml->attributes() as $name => $attribute ) {
                        if( $name === 'match' ) {
                            $regex              = "$pseudoRegex/$attribute";
                        } else {
                            if( in_array( $name, [ 'module', 'controller', 'action' ], true ) ) {
                                $route[ 'matches' ][ $name ] = (string) $attribute;
                            } else if ( $name === 'methods' ) {
                                $route[ 'methods' ] = array_map( 'strtoupper', explode( ',', (string) $attribute ) );
                            } else {
                                $route[ 'regex' ][ $name ] = (string) $attribute;
                            }
                        }
                    }

                    $currentRegex       = isset( $route[ 'regex' ] )
                        ? $route[ 'regex' ]
                        : [];

                    $route[ 'regex' ]   = $currentRegex + $previousRegex;

                    if( $routeXml->count() > 0 ) {
                        $routes = array_merge( $this->buildArray( $routeXml, $regex, $route[ 'regex' ] ), $routes );
                    }
                    $routes[ $regex ]     = $route;
                }

            }

            return $routes;
        }



    }