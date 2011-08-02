<?php

namespace spriebsch\PHPca\Rule;

use spriebsch\PHPca\Tokenizer;
use spriebsch\PHPca\Result;

require_once __DIR__ . '/../../src/Rule/DependencyIsUsedRule.php';

class DependencyIsUedRuleTest extends AbstractRuleTest
{
    protected function init($filename)
    {
        $this->file = Tokenizer::tokenize($filename, file_get_contents($filename));
        $this->result = new Result();
        $this->result->addFile($filename);
    }

	/**
     * @covers \spriebsch\PHPca\Rule\DependencyIsUsedRule
     */
    public function testDetect()
    {
        $this->init(__DIR__ . '/../_testdata/DependencyIsUsedRule/mixed.php');

        $rule = new DependencyIsUsedRule();

        $rule->check($this->file, $this->result);

        $this->assertTrue($this->result->hasViolations());
        $this->assertEquals(1, $this->result->getNumberOfViolations());
    }
}
?>