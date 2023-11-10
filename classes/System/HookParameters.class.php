<?php
    namespace fruithost\System;

    class HookParameters {
        private array $args = [];

        public function __construct() {
            $this->args = func_get_args();
        }

        public function get(?int $position = null) : mixed {
            if(!empty($position)) {
                return $this->args[$position];
            }

            return $this->args;
        }
    }
?>