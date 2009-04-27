<?php
/**
 * This file is part of phpca, the static code analyzer for PHP.
 *
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

namespace spriebsch\PHPca\Tests;

use spriebsch\PHPca\NoVarKeyword as NoVarKeyword;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'RuleTestCase.php';
require_once implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', '..' , 'Rules', 'NoVarKeyword.php'));

/**
 * Tests for the No Var Keyword Rule.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class NoVarKeywordTest extends RuleTestCase
{
    protected function setUp()
    {
        $this->rule = new NoVarKeyword();
        parent::setUp();
    }

    public function test001()
    {
        $this->fileName = implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', '_files', 'testdata', 'no_var_keyword', '001.php'));
        $this->tokenize();

        $this->assertEquals(1, $this->result->getNumberOfErrors());
        $this->assertHasErrorOnLine(7);
    }

    public function test002()
    {
        $this->fileName = implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', '_files', 'testdata', 'no_var_keyword', '002.php'));
        $this->tokenize();

        $this->assertEquals(0, $this->result->getNumberOfErrors());
    }
}
?>
