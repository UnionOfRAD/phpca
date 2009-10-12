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
 * Tests for the CLI class.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class CLITest extends \PHPUnit_Framework_TestCase
{
    /**
     * We test CLI through a subclass that prevents exit on errors.
     */
    protected function setUp()
    {
        Loader::init();
        Loader::registerPath(__DIR__ . '/../src');
        Loader::registerPath(__DIR__ . '/_testdata/CLI');

        $this->cli = new TestCLISubclass(getcwd());
    }

    protected function tearDown()
    {
        Loader::reset();
    }

    /**
     * Runs CLI (through a subclass that prevents exit on errors)
     * and captures its output through output buffering.
     * Returns a string with CLI's output.
     *
     * @param array $argv The command line arguments for CLI
     * @return string
     * @todo use output buffering annotation of PHPUnit?
     */
    protected function runCLI($argv)
    {
        ob_start();
        $this->cli->run($argv);
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }

    /**
     * @covers spriebsch\PHPca\CLI
     */
    public function testRunShowsErrorOnUnknownOption()
    {
        $result = $this->runCLI(array('cli.php', '-unknown'));

        $this->assertContains('Error: Unknown option', $result);
    }

    /**
     * @covers spriebsch\PHPca\CLI
     */
    public function testRunShowsNameAndVersion()
    {
        $result = $this->runCLI(array('cli.php'));

        $this->assertContains('PHP Code Analyzer', $result);
        $this->assertContains(Application::$version, $result);
    }

    /**
     * @covers spriebsch\PHPca\CLI
     */
    public function testRunShowsUsageMessageWhenInvokedWithoutArguments()
    {
        $result = $this->runCLI(array('cli.php'));

        $this->assertContains('Usage:', $result);
    }

    /**
     * @covers spriebsch\PHPca\CLI
     */
    public function testRunShowsErrorMessageWhenInvokedWithoutArguments()
    {
        $result = $this->runCLI(array('cli.php'));

        $this->assertContains('Error: No path to PHP executable', $result);
    }

    /**
     * @covers spriebsch\PHPca\CLI
     */
    public function testRunShowsUsageMessageWhenInvokedWithShortHelpSwitch()
    {
        $result = $this->runCLI(array('cli.php', '-h'));

        $this->assertContains('Usage:', $result);
    }

    /**
     * @covers spriebsch\PHPca\CLI
     */
    public function testRunShowsUsageMessageWhenInvokedWithLongHelpSwitch()
    {
        $result = $this->runCLI(array('cli.php', '--help'));

        $this->assertContains('Usage:', $result);
    }

    /**
     * @covers spriebsch\PHPca\CLI
     */
    public function testRunDisplaysErrorWhenNoFilesToAnalyze()
    {
        $result = $this->runCLI(array('cli.php', '-p', trim(exec('which php')), __DIR__ . '/_testdata/Application/none'));

        $this->assertContains('Error: No PHP files to analyze', $result);
    }

    /**
     * @covers spriebsch\PHPca\CLI
     */
    public function testRunDisplaysFailMessageWhenAnalyzingFilesWithViolations()
    {
        $result = $this->runCLI(array('cli.php', '-p', trim(exec('which php')), __DIR__ . '/_testdata/Application/fail'));

        $this->assertContains("\nF\n", $result);
        $this->assertContains('FAIL', $result);
    }

    /**
     * @covers spriebsch\PHPca\CLI
     */
    public function testRunDisplayOkMessageWhenAnalyzingFilesWithoutViolations()
    {
        $result = $this->runCLI(array('cli.php', '-p', trim(exec('which php')), __DIR__ . '/_testdata/Application/pass'));

        $this->assertContains("\n.\n", $result);
        $this->assertContains('OK', $result);
    }

    /**
     * @covers spriebsch\PHPca\CLI
     */
    public function testRunDisplayLintErrorWhenAnalyzingFilesWithLintErrors()
    {
        $result = $this->runCLI(array('cli.php', '-p', trim(exec('which php')), __DIR__ . '/_testdata/lint_fail.php'));

        $this->assertContains("\nE\n", $result);
        $this->assertContains('Parse error:', $result);
        $this->assertContains('FAIL', $result);
    }
}
?>