<?php

namespace spriebsch\PHPca\Rule;

use spriebsch\PHPca\Configuration;

require_once __DIR__ . '/../../src/Rule/MaximumLineLengthRule.php';

class MaximumLineLengthRuleTest extends AbstractRuleTest
{
    /**
     * @covers \spriebsch\PHPca\Rule\MaximumLineLengthRule
     */
    public function testTabsCountAsFourCharactersTowardsLength()
    {
        $this->init(__DIR__ . '/../_testdata/MaximumLineLengthRule/tabbed_over.php');

        $rule = new MaximumLineLengthRule();
        $conf = new Configuration("");
        $conf->setLineEndings("\n");
        $rule->setConfiguration($conf);
        $rule->configure(array("line_length" => 100));

        $rule->check($this->file, $this->result);

        $this->assertTrue($this->result->hasViolations());
    }

	/**
     * @covers \spriebsch\PHPca\Rule\MaximumLineLengthRule
     */
    public function testTabsCountAsFourCharactersTowardsLengthPass()
    {
        $this->init(__DIR__ . '/../_testdata/MaximumLineLengthRule/tabbed_over_pass.php');

        $rule = new MaximumLineLengthRule();
        $conf = new Configuration("");
        $conf->setLineEndings("\n");
        $rule->setConfiguration($conf);
        $rule->configure(array("line_length" => 100));

        $rule->check($this->file, $this->result);

        $this->assertFalse($this->result->hasViolations());
    }
}
?>