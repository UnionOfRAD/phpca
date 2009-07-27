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

require_once 'PHPUnit/Framework.php';
require_once __DIR__ . '/../src/Exceptions.php';
require_once __DIR__ . '/../src/Loader.php';

/**
* Tests for the Result class.
*
* @author Stefan Priebsch <stefan@priebsch.de>
* @copyright Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
*/
class LinterTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        Loader::init();
        Loader::registerPath(__DIR__ . '/../src');
    }

    protected function tearDown()
    {
        Loader::reset();
    }

    /**
     * @covers spriebsch\PHPca\Linter::__construct
     * @covers spriebsch\PHPca\Linter::init
     * @expectedException spriebsch\PHPca\LinterException
     */
    public function testThrowsExceptionWhenBinaryDoesNotExist()
    {
        $linter = new Linter('/path/to/nonexisting/binary');
    }

    /**
     * @covers spriebsch\PHPca\Linter::__construct
     * @covers spriebsch\PHPca\Linter::init
     * @expectedException spriebsch\PHPca\LinterException
     */
    public function testThrowsExceptionWhenBinaryIsNotExecutable()
    {
        $linter = new Linter(__DIR__ . '/_testdata/Linter/not_executable');
    }

    /**
     * This test assumes that the which command is available.
     *
     * @covers spriebsch\PHPca\Linter::__construct
     * @covers spriebsch\PHPca\Linter::init
     * @expectedException spriebsch\PHPca\LinterException
     */
    public function testThrowsExceptionWhenBinaryIsNoPhpExecutable()
    {
        $php = trim(exec('which which'));
        $linter = new Linter($php);
    }

    /**
     * This test assumes that the PHP binary can be found by running "which php".
     *
     * @covers spriebsch\PHPca\Linter::runLintCheck
     * @expectedException spriebsch\PHPca\LinterException
     */
    public function testRunLintCheckThrowsExceptionWhenFileNotFound()
    {
        $php = trim(exec('which php'));
        $linter = new Linter($php);

        $this->assertTrue($linter->runLintCheck('nonexisting_file'));
    }

    /**
     * This test assumes that the PHP binary can be found by running "which php".
     *
     * @covers spriebsch\PHPca\Linter::runLintCheck
     */
    public function testRunLintCheckReturnsTrueWhenSuccessful()
    {
        $php = trim(exec('which php'));
        $linter = new Linter($php);

        $this->assertTrue($linter->runLintCheck(__DIR__ . '/_testdata/Linter/pass.php'));
    }

    /**
     * This test assumes that the PHP binary can be found by running "which php".
     *
     * @covers spriebsch\PHPca\Linter::runLintCheck
     */
    public function testLintReturnsFalseOnError()
    {
        $php = trim(exec('which php'));
        $linter = new Linter($php);

        $this->assertFalse($linter->runLintCheck(__DIR__ . '/_testdata/Linter/fail.php'));
    }

    /**
     * This test assumes that the PHP binary can be found by running "which php".
     *
     * @covers spriebsch\PHPca\Linter::getOutput
     */
    public function testGetErrorMessagesReturnsLintErrors()
    {
        $php = trim(exec('which php'));
        $linter = new Linter($php);
        $linter->runLintCheck(__DIR__ . '/_testdata/Linter/fail.php');

        $this->assertContains('Parse error', $linter->getErrorMessages());
    }
}
?>