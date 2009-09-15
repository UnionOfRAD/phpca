<?php

class Test
{
    protected $a;
    private $b = array();

    public function __construct($a, array $b)
    {
        $this->a = $a;
        die();
        $this->b = $b;
    }

    public function setA($a)
    {
        $this->a = $a;
    }

    public function getA()
    {
        return $this->a;
    }
}

function run()
{
    doRun();
}

function doRun()
{
}
?>