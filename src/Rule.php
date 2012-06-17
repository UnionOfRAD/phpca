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

use spriebsch\PHPca\File;
use spriebsch\PHPca\Result;
use spriebsch\PHPca\Message;
use spriebsch\PHPca\Violation;
use spriebsch\PHPca\Configuration;

/**
 * Base class for a Rule that is enforced on a token stream.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
abstract class Rule
{
    protected $settings;
    protected $file;
    protected $result;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * Checks whether a configuration setting exists
     *
     * @param mixed $setting
     */
    protected function hasSetting($setting)
    {
        return isset($this->settings[$setting]);
    }

    /**
     * Returns a configuration setting
     *
     * @param mixed $setting
     */
    protected function getSetting($setting)
    {
        if (!isset($this->settings[$setting])) {
            throw new \Exception('Configuration setting ' . $setting . ' does not exist');
        }

        return $this->settings[$setting];
    }

    /**
     * Checks whether this rule is disabled.
     *
     * @return bool
     */
    protected function isDisabled()
    {
        if (!$this->hasSetting('disable')) {
            return false;
        }

        return (bool) $this->getSetting('disable');
    }

    /**
     * Checks whether this rule should be skipped
     *
     * @return bool
     */
    protected function skip()
    {
        if (!$this->hasSetting('skip')) {
            return false;
        }

        if (in_array($this->file->getFileName(), $this->getSetting('skip'))) {
            return true;
        }

        return false;
    }

    /**
     * Checks the rule.
     * Returns false when the rule has been disabled or skipped.
     *
     * @param File   $file   Tokenized file to apply rule to
     * @param Result $result Result object
     * @return bool
     */
    public function check(File $file, Result $result)
    {
        $this->file = $file;

        if ($this->isDisabled()) {
            return false;
        }

        if ($this->skip()) {
            return false;
        }

        $this->result = $result;
        $this->file->rewind();

        $this->doCheck();

        return true;
    }

    /**
     * Set the configuration
     *
     * @param Configuration $configuration
     * @return null
     * @todo should replace configure()
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Configure the rule.
     *
     * @param array $settings
     * @return null
     */
    public function configure(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Add a rule violation.
     *
     * @param string $message
     * @param mixed $tokens
     * @param int $line
     * @param int $column
     * @return null
     */
    protected function addViolation($message, $tokens, $line = null, $column = null)
    {
        if (!is_array($tokens)) {
            $tokens = array($tokens);
        }

        foreach ($tokens as $token) {
            $violation = new Violation($this->file->getFileName(), $message, $token, $line, $column);
            $violation->setRule($this);
            $this->result->addMessage($violation);
        }
    }

    /**
     * Performs the rule check.
     *
     * @return null
     */
    abstract protected function doCheck();
}
?>