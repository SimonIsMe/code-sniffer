<?php

class Example
{
    public function methodA(){}

    public static function methodB(){}

    static public function methodC(){}

    public static $VALUE_A = '';

    static public $VALUE_B = '';

    public function methodD(): void
    {
        static::$VALUE_A = static::$VALUE_B;
    }
}