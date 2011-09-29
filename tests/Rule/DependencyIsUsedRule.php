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

	/**
     * @covers \spriebsch\PHPca\Rule\DependencyIsUsedRule
     */
    public function testDetectInstanceof()
    {
        $this->init(__DIR__ . '/../_testdata/DependencyIsUsedRule/instanceof.php');

        $rule = new DependencyIsUsedRule();

        $rule->check($this->file, $this->result);

		$this->assertFalse($this->result->hasViolations());
        $this->assertEquals(0, $this->result->getNumberOfViolations());
    }

	/**
     * @covers \spriebsch\PHPca\Rule\DependencyIsUsedRule
     */
    public function testDetectTypeHint()
    {
        $this->init(__DIR__ . '/../_testdata/DependencyIsUsedRule/hint.php');

        $rule = new DependencyIsUsedRule();

        $rule->check($this->file, $this->result);

		$this->assertFalse($this->result->hasViolations());
        $this->assertEquals(0, $this->result->getNumberOfViolations());
    }
}

?>