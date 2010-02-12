<?php

namespace spriebsch\PHPca\Rule;

use spriebsch\PHPca\Tokenizer;
use spriebsch\PHPca\Result;

require_once __DIR__ . '/../../src/Rule/NotExecutableRule.php';

class NotExecutableRuleTest extends AbstractRuleTest
{
    protected function init($filename)
    {
        $this->file = Tokenizer::tokenize($filename, file_get_contents($filename));
        $this->result = new Result();
        $this->result->addFile($filename);
    }

	/**
     * @covers \spriebsch\PHPca\Rule\NotExecutableRule
     */
    public function testDetectExecutable()
    {
        $this->init(__DIR__ . '/../_testdata/NotExecutableRule/executable.php');

        $rule = new NotExecutableRule();

        $rule->check($this->file, $this->result);

        $this->assertTrue($this->result->hasViolations());
    }

	/**
     * @covers \spriebsch\PHPca\Rule\NotExecutableRule
     */
    public function testNotExecutablePasses()
    {
        $this->init(__DIR__ . '/../_testdata/NotExecutableRule/not_executable.php');

        $rule = new NotExecutableRule();

        $rule->check($this->file, $this->result);

        $this->assertFalse($this->result->hasViolations());
    }
}
?>