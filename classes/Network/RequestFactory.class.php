<?php
    namespace fruithost\Network;

    class RequestFactory {
        private static ?RequestFactory $instance	= null;
        private array $_get							= [];

        public function __construct() {
            if(empty($this->_get)) {
                $this->init();
            }
        }

        public static function getInstance() : RequestFactory {
            if(self::$instance === null) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function init() : void {
            if(!empty($this->_get)) {
                return;
            }

            // Fix bad behavior of Apache's Alias configguration
            if(empty($_SERVER['QUERY_STRING']) && isset($_SERVER['REQUEST_URI']) && strtok($_SERVER['REQUEST_URI'], '?') !== false) {
                $split = explode('?', $_SERVER['REQUEST_URI']);

                if(count($split) > 1) {
                    $_SERVER['QUERY_STRING']	= $split[1];
                    parse_str($_SERVER['QUERY_STRING'], $_GET);
                }
            }

            $this->_get = $_GET;
        }

        public function has(string $name) : bool {
            return array_key_exists($name, $this->_get);
        }

        public function get(string $name) : mixed {
            if(!$this->has($name)) {
                return null;
            }

            return $this->_get[$name];
        }

        public function url() : string {
            return $_SERVER['REQUEST_URI'];
        }
    }
?>