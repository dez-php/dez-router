<?php

    namespace Dez\Router\Adapter;

    use Dez\Router\Exception;
    use Dez\Router\RouterAdapter;

    /**
     * Class NativeArray
     * @package Dez\Router\Adapter
     */
    class NativeArray extends RouterAdapter {

        /**
         * NativeArray constructor.
         * @param array $routesArray
         * @throws Exception
         */
        public function __construct( $routesArray ) {
            if ( ! is_array( $routesArray ) ) {
                throw new Exception('NativeArray you must pass native php array to constructor');
            }

            $this->setArrayRoutes( $routesArray );
            parent::__construct($this->getArrayRoutes());
        }

        protected function parse() {
            return $this;
        }

    }