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

use spriebsch\PHPca\Loader;
use spriebsch\PHPca\Constants;
use spriebsch\PHPca\Tokenizer;
use spriebsch\PHPca\Result;

require_once 'PHPUnit/Framework.php';
require_once __DIR__ . '/RuleTest.php';
require_once __DIR__ . '/../../src/Exceptions.php';
require_once __DIR__ . '/../../src/Loader.php';

/**
 * Tests for the No tabulators rule.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class NoTabulatorsRuleTest extends RuleTest
{
    /**
     * @covers \spriebsch\PHPca\Rule\NoTabulatorsRule
     */
    public function testNoTabulators()
    {
        $this->init(__DIR__ . '/../_testdata/NoTabulatorsRule/blanks.php');

        $rule = new NoTabulatorsRule();
        $rule->check($this->file, $this->result);

        $this->assertFalse($this->result->hasWarnings());
        $this->assertFalse($this->result->hasErrors());
    }

    /**
     * @covers \spriebsch\PHPca\Rule\NoTabulatorsRule
     */
    public function testTabulators()
    {
        $this->init(__DIR__ . '/../_testdata/NoTabulatorsRule/tabulators.php');

        $rule = new NoTabulatorsRule();
        $rule->check($this->file, $this->result);

        $this->assertFalse($this->result->hasWarnings());
        $this->assertEquals(6, $this->result->getNumberOfErrors());

        $errors = $this->result->getErrors('test.php');

        $this->assertEquals(5, $errors[0]->getLine());
        $this->assertEquals(1, $errors[0]->getColumn());

        $this->assertEquals(6, $errors[1]->getLine());
        $this->assertEquals(1, $errors[1]->getColumn());

        $this->assertEquals(14, $errors[2]->getLine());
        $this->assertEquals(1, $errors[2]->getColumn());

        $this->assertEquals(15, $errors[3]->getLine());
        $this->assertEquals(1, $errors[3]->getColumn());

        $this->assertEquals(16, $errors[4]->getLine());
        $this->assertEquals(1, $errors[4]->getColumn());

        $this->assertEquals(17, $errors[5]->getLine());
        $this->assertEquals(1, $errors[5]->getColumn());
    }
}
?>