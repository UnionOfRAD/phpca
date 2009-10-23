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

namespace spriebsch\PHPca;

use spriebsch\PHPca\Pattern\Pattern;
use spriebsch\PHPca\Pattern\Token;

/**
 * Tests for the Finder class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class FinderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        Constants::init();
    }

    /**
     * @covers spriebsch\PHPca\Finder::findToken
     */
    public function testFindTokenReturnsEmptyArrayWhenTokenNotFound()
    {
        $file = Tokenizer::tokenize('filename', "<?php \n\n function hello()\n{\n    print 'hello world';\n} \n ?>");

        $result = Finder::findToken($file, T_CLASS);

        $this->assertEquals(0, sizeof($result));
    }

    /**
     * @covers spriebsch\PHPca\Finder::findToken
     */
    public function testFindToken()
    {
        $file = Tokenizer::tokenize('filename', "<?php \n\n function hello()\n{\n    print 'hello world';\n} \n ?>");

        $result = Finder::findToken($file, T_FUNCTION);

        $this->assertEquals(1, sizeof($result));
        $this->assertEquals('T_FUNCTION', $result[0]->getName());
    }

    /**
     * @covers spriebsch\PHPca\Finder::containsToken
     */
    public function testContainsReturnsTrueWhenTokenIsFound()
    {
        $file = Tokenizer::tokenize('filename', "<?php \n\n function hello()\n{\n    print 'hello world';\n} \n ?>");

        $this->assertTrue(Finder::containsToken($file, T_FUNCTION));
    }

    /**
     * @covers spriebsch\PHPca\Finder::containsToken
     */
    public function testContainsReturnsFalseWhenTokenIsNotFound()
    {
        $file = Tokenizer::tokenize('filename', "<?php \n\n function hello()\n{\n    print 'hello world';\n} \n ?>");

        $this->assertFalse(Finder::containsToken($file, T_CLASS));
    }

    /**
     * @covers spriebsch\PHPca\Finder::findPattern
     * @expectedException spriebsch\PHPca\EmptyPatternException
     */
    public function testFindPatternThrowsExceptionOnEmptyPattern()
    {
        $file = Tokenizer::tokenize('filename', "<?php \n\n function hello()\n{\n    print 'hello world';\n} \n ?>");
        $pattern = new Pattern();
        $result = Finder::findPattern($file, $pattern);
    }

    /**
     * @covers spriebsch\PHPca\Finder::findPattern
     */
    public function testFindPatternFindsSingleToken()
    {
        $file = Tokenizer::tokenize('filename', "<?php \n\n function hello()\n{\n    print 'hello world';\n} \n ?>");

        $pattern = new Pattern();
        $pattern->token(T_FUNCTION);

        $result = Finder::findPattern($file, $pattern);

        // Since there is only one match, the result array must contain one element
        $this->assertEquals(1, sizeof($result));

        // Since we've matched for one token, the match must contain one element
        $this->assertEquals(1, sizeof($result[0]));

        // The first element must be T_FUNCTION
        $this->assertEquals('T_FUNCTION', $result[0][0]->getName());
    }

    /**
     * @covers spriebsch\PHPca\Finder::findPattern
     */
    public function testFindPatternFindsChainedTokens()
    {
        $file = Tokenizer::tokenize('filename', "<?php \n\n function hello()\n{\n    print 'hello world';\n} \n ?>");

        $pattern = new Pattern();
        $pattern->token(T_FUNCTION)
                ->token(T_WHITESPACE)
                ->token(T_STRING);

        $result = Finder::findPattern($file, $pattern);

        // Since there is only one match, the result array must contain one element
        $this->assertEquals(1, sizeof($result));

        // Since we've matched for three tokens, the match must contain three elements
        $this->assertEquals(3, sizeof($result[0]));

        // The first element must be T_FUNCTION
        $this->assertEquals('T_FUNCTION', $result[0][0]->getName());

        // The second element must be T_WHITESPACE
        $this->assertEquals('T_WHITESPACE', $result[0][1]->getName());

        // The third element must be T_STRING
        $this->assertEquals('T_STRING', $result[0][2]->getName());
    }

    /**
     * @covers spriebsch\PHPca\Finder::findPattern
     */
    public function testFindPatternWithZeroOrMore()
    {
        $file = Tokenizer::tokenize('filename', "<?php \n\n function hello(\$a, \$b)\n{\n    print 'hello world';\n} \n ?>");

        $pattern = new Pattern();
        $pattern->token(T_FUNCTION)
                ->token(T_WHITESPACE)
                ->token(T_STRING)
                ->token(T_OPEN_BRACKET)
                ->zeroOrMore(new Token(T_ANY))
                ->token(T_CLOSE_BRACKET);

        $result = Finder::findPattern($file, $pattern);

        // Since there is only one match, the result array must contain one element
        $this->assertEquals(1, sizeof($result));

        // The sequence also contains the tokens for "$a, $b"
        $this->assertEquals(9, sizeof($result[0]));

        // The first element must be T_FUNCTION
        $this->assertEquals('T_FUNCTION', $result[0][0]->getName());

        // The last element must be T_CLOSE_BRACKET
        $this->assertEquals('T_CLOSE_BRACKET', $result[0][8]->getName());
    }

    /**
     * @covers spriebsch\PHPca\Finder::findPattern
     */
    public function testFindPatternWithOneOrMore()
    {
        $file = Tokenizer::tokenize('filename', "<?php \n\n function hello(\$a, \$b)\n{\n    print 'hello world';\n} \n ?>");

        $pattern = new Pattern();
        $pattern->token(T_OPEN_BRACKET)
                ->token(T_VARIABLE)
                ->oneOrMore(new Token(T_ANY))
                ->token(T_CLOSE_BRACKET);

        $result = Finder::findPattern($file, $pattern);

        // Since there is only one match, the result array must contain one element
        $this->assertEquals(1, sizeof($result));

        $this->assertEquals(6, sizeof($result[0]));

        // The first element must be T_OPEN_BRACKET
        $this->assertEquals('T_OPEN_BRACKET', $result[0][0]->getName());

        // The last element must be T_CLOSE_BRACKET
        $this->assertEquals('T_CLOSE_BRACKET', $result[0][5]->getName());
    }

    /**
     * @covers spriebsch\PHPca\Finder::findPattern
     */
    public function testFindPatternWithOneOf()
    {
        $file = Tokenizer::tokenize('filename', "<?php \n\n class Test { public function hello(\$a, \$b)\n{\n    print 'hello world';\n}\n} \n ?>");

        $pattern = new Pattern();
        $pattern->oneOf(array(new Token(T_PUBLIC), new Token(T_PROTECTED), new Token(T_PRIVATE)))
                ->token(T_WHITESPACE)
                ->token(T_FUNCTION);

        $result = Finder::findPattern($file, $pattern);

        // Since there is only one match, the result array must contain one element
        $this->assertEquals(1, sizeof($result));

        $this->assertEquals(3, sizeof($result[0]));

        // The first element must be T_PUBLIC
        $this->assertEquals('T_PUBLIC', $result[0][0]->getName());

        // The last element must be T_FUNCTION
        $this->assertEquals('T_FUNCTION', $result[0][2]->getName());
    }

    /**
     * @covers spriebsch\PHPca\Finder::findPattern
     */
    public function testFindPatternWithTwoMatches()
    {
        $file = Tokenizer::tokenize('filename', "<?php \n\n class Test { public function hello(\$a, \$b) {}\n protected function sayHello(\$a, \$b) {}} \n ?>");

        $pattern = new Pattern();
        $pattern->oneOf(array(new Token(T_PUBLIC), new Token(T_PROTECTED), new Token(T_PRIVATE)))
                ->token(T_WHITESPACE)
                ->token(T_FUNCTION);

        $result = Finder::findPattern($file, $pattern);

        // Since there are two matches, the result array must contain two elements
        $this->assertEquals(2, sizeof($result));

        // First result must have three elements
        $this->assertEquals(3, sizeof($result[0]));

        // The first element of the first result must be T_PUBLIC
        $this->assertEquals('T_PUBLIC', $result[0][0]->getName());

        // The last element of the first result must be T_FUNCTION
        $this->assertEquals('T_FUNCTION', $result[0][2]->getName());

        // Second result must have three elements
        $this->assertEquals(3, sizeof($result[1]));

        // The first element of the second result must be T_PROTECTED
        $this->assertEquals('T_PROTECTED', $result[1][0]->getName());

        // The last element of the second result must be T_FUNCTION
        $this->assertEquals('T_FUNCTION', $result[1][2]->getName());
    }

    /**
     * Find all T_FUNCTION tokens in the file and make sure they have correct
     * line/column positions.
     *
     * @covers spriebsch\PHPca\Finder::findPattern
     */
    public function testBug0002()
    {
        $file = Tokenizer::tokenize('filename', file_get_contents(__DIR__ . '/_testdata/Finder/bug0002.php'));

        $pattern = new Pattern();
        $pattern->token(T_FUNCTION);

        $result = Finder::findPattern($file, $pattern);

        $this->assertEquals(5, sizeof($result));

        $this->assertEquals(1, sizeof($result[0]));
        $this->assertEquals('T_FUNCTION', $result[0][0]->getName());
        $this->assertEquals(8, $result[0][0]->getLine());
        $this->assertEquals(12, $result[0][0]->getColumn());

        $this->assertEquals(1, sizeof($result[1]));
        $this->assertEquals('T_FUNCTION', $result[1][0]->getName());
        $this->assertEquals(12, $result[1][0]->getLine());
        $this->assertEquals(19, $result[1][0]->getColumn());

        $this->assertEquals(1, sizeof($result[2]));
        $this->assertEquals('T_FUNCTION', $result[2][0]->getName());
        $this->assertEquals(16, $result[2][0]->getLine());
        $this->assertEquals(21, $result[2][0]->getColumn());

        $this->assertEquals(1, sizeof($result[3]));
        $this->assertEquals('T_FUNCTION', $result[3][0]->getName());
        $this->assertEquals(22, $result[3][0]->getLine());
        $this->assertEquals(1, $result[3][0]->getColumn());

        $this->assertEquals(1, sizeof($result[4]));
        $this->assertEquals('T_FUNCTION', $result[4][0]->getName());
        $this->assertEquals(26, $result[4][0]->getLine());
        $this->assertEquals(1, $result[4][0]->getColumn());
    }
}
?>