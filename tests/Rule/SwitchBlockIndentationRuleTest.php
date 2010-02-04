<?php

namespace spriebsch\PHPca\Rule;

require_once __DIR__ . '/../../src/Rule/SwitchBlockIndentationRule.php';

class SwitchBlockIndentationRuleTest extends AbstractRuleTest
{
    /**
     * @covers \spriebsch\PHPca\Rule\SwitchBlockIndentationRule
     */
    public function testDetectWrongIndentation()
    {
        $this->init(__DIR__ . '/../_testdata/SwitchBlockIndentationRule/incorrectly_indented.php');

        $rule = new SwitchBlockIndentationRule();
        $rule->check($this->file, $this->result);

        $this->assertTrue($this->result->hasViolations());
    }

	public function testValidIndentationPasses()
	{
		$this->init(__DIR__ . '/../_testdata/SwitchBlockIndentationRule/correctly_indented.php');

        $rule = new SwitchBlockIndentationRule();
        $rule->check($this->file, $this->result);

        $this->assertFalse($this->result->hasViolations());
	}
}
?>