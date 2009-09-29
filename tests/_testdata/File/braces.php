<?php

class Test
{
    protected $a;
    private $b = array();

    public function __construct($a, array $b)
    {
        if ($a) {
            foreach ($b as $item) {
                if ($item == 'something') {
                    $this->setA($item);
                } else {
                    continue;
                }
            }
        } else {
            for ($i = 0; $i < 23; $i++) {
                $this->doRun();
            }
        }

    }
}
?>