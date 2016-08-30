<?php

namespace Dez\Router;

/**
 * Class RouterAdapter
 * @package Dez\Router
 */
abstract class RouterAdapter
{

    /**
     * @var array
     */
    protected $arrayRoutes = [];

    /**
     * @var string
     */
    protected $routesFile = '';

    /**
     * @var null
     */
    protected $router = null;

    /**
     * RouterAdapter constructor.
     * @param array $arrayRoutes
     * @param RouterInterface $router
     */
    public function __construct(array $arrayRoutes = [], RouterInterface $router)
    {
        $this->setArrayRoutes($arrayRoutes);
        $this->setRouter($router);
        $this->loadRoutes();
    }

    /**
     * @return array
     */
    public function getArrayRoutes()
    {
        return $this->arrayRoutes;
    }

    /**
     * @param array $arrayRoutes
     */
    public function setArrayRoutes(array $arrayRoutes)
    {
        $this->arrayRoutes = $arrayRoutes;
    }

    /**
     * @return string
     */
    public function getRoutesFile()
    {
        return $this->routesFile;
    }

    /**
     * @param string $routesFile
     */
    public function setRoutesFile($routesFile)
    {
        $this->routesFile = $routesFile;
    }

    /**
     * @return null
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param null $router
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }

    /**
     * @return Router
     * @throws Exception
     */
    protected function loadRoutes()
    {

        foreach ($this->getArrayRoutes() as $pseudoPattern => $routeParams) {
            $route = $this->getRouter()->add($pseudoPattern, isset($routeParams['matches']) ? $routeParams['matches'] : []);
            if (isset($routeParams['methods'])) {
                $route->via($routeParams['methods']);
            }
            if (isset($routeParams['regex']) && count($routeParams['regex']) > 0) {
                foreach ($routeParams['regex'] as $name => $regex) {
                    $route->regex($name, $regex);
                }
            }
        }

        return $this->getRouter();
    }

    /**
     * @return mixed
     */
    abstract protected function parse();

}