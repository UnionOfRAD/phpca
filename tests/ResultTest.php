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

/**
* Tests for the Result class.
*
* @author Stefan Priebsch <stefan@priebsch.de>
* @copyright Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
*/
class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers spriebsch\PHPca\Result::getFiles
     */
    public function testGetFiles()
    {
        $result = new Result();
        $result->addFile('testfile');

        $this->assertEquals(array('testfile'), $result->getFiles());
    }

    /**
     * @covers spriebsch\PHPca\Result::getNumberOfFiles
     */
    public function testGetNumberOfFiles()
    {
        $result = new Result();

        $this->assertEquals(0, $result->getNumberOfFiles());

        $result->addFile('testfile');

        $this->assertEquals(1, $result->getNumberOfFiles());
    }

    /**
     * @covers spriebsch\PHPca\Result::addMessage
     * @covers spriebsch\PHPca\Result::hasViolations
     */
    public function testInitiallyHasNoViolations()
    {
        $result = new Result();
        $result->addFile('testfile');

        $this->assertFalse($result->hasViolations());
    }

    /**
     * @covers spriebsch\PHPca\Result::addMessage
     * @covers spriebsch\PHPca\Result::hasViolations
     */
    public function testHasViolations()
    {
        $result = new Result();
        $result->addFile('testfile');

        $result->addMessage(new Violation('testfile', 'error message'));
        $result->addMessage(new Violation('testfile', 'another error message'));

        $this->assertTrue($result->hasViolations());
    }

    /**
     * @covers spriebsch\PHPca\Result::hasViolations
     */
    public function testHasNoViolationsForFile()
    {
        $result = new Result();
        $result->addFile('testfile');

        $this->assertFalse($result->hasViolations('testfile'));
    }

    /**
     * @covers spriebsch\PHPca\Result::hasViolations
     */
    public function testHasViolationsForFile()
    {
        $result = new Result();
        $result->addFile('testfile');

        $result->addMessage(new Violation('testfile', 'error message'));

        $this->assertTrue($result->hasViolations('testfile'));
    }

    /**
     * @covers spriebsch\PHPca\Result::getViolations
     */
    public function testGetViolations()
    {
        $result = new Result();
        $result->addFile('testfile');

        $this->assertEquals(array(), $result->getViolations('testfile'));

        $t1 = new Token(T_OPEN_TAG, '<?php', 5, 4);
        $violation1 = new Violation('testfile', 'error message', $t1);
        $t2 = new Token(T_OPEN_TAG, '<?php', 5, 9);
        $violation2 = new Violation('testfile', 'another error message', $t2);

        $result->addMessage($violation1);
        $result->addMessage($violation2);

        $this->assertContains($violation1, $result->getViolations('testfile'));
        $this->assertContains($violation2, $result->getViolations('testfile'));
    }

    /**
     * @covers spriebsch\PHPca\Result::getViolations
     */
    public function testGetViolationsInitiallyReturnsEmptyArray()
    {
        $result = new Result();
        $result->addFile('testfile');

        $this->assertEquals(array(), $result->getViolations('testfile'));
    }

    /**
     * @covers spriebsch\PHPca\Result::getViolations
     * @covers spriebsch\PHPca\Result::sortByLine
     */
    public function testGetViolationsSortsByLine()
    {
        $result = new Result();
        $result->addFile('testfile');

        $t1 = new Token(T_OPEN_TAG, '<?php', 5);
        $violation1 = new Violation('testfile', 'error message', $t1);
        $result->addMessage($violation1);
        
        $t2 = new Token(T_OPEN_TAG, '<?php', 3);
        $violation2 = new Violation('testfile', 'another error message', $t2);
        $result->addMessage($violation2);

        $this->assertEquals(array($violation2, $violation1), $result->getViolations('testfile'));
    }

    /**
     * @covers spriebsch\PHPca\Result::getViolations
     * @covers spriebsch\PHPca\Result::sortByLine
     */
    public function testGetViolationsSortsByColumn()
    {
        $result = new Result();
        $result->addFile('testfile');

        $this->assertEquals(array(), $result->getViolations('testfile'));

        $t1 = new Token(T_OPEN_TAG, '<?php', 5, 9);
        $violation1 = new Violation('testfile', 'error message', $t1);
        $result->addMessage($violation1);
        
        $t2 = new Token(T_OPEN_TAG, '<?php', 5, 4);
        $violation2 = new Violation('testfile', 'another error message', $t2);
        $result->addMessage($violation2);

        $this->assertEquals(array($violation2, $violation1), $result->getViolations('testfile'));
    }

    /**
     * @covers spriebsch\PHPca\Result::hasLintError
     */
    public function testHasLintErrorReturnsFalseWhenNoLintError()
    {
        $result = new Result();
        $result->addFile('testfile');

        $this->assertFalse($result->hasLintError('testfile'));
    }

    /**
     * @covers spriebsch\PHPca\Result::hasLintError
     */
    public function testHasLintErrorReturnsTrueForLintError()
    {
        $result = new Result();
        $result->addFile('testfile');

        $error = new LintError('testfile', 'error message');
        $result->addMessage($error);

        $this->assertTrue($result->hasLintError('testfile'));
    }

    /**
     * @covers spriebsch\PHPca\Result::hasLintError
     */
    public function testHasLintErrorReturnsFalseForOtherError()
    {
        $result = new Result();
        $result->addFile('testfile');

        $error = new Violation('testfile', 'error message');
        $result->addMessage($error);

        $this->assertFalse($result->hasLintError('testfile'));
    }

    /**
     * @covers spriebsch\PHPca\Result::hasRuleError
     */
    public function testHasRuleErrorReturnsFalseWhenNoRuleError()
    {
        $result = new Result();
        $result->addFile('testfile');

        $this->assertFalse($result->hasRuleError('testfile'));
    }

    /**
     * @covers spriebsch\PHPca\Result::hasRuleError
     */
    public function testHasRuleErrorReturnsTrueForRuleError()
    {
        $result = new Result();
        $result->addFile('testfile');

        $error = new RuleError('testfile', 'error message');
        $result->addMessage($error);

        $this->assertTrue($result->hasRuleError('testfile'));
    }

    /**
     * @covers spriebsch\PHPca\Result::hasRuleError
     */
    public function testHasRuleErrorReturnsFalseForOtherError()
    {
        $result = new Result();
        $result->addFile('testfile');

        $error = new Violation('testfile', 'error message');
        $result->addMessage($error);

        $this->assertFalse($result->hasRuleError('testfile'));
    }

    /**
     * @covers spriebsch\PHPca\Result::getNumberOfViolations
     */
    public function testGetNumberOfViolationsInitiallyReturnsZero()
    {
        $result = new Result();
        $result->addFile('testfile');

        $this->assertEquals(0, $result->getNumberOfViolations());
    }

    /**
     * @covers spriebsch\PHPca\Result::addMessage
     * @covers spriebsch\PHPca\Result::getNumberOfViolations
     */
    public function testGetNumberOfViolationsReturnsViolationCount()
    {
        $result = new Result();
        $result->addFile('testfile');

        $result->addMessage(new Violation('testfile', 'error message'));
        $result->addMessage(new Violation('testfile', 'another error message'));

        $this->assertEquals(2, $result->getNumberOfViolations());
    }

    /**
     * @covers spriebsch\PHPca\Result::getNumberOfLintErrors
     */
    public function testGetNumberOfLintErrorsInitiallyReturnsZero()
    {
        $result = new Result();
        $result->addFile('testfile');

        $this->assertEquals(0, $result->getNumberOfLintErrors('testfile'));
    }

    /**
     * @covers spriebsch\PHPca\Result::addMessage
     * @covers spriebsch\PHPca\Result::getNumberOfLintErrors
     */
    public function testGetNumberOfLintErrors()
    {
        $result = new Result();
        $result->addFile('testfile');

        $result->addMessage(new LintError('testfile', 'error message'));

        $this->assertEquals(1, $result->getNumberOfLintErrors('testfile'));
    }

    /**
     * @covers spriebsch\PHPca\Result::getNumberOfRuleErrors
     */
    public function testGetNumberOfRuleErrorsInitiallyReturnsZero()
    {
        $result = new Result();
        $result->addFile('testfile');

        $this->assertEquals(0, $result->getNumberOfRuleErrors('testfile'));
    }

    /**
     * @covers spriebsch\PHPca\Result::addMessage
     * @covers spriebsch\PHPca\Result::getNumberOfRuleErrors
     */
    public function testGetNumberOfRuleErrors()
    {
        $result = new Result();
        $result->addFile('testfile');

        $error = new RuleError('testfile', 'error message');
        $result->addMessage($error);

        $this->assertEquals(1, $result->getNumberOfRuleErrors('testfile'));
    }
}
?>