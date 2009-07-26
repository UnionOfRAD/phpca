<?php

class Test
{
    protected $a;
    private $b = array();

    public function __construct($a, array $b)
    {
        $this->a = $a;
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

    public function run()
    {
        $this->doRun();
    }

    protected function doRun()
    {
    }
}
?>