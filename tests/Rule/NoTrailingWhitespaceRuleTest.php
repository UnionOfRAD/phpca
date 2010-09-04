<?php
/**
 * Copyright (c) 2009 Stefan Priebsch <stefan@priebsch.de>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the name of Stefan Priebsch nor the names of contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER ORCONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPca
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 * @license    BSD License
 */

namespace spriebsch\PHPca\Rule;

require_once __DIR__ . '/../../src/Rule/NoTrailingWhitespaceRule.php';

/**
 * Tests for the no trailing whitespace rule.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class NoTrailingWhitespaceRuleTest extends AbstractRuleTest
{
    /**
     * @covers \spriebsch\PHPca\Rule\NoTrailingWhitespaceRule
     */
    public function testDetectsTrailingWhitespace()
    {
        $this->init(__DIR__ . '/../_testdata/trailing_whitespace.php');

        $rule = new NoTrailingWhitespaceRule();
        $rule->check($this->file, $this->result);

        $this->assertTrue($this->result->hasViolations());
        
        $this->assertEquals(8, $this->result->getNumberOfViolations());

        $violations = $this->result->getViolations('test.php');

        $this->assertEquals(1, $violations[0]->getLine());
        $this->assertEquals(3, $violations[1]->getLine());
        $this->assertEquals(7, $violations[2]->getLine());
        $this->assertEquals(8, $violations[3]->getLine());
        $this->assertEquals(10, $violations[4]->getLine());
        $this->assertEquals(16, $violations[5]->getLine());
        $this->assertEquals(18, $violations[6]->getLine());
        $this->assertEquals(20, $violations[7]->getLine());
    }
}
?>