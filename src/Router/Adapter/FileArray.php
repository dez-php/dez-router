<?php

namespace Dez\Router\Adapter;

use Dez\Router\Exception;
use Dez\Router\Router;
use Dez\Router\RouterAdapter;

/**
 * Class FileArray
 * @package Dez\Router\Adapter
 */
class FileArray extends RouterAdapter
{

    /**
     * FileArray constructor.
     * @param string $routesFile
     * @param Router $router
     * @throws Exception
     */
    public function __construct($routesFile, Router $router)
    {
        if (!file_exists($routesFile)) {
            throw new Exception('Routes file not found [' . $routesFile . ']');
        }

        $this->setRoutesFile($routesFile);
        $this->parse();

        parent::__construct($this->getArrayRoutes(), $router);
    }

    protected function parse()
    {
        $routes = include $this->getRoutesFile();
        $this->setArrayRoutes($routes);
        return $this;
    }

}