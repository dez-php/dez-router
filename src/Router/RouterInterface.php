<?php

    namespace Dez\Router;

    interface RouterInterface {

        public function importFromJson( $routesFile = null );

        public function importFromXml( $routesFile = null );

        public function importFromArray( $routesFile = null );

        public function importFromFileArray( $routesFile = null );

    }