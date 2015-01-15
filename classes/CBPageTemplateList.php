<?php

/**
 * This class implements the singleton design pattern. Only a single instance
 * will ever be created.
 */
class CBPageTemplateList implements IteratorAggregate {

    private static $instance;
    private static $list = ['CBPageTemplate'];

    /**
     * @return instance type
     */
    private function __construct() {}

    /**
     * @return instance type
     */
    public static function init() {

        if (!self::$instance) {

            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @return Traversable
     */
    public function getIterator() {

        return new ArrayIterator(self::$list);
    }
}
