<?php

namespace spriebsch\PHPca\Rule;

require_once __DIR__ . '/../../src/Rule/NamespaceComesAfterOpeningAndNewlineRule.php';

class NamespaceComesAfterOpeningAndNewlineRuleTest extends AbstractRuleTest
{
    /**
     * @covers \spriebsch\PHPca\Rule\NamespaceComesAfterOpeningAndNewlineRule
     */
    public function testNoNewlineRaisesViolation()
    {
        $this->init(__DIR__ . '/../_testdata/NamespaceComesAfterOpeningAndNewlineRule/no_newline.php');

        $rule = new NamespaceComesAfterOpeningAndNewlineRule();
        $rule->check($this->file, $this->result);

        $this->assertTrue($this->result->hasViolations());
    }

    public function testNotImmediatelyAfterOpeningRaisesViolation()
    {
        $this->init(__DIR__ . '/../_testdata/NamespaceComesAfterOpeningAndNewlineRule/not_following.php');

        $rule = new NamespaceComesAfterOpeningAndNewlineRule();
        $rule->check($this->file, $this->result);

        $this->assertTrue($this->result->hasViolations());
    }

    public function testPreceedingOpeningTagIsAtStartOfFile()
    {
        $this->init(__DIR__ . '/../_testdata/NamespaceComesAfterOpeningAndNewlineRule/not_first_opening.php');

        $rule = new NamespaceComesAfterOpeningAndNewlineRule();
        $rule->check($this->file, $this->result);

        $this->assertTrue($this->result->hasViolations());
    }

    public function testProperFormatPasses()
    {
        $this->init(__DIR__ . '/../_testdata/NamespaceComesAfterOpeningAndNewlineRule/proper.php');

        $rule = new NamespaceComesAfterOpeningAndNewlineRule();
        $rule->check($this->file, $this->result);

        $this->assertFalse($this->result->hasViolations());
    }
}

?>