<?php

namespace spriebsch\PHPca\Rule;

use spriebsch\PHPca\Token;

class TestRuleSubclass extends Rule
{
    protected function doCheck()
    {
        $this->addWarning('a warning', $this->file[0]);
    }
}
?>