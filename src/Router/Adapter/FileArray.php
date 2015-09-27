<?php

namespace Dez\Router\Adapter;

use Dez\Router\Exception;
use Dez\Router\RouterAdapter;

/**
 * Class FileArray
 * @package Dez\Router\Adapter
 */
class FileArray extends RouterAdapter {

    /**
     * FileArray constructor.
     * @param string $routesFile
     * @throws Exception
     */
    public function __construct( $routesFile ) {
        if ( ! file_exists( $routesFile ) ) {
            throw new Exception('Routes file not found [' . $routesFile . ']');
        }

        $this->setRoutesFile( $routesFile );
        $this->parse();

        parent::__construct($this->getArrayRoutes());
    }

    protected function parse() {
        $this->setArrayRoutes( require $this->getRoutesFile() );
        return $this;
    }

}