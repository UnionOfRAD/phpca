<?php

namespace spriebsch\PHPca\Rule;

require_once __DIR__ . '/../../src/Rule/DocBlockTagsOrderRule.php';

class DocBlockTagsOrderRuleTest extends AbstractRuleTest
{
    /**
     * @covers \spriebsch\PHPca\Rule\DocBlockTagsOrderRule
     */
    public function testDetectWrongOrder()
    {
        $this->init(__DIR__ . '/../_testdata/DocBlockTagsOrderRule/wrong_order.php');

        $rule = new DocBlockTagsOrderRule();

        $rule->configure(array());
        $rule->check($this->file, $this->result);
        $this->assertFalse($this->result->hasViolations());

        $rule->configure(array('order' => 'link, see, param, return'));
        $rule->check($this->file, $this->result);
        $this->assertTrue($this->result->hasViolations());
    }
}
?>