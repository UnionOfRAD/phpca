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

namespace spriebsch\PHPca\Pattern;

use spriebsch\PHPca\Loader;
use spriebsch\PHPca\Constants;

/**
 * Tests for the Pattern class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class PatternTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        Constants::init();
    }

    /**
     * @covers \spriebsch\PHPca\Pattern\Pattern
     */
    public function testRegularTokenMapToRegEx()
    {
        $pattern = new Pattern();
        $pattern->token(T_WHITESPACE);

        $this->assertEquals('(\bT_WHITESPACE\b)', $pattern->getRegEx());
    }

    /**
     * @covers \spriebsch\PHPca\Pattern\Pattern
     */
    public function testAnyTokenMapToRegEx()
    {
        $pattern = new Pattern();
        $pattern->token(T_ANY);

        $this->assertEquals('(\bT_.*\b )', $pattern->getRegEx());
    }

    /**
     * @covers \spriebsch\PHPca\Pattern\Pattern
     */
    public function testTwoTokensMapToRegEx()
    {
        $pattern = new Pattern();
        $pattern->token(T_OPEN_TAG)
                ->token(T_FUNCTION);

        $this->assertEquals('(\bT_OPEN_TAG\b) (\bT_FUNCTION\b)', $pattern->getRegEx());
    }

    /**
     * @covers \spriebsch\PHPca\Pattern\Pattern::isEmpty
     */
    public function testIsEmptyReturnsTrueForEmptyPattern()
    {
        $pattern = new Pattern();

        $this->assertTrue($pattern->isEmpty());
    }

    /**
     * @covers \spriebsch\PHPca\Pattern\Pattern::isEmpty
     */
    public function testIsEmptyReturnsFalseForNonEmptyPattern()
    {
        $pattern = new Pattern();
        $pattern->add(new Pattern());

        $this->assertFalse($pattern->isEmpty());
    }

    /**
     * @covers \spriebsch\PHPca\Pattern\Pattern::oneOf
     * @expectedException \Exception
     */
    public function testOneOfThrowsExceptionWhenArrayElementIsNoPattern()
    {
        $pattern = new Pattern();
        $pattern->oneOf(array('nonsense'));
    }

    /**
     * @covers \spriebsch\PHPca\Pattern\Pattern::oneOf
     */
    public function testOneOfMapsToRegExWithoutTrailingBlank()
    {
        $pattern = new Pattern();
        $pattern->oneOf(array(new Token(T_OPEN_TAG), new Token(T_FUNCTION)));

        $this->assertEquals('((\bT_OPEN_TAG\b)|(\bT_FUNCTION\b))', $pattern->getRegEx());
    }

    /**
     * @covers \spriebsch\PHPca\Pattern\Pattern::oneOrMore
     */
    public function testOneOrMoreMapsToRegExWithoutTrailingBlank()
    {
        $pattern = new Pattern();
        $pattern->oneOrMore(new Token(T_OPEN_TAG));

        $this->assertEquals('(\bT_OPEN_TAG\b)+', $pattern->getRegEx());
    }

    /**
     * @covers \spriebsch\PHPca\Pattern\Pattern::zeroOrMore
     */
    public function testZeroOrMoreMapsToRegExWithoutTrailingBlank()
    {
        $pattern = new Pattern();
        $pattern->zeroOrMore(new Token(T_OPEN_TAG));

        $this->assertEquals('(\bT_OPEN_TAG\b)*', $pattern->getRegEx());
    }
}
?>