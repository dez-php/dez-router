<?php

    namespace Dez\Router;

    interface RouterInterface {

        public function import( $fileExtention, $filePath );

        public function importFromJson( $filePath );

        public function importFromXml( $filePath );

        public function merge( RouterInterface $router );

    }