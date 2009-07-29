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
 * Tests for the Application class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        Loader::init();
        Loader::registerPath(__DIR__ . '/../src');

        $this->application = new Application();
        $this->application->setEnableBuiltInRules(false);
    }

    protected function tearDown()
    {
        Loader::reset();
    }

    /**
     * @covers spriebsch\PHPca\Application::getNumberOfFiles
     */
    public function testGetNumberOfFilesIsInitiallyZero()
    {
        $application = new Application();
        $this->assertEquals(0, $application->getNumberOfFiles());
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
        $application = new Application();
        $application->run('', '');
    }

    /**
     * Does not specify a file or directory to analyze.
     *
     * @covers spriebsch\PHPca\Application::run
     * @expectedException spriebsch\PHPca\Exception
     */
    public function testRunThrowsExceptionWhenNoPathToAnalyzeIsGiven()
    {
        $this->application->run('php', '');
    }

    /**
     * Calls setEnableBuiltInRules with a non-boolean parameter.
     *
     * @covers spriebsch\PHPca\Application::enableBuiltInRules
     * @expectedException spriebsch\PHPca\Exception
     */
    public function testSetEnableBuiltInRulesThrowsExceptionWhenParameterIsNotBoolean()
    {
        $this->application->setEnableBuiltInRules('nonsense');
    }

    /**
     * Disables the built-in rules and do not specify any
     * additional rule directories, thus there are no rules to enforce.
     *
     * @covers spriebsch\PHPca\Application::run
     * @covers spriebsch\PHPca\Application::enableBuiltInRules
     * @expectedException spriebsch\PHPca\Exception
     */
    public function testRunThrowsExceptionWhenNoRulesToEnforce()
    {
        $this->application->run(trim(exec('which php')), __DIR__ . '/_testdata/Application');
    }

    /**
     * Specifies a directory that contains no PHP files.
     *
     * @covers spriebsch\PHPca\Application::run
     * @expectedException spriebsch\PHPca\Exception
     */
    public function testRunThrowsExceptionWhenNoFilesToAnalyze()
    {
        $this->application->run(trim(exec('which php')), __DIR__ . '/_testdata/Application/none');
    }

    /**
     * Specifies a non-existing file/directory to analyze.
     *
     * @covers spriebsch\PHPca\Application::run
     * @expectedException spriebsch\PHPca\Exception
     */
    public function testRunThrowsExceptionWhenDirectoryDoesNotExist()
    {
        $this->application->run(trim(exec('which php')), '/nonsense');
    }

    /**
     * Analyzes a file with a lint error.
     *
     * @covers spriebsch\PHPca\Application::run
     */
    public function testRunCreatesErrorOnLintFail()
    {
        $result = $this->application->run(trim(exec('which php')), __DIR__ . '/_testdata/Application/lint_fail');

        $this->assertTrue($result->hasLintError(__DIR__ . '/_testdata/Application/lint_fail/fail.php'));
    }

    /**
     * Calls run() with a single file instead of a directory.
     *
     * @covers spriebsch\PHPca\Application::run
     */
    public function testRunAcceptsSingleFile()
    {
        $this->application->addRulePath(__DIR__ . '/_testdata/Application/_rules/pass');

        $result = $this->application->run(trim(exec('which php')), __DIR__ . '/_testdata/Application/pass/pass.php');

        $this->assertFalse($result->hasErrors());
    }

    /**
     * Registers a progress printer to make sure that it gets called
     * after processing a file.
     *
     * @covers spriebsch\PHPca\Application::run
     */
    public function testRunNotifiesProgressPrinter()
    {
        $this->application->addRulePath(__DIR__ . '/_testdata/Application/_rules/pass');

        $mock = $this->getMock('spriebsch\PHPca\ProgressPrinterInterface', array('showProgress'));
        $mock->expects($this->once())->method('showProgress');

        $this->application->registerProgressPrinter($mock);

        $result = $this->application->run(trim(exec('which php')), __DIR__ . '/_testdata/Application/pass');
    }

    /**
     * Calls run() with a single file instead of a directory.
     * This test depends on the CloseTagAtEndRule and might fail if rules
     * are added that fail on _testdata/Application/fail/fail.php
     * or when the error message of CloseTagAtEndRule is modified.
     *
     * @covers spriebsch\PHPca\Application::run
     */
    public function testRunEnforcesBuiltInRules()
    {
        $this->application->setEnableBuiltInRules(true);

        $result = $this->application->run(trim(exec('which php')), __DIR__ . '/_testdata/Application/fail/fail.php');
        $errors = $result->getErrors(__DIR__ . '/_testdata/Application/fail/fail.php');

        $this->assertContains('File does not end with PHP close tag', $errors[0]->getMessage());
    }

    /**
     * Calls addRulePath with a non-existing path.
     *
     * @covers spriebsch\PHPca\Application::addRulePath
     * @expectedException spriebsch\PHPca\Exception
     */
    public function testAddRulePathThrowsExceptionWhenPathDoesNotExist()
    {
        $this->application->addRulePath('does/not/exist');
    }

    /**
     * Calls addRulePath twíce with the same path,
     * and makes sure that the path is only added once.
     *
     * @covers spriebsch\PHPca\Application::addRulePath
     * @covers spriebsch\PHPca\Application::getRulePaths
     */
    public function testAddRulePathAddsPathOnlyOnce()
    {
        $this->application->addRulePath(__DIR__ . '/_testdata/Application/_rules');
        $this->application->addRulePath(__DIR__ . '/_testdata/Application/_rules');

        $this->assertEquals(array(__DIR__ . '/_testdata/Application/_rules'), $this->application->getRulePaths());
    }

    /**
     * Registers ErrorTestRule and PassTestRule, but then explicitly disables
     * ErrorTestRule so that no error must occur.
     *
     * @covers spriebsch\PHPca\Application::disableRule
     * @covers spriebsch\PHPca\Application::enforceRules
     */
    public function testDisableRuleDisablesGivenRule()
    {
        $this->application->addRulePath(__DIR__ . '/_testdata/Application/_rules/pass');
        $this->application->addRulePath(__DIR__ . '/_testdata/Application/_rules/error');
        $this->application->disableRule('\\spriebsch\\PHPca\\Rule\\ErrorTestRule');

        $result = $this->application->run(trim(exec('which php')), __DIR__ . '/_testdata/Application/pass/pass.php');

        $this->assertFalse($result->hasErrors());
    }

    /**
     * Disables CloseTagAtEndRule rule, then analyzes fail.php that has
     * no close tag at the end. If the rule has been disabled, no error
     * may occur. Also see testRunLoadsBuiltInRules().
     *
     * @covers spriebsch\PHPca\Application::disableRule
     * @covers spriebsch\PHPca\Application::enforceRules
     */
    public function testDisableRuleDisablesBuiltInRule()
    {
        $this->application->setEnableBuiltInRules(true);
        $this->application->disableRule('\\spriebsch\\PHPca\\Rule\\CloseTagAtEndRule');

        $result = $this->application->run(trim(exec('which php')), __DIR__ . '/_testdata/Application/fail/fail.php');

        $this->assertFalse($result->hasErrors());
    }

    /**
     * Registes a rule that always throws an exception to make
     * sure that a RuleError is generated.
     *
     * @covers spriebsch\PHPca\Application::enforceRules
     */
    public function testRunGeneratesRuleErrorOnExceptionInRule()
    {
        $this->application->addRulePath(__DIR__ . '/_testdata/Application/_rules/fail');

        $result = $this->application->run(trim(exec('which php')), __DIR__ . '/_testdata/Application/pass/pass.php');
        $errors = $result->getErrors(__DIR__ . '/_testdata/Application/pass/pass.php');

        $this->assertTrue($result->hasErrors());
        $this->assertTrue($errors[0] instanceOf RuleError);
    }
}
?>