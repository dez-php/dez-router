<?php

    namespace Dez\Router;

    class Route implements RouteInterface {

        protected $pattern  = '';

        protected $compiledPattern  = '';

        protected $paths    = null;

        protected $methods  = [];

        public function __construct( $pattern = '', $paths = null, array $methods = [] ) {

            $this->setPattern( $pattern );
            $this->setPaths( $paths );
            $this->setMethods( $methods );

            $this->compilePattern();

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
         * @return null
         */
        public function getPaths() {
            return $this->paths;
        }

        /**
         * @param mixed $paths
         * @return static
         */
        public function setPaths( $paths ) {
            $this->paths = $paths;
            return $this;
        }

        /**
         * @return string
         */
        public function getPattern() {
            return $this->pattern;
        }

        /**
         * @param string $pattern
         * @return static
         */
        public function setPattern( $pattern = null ) {
            $this->pattern = $pattern;
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
        public function setCompiledPattern( $compiledPattern ) {
            $this->compiledPattern = $compiledPattern;
            return $this;
        }

        protected function compilePattern() {

            $this->setCompiledPattern( $this->getPattern() );

            if( strpos( $this->getCompiledPattern(), ':' ) !== false ) {
                $this->replaceMacros();
            }

            if( strpos( $this->getCompiledPattern(), '{{' ) !== false ) {
                $this->replacePseudoPatterns();
            }

        }

        protected function replaceMacros() {

            $pattern        = '([\w\d_-]+)';
            $anyPattern     = '(/.*)';
            $intPattern     = '(\d+)';

            $macroses       = [
                ':module'       => $pattern,
                ':controller'   => $pattern,
                ':action'       => $pattern,
                ':params'       => $anyPattern,
                ':any'          => $anyPattern,
                ':int'          => $intPattern,
            ];

            $compiled   = str_replace( array_keys( $macroses ), array_values( $macroses ), $this->getCompiledPattern() );

            $this->setCompiledPattern( $compiled );

        }

        protected function replacePseudoPatterns() {

            $pattern    = '/\{\{(?:([a-z_]*?)\:?(.*))\}\}/Uui';

            $compiled   = preg_replace_callback( $pattern, function( $matches ) {
                $this->paths[ $matches[1] ]     = 1;
                return trim( $matches[2], ':' );
            }, $this->getCompiledPattern() );

            $this->setCompiledPattern( $compiled );

        }



    }