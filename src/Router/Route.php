<?php

    namespace Dez\Router;

    use Dez\DependencyInjection\ContainerInterface;
    use Dez\DependencyInjection\Injectable;
    use Dez\EventDispatcher\Dispatcher;
    use Dez\Http\RequestInterface;

    /**
     * @property Router router
     * @property RequestInterface request
     * @property Dispatcher eventDispatcher
     */
    class Route extends Injectable implements RouteInterface {

        /**
         * @var string
         */
        protected $pseudoPattern    = '';

        /**
         * @var string
         */
        protected $compiledPattern  = '';

        /**
         * @var array
         */
        protected $matches          = [];

        /**
         * @var array
         */
        protected $regexes          = [];

        /**
         * @var array
         */
        protected $methods          = [ 'GET' ];

        /**
         * @var bool
         */
        protected $regexAble        = false;

        /**
         * @var null
         */
        protected $routeId          = null;

        /**
         * @param string $pattern
         * @param array $matches
         * @param null $methods
         */
        public function __construct( $pattern = '', $matches = [], $methods = null ) {
            $this->setPseudoPattern( $pattern )->setMatches( $matches );

            if( count( $methods ) > 0 ) {
                $this->setMethods( $methods );
            }

            $this->setRouteId( spl_object_hash( $this ) );
            $this->replaceMacros();
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
         * @return array
         */
        public function getMethods() {
            return $this->methods;
        }

        /**
         * @param array $methods
         * @return static
         */
        public function setMethods( array $methods = [] ) {
            $this->methods = $methods;
            return $this;
        }

        /**
         * @return string
         */
        public function getPseudoPattern() {
            return $this->pseudoPattern;
        }

        /**
         * @param string $pseudoPattern
         * @return static
         */
        public function setPseudoPattern($pseudoPattern) {
            $this->pseudoPattern = $pseudoPattern;
            return $this;
        }

        /**
         * @return string
         */
        public function getCompiledPattern() {
            return $this->compiledPattern;
        }

        /**
         * @param string $compiledPattern
         * @return static
         */
        public function setCompiledPattern($compiledPattern) {
            $this->compiledPattern = $compiledPattern;
            return $this;
        }

        /**
         * @return array
         */
        public function getMatches() {
            return $this->matches;
        }

        /**
         * @param array $matches
         * @return static
         */
        public function setMatches( $matches ) {
            if( count( $matches ) > 0 ) {
                foreach( $matches as $name => $match ) {
                    $this->setMatch( $name, $match );
                }
            }

            return $this;
        }

        /**
         * @param $name
         * @param $match
         * @return $this
         */
        public function setMatch( $name, $match ) {
            $this->matches[ $name ]     = $match;
            return $this;
        }

        /**
         * @param $name
         * @return bool
         */
        public function hasMatch( $name ) {
            return isset( $this->matches[ $name ] );
        }

        /**
         * @param $name
         * @return array
         */
        public function getMatch( $name ) {
            return $this->hasMatch( $name ) ? $this->matches[ $name ] : [];
        }

        /**
         * @return null
         */
        public function getRouteId() {
            return $this->routeId;
        }

        /**
         * @param null $routeId
         * @return static
         */
        public function setRouteId( $routeId ) {
            $this->routeId = sha1( $routeId );
            return $this;
        }

        /**
         * @return boolean
         */
        public function isRegexAble() {
            return $this->regexAble;
        }

        /**
         * @param boolean $regexAble
         * @return static
         */
        public function setRegexAble( $regexAble ) {
            $this->regexAble = $regexAble;
            return $this;
        }

        /**
         * @param $name
         * @param $regex
         * @return $this
         */
        public function regex( $name, $regex ) {
            $this->regexes[ $name ]    = [
                'regex'         => '('. trim( $regex, '()' ) .')',
                'replacement'   => '{'. $name .'}'
            ];
            return $this;
        }

        /**
         * @param array $methods
         * @return $this
         */
        public function via( $methods = [] ) {
            if( count( $methods ) > 0 ) {
                $methods    = array_map( 'strtoupper', $methods );
                $this->setMethods( $methods );
            }
            return $this;
        }

        /**
         * @return bool
         * @throws Exception
         */
        public function handleUri() {
            if( ! $this->getDi()->has( 'request' ) || ! ( $this->getDi()->get( 'request' ) instanceof RequestInterface ) ) {
                throw new Exception( 'Request must be registered in DependencyInjection for Route' );
            }

            $request    = $this->request;
            if( $request->isMethod( $this->getMethods() ) ) {
                return $this->compilePattern();
            }
            return false;
        }

        /**
         * @return bool
         * @throws Exception
         */
        protected function compilePattern() {

            $compiled   = $this->getPseudoPattern();

            if( ! $this->getDi() || ! ( $this->getDi() instanceof ContainerInterface ) ) {
                throw new Exception( 'DependencyInjection is require for Route' );
            }

            if( ! $this->getDi()->has( 'router' ) || ! ( $this->getDi()->get( 'router' ) instanceof RouterInterface ) ) {
                throw new Exception( 'Router must be registered in DependencyInjection for Route' );
            }

            $router     = $this->router;
            $targetURI  = $router->getTargetUri();

            if( strpos( $this->getPseudoPattern(), '{' ) !== false ) {
                $this->setRegexAble( true );

                preg_match_all( '/\{(\w+)\}/Uuis', $this->getPseudoPattern(), $macrosMatches, PREG_PATTERN_ORDER );
                list( $macroses, $macrosNames )  = $macrosMatches;

                foreach( $macroses as $index => $macros ) {
                    if( isset( $this->regexes[ $macrosNames[ $index ] ] ) ) {
                        $regex  = $this->regexes[ $macrosNames[ $index ] ]['regex'];
                    } else {
                        $regex  = '([a-zA-Z0-9-_]+)';
                        $this->regex( $macrosNames[ $index ], $regex );
                    }
                    $compiled   = str_replace( $macros, $regex, $compiled );
                }

                $this->setCompiledPattern( $compiled );

                preg_match_all( "~^$compiled$~Uus", $targetURI, $matches, PREG_SET_ORDER );

                if( count( $matches ) > 0 && count( $matches ) !== count( $matches, true ) ) {
                    $matches        = $matches[0];
                    array_shift( $matches );

                    foreach( $matches as $index => $foundValue ) {
                        $this->setMatch( $macrosNames[$index], $foundValue );
                    }

                    $router->setMatchedRoute( $this );
                    return true;
                }
            } else {
                if( $targetURI === $this->getPseudoPattern() ) {
                    $router->setMatchedRoute( $this );
                    return true;
                }
            }

            return false;
        }

        /**
         * @return $this
         */
        protected function replaceMacros() {

            $pattern        = '([a-zA-Z0-9-_]+)';
            $anyPattern     = '(.*)';
            $intPattern     = '(\d+)';

            $macroses       = [
                ':module'       => [ 'module', $pattern ],
                ':controller'   => [ 'controller', $pattern ],
                ':action'       => [ 'action', $pattern ],
                ':params'       => [ 'params', $anyPattern ],
                ':any'          => [ 'any', $anyPattern ],
                ':int'          => [ 'id', $intPattern ],
            ];

            $compiled       = $this->getPseudoPattern();

            foreach( $macroses as $macros => $replacement ) {
                if( strpos( $compiled, $macros ) ) {
                    $compiled   = str_replace( $macros, "{{$replacement[0]}}", $compiled );
                    $this->regex( $replacement[0], $replacement[1] );
                }
            }

            $this->setPseudoPattern( $compiled );

            return $this;
        }

    }