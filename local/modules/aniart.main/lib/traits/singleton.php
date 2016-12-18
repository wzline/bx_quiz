<?php


namespace Aniart\Main\Traits;


trait Singleton
{
    protected static $instance;

    protected function __construct(){}
    protected function __clone(){}
    protected function __wakeup(){}

    /**
     * @return static
     */
    public static function getInstance()
    {
        if(!static::$instance){
            static::$instance = new static();
        }
        return static::$instance;
    }
}