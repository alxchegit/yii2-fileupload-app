<?php
namespace test;

class Test
{
    public $test;
    public function __invoke($x)
    {
        echo $x;
    }
}