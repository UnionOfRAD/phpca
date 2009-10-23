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
 * Tests for the Application class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->configuration = new Configuration(getcwd());
        $this->application = new Application(getcwd());
    }

    /**
     * @covers spriebsch\PHPca\Application::getNumberOfFiles
     */
    public function testGetNumberOfFilesIsInitiallyZero()
    {
        $this->assertEquals(0, $this->application->getNumberOfFiles());
    }

    /**
     * Does not specify a PHP executable
     * (which is required to run the lint checks).
     *
     * @covers spriebsch\PHPca\Application::run
     * @expectedException spriebsch\PHPca\Exception
     */
    public function testRunThrowsExceptionWhenNoPhpExecutableIsGiven()
    {
        $this->configuration->setConfiguration(parse_ini_file(__DIR__ . '/_testdata/pass.ini', true));

        $this->application->run('', '', $this->configuration);
    }

    /**
     * Does not specify a parse_ini_file or directory to analyze.
     *
     * @covers spriebsch\PHPca\Application::run
     * @expectedException spriebsch\PHPca\Exception
     */
    public function testRunThrowsExceptionWhenNoPathToAnalyzeIsGiven()
    {
        $this->application->run('php', '');
    }

    /**
     * Specifies a directory that contains no PHP parse_ini_files.
     *
     * @covers spriebsch\PHPca\Application::run
     * @expectedException spriebsch\PHPca\Exception
     */
    public function testRunThrowsExceptionWhenNoFilesToAnalyze()
    {
        $this->application->run(trim(exec('which php')), __DIR__ . '/_testdata/Application/none');
    }

    /**
     * Specifies a non-existing parse_ini_file/directory to analyze.
     *
     * @covers spriebsch\PHPca\Application::run
     * @expectedException spriebsch\PHPca\Exception
     */
    public function testRunThrowsExceptionWhenDirectoryDoesNotExist()
    {
        $this->application->run(trim(exec('which php')), '/nonsense');
    }

    /**
     * Analyzes a parse_ini_file with a lint error.
     *
     * @covers spriebsch\PHPca\Application::run
     */
    public function testRunCreatesErrorOnLintFail()
    {
        $result = $this->application->run(trim(exec('which php')), __DIR__ . '/_testdata/lint_fail.php');

        $this->assertTrue($result->hasLintError(__DIR__ . '/_testdata/lint_fail.php'));
    }

    /**
     * Calls run() with a single parse_ini_file instead of a directory.
     *
     * @covers spriebsch\PHPca\Application::run
     */
    public function testRunAcceptsSingleFile()
    {
        $this->configuration->setConfiguration(parse_ini_file(__DIR__ . '/_testdata/pass.ini', true));

        $result = $this->application->run(trim(exec('which php')), __DIR__ . '/_testdata/Application/pass/pass.php', $this->configuration);

        $this->assertFalse($result->hasErrors());
    }

    /**
     * Registers a progress printer to make sure that it gets called
     * after processing a parse_ini_file.
     *
     * @covers spriebsch\PHPca\Application::run
     */
    public function testRunNotifiesProgressPrinter()
    {
        $this->configuration->setConfiguration(parse_ini_file(__DIR__ . '/_testdata/pass.ini', true));

        $mock = $this->getMock('spriebsch\PHPca\ProgressPrinterInterface', array('showProgress'));
        $mock->expects($this->once())->method('showProgress');

        $this->application->registerProgressPrinter($mock);

        $result = $this->application->run(trim(exec('which php')), __DIR__ . '/_testdata/Application/pass', $this->configuration);
    }

    /**
     * Registes a rule that always throws an exception to make
     * sure that a RuleError is generated.
     *
     * @covers spriebsch\PHPca\Application::enforceRules
     */
    public function testRunGeneratesRuleErrorOnExceptionInRule()
    {
        $this->configuration->setConfiguration(parse_ini_file(__DIR__ . '/_testdata/fail.ini', true));

        $result = $this->application->run(trim(exec('which php')), __DIR__ . '/_testdata/Application/pass/pass.php', $this->configuration);
        // $errors = $result->getViolations(__DIR__ . '/_testdata/Application/pass/pass.php');

        $this->assertTrue($result->hasRuleError(__DIR__ . '/_testdata/Application/pass/pass.php'));
    }
}
?>