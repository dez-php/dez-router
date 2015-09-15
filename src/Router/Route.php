<?php

    namespace Dez\Router;

    class Route implements RouteInterface {

        protected $pattern  = '';

        protected $paths    = null;

        protected $methods  = [];

        public function __construct( $pattern = '', $paths = null, array $methods = [] ) {

            $this->setPattern( $pattern );
            $this->setPaths( $paths );
            $this->setMethods( $methods );

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



    }