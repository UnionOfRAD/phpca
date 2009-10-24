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

require_once __DIR__ . '/../../src/Rule/KeywordsAreLowercaseRule.php';

/**
 * Tests for the keywords are lowercase rule.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class KeywordsAreLowercaseRuleTest extends AbstractRuleTest
{
    /**
     * @covers \spriebsch\PHPca\Rule\KeywordsAreLowercaseRule
     */
    public function testDetectsNonLowercaseKeywords()
    {
        $this->init(__DIR__ . '/../_testdata/non_lowercase.php');

        $rule = new KeywordsAreLowercaseRule();
        $rule->check($this->file, $this->result);        
        
        $this->assertTrue($this->result->hasViolations());
        $this->assertEquals(7, $this->result->getNumberOfViolations());
        
        $violations = $this->result->getViolations('test.php');

        // Function
        $this->assertEquals(11, $violations[0]->getLine());
        
        // CLASS
        $this->assertEquals(15, $violations[1]->getLine());
        
        // Public
        $this->assertEquals(17, $violations[2]->getLine());
        
        // Protected
        $this->assertEquals(21, $violations[3]->getLine());
        
        // Private
        $this->assertEquals(25, $violations[4]->getLine());
        
        // interFace
        $this->assertEquals(30, $violations[5]->getLine());

        // Array
        $this->assertEquals(34, $violations[6]->getLine());
    }
}
?>