<?php

namespace Dez\Router\Adapter;

use Dez\Router\Exception;
use Dez\Router\RouterAdapter;

/**
 * Class Json
 * @package Dez\Router\Adapter
 */
class Json extends RouterAdapter {

    /**
     * Json constructor.
     * @param string $routesFile
     * @param Router $router
     * @throws Exception
     */
    public function __construct( $routesFile, Router $router  ) {
        if ( ! file_exists( $routesFile ) ) {
            throw new Exception('Routes file not found [' . $routesFile . ']');
        }

        $this->setRoutesFile( $routesFile );
        $this->parse();

        parent::__construct( $this->getArrayRoutes(), $router );
    }

    protected function parse() {
        $this->setArrayRoutes( json_decode( $this->getRoutesFile(), true ) );
        return $this;
    }

}