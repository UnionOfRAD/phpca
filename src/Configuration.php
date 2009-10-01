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
 * The PHPca configuration.
 *
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 */
class Configuration
{
    /**
     * @var array
     */
    protected $settings = array();

    /**
     * @var array
     */
    protected $ruleSettings = array();

    /**
     * @var string
     */
    protected $codingStandard;

    /**
     * @var array
     */
    protected $extensions = array('php');

    /**
     * @var array
     */
    protected $rules = array();

    /**
     * Sets the standard settings read from an ini file.
     *
     * @param array $settings
     * @return null
     */
    public function setStandard(array $settings)
    {
        if (isset($settings['PHPca'])) {
            $this->settings = $settings['PHPca'];
            unset($settings['PHPca']);
        }

        $this->ruleSettings = $settings;
    }

    /**
     * Sets the configuration settings read from an ini file.
     *
     * @param array $configuration
     * @return null
     */
    public function setConfiguration(array $configuration)
    {
        if (isset($configuration['PHPca'])) {
            $this->settings = array_merge($this->settings, $configuration['PHPca']);
            unset($configuration['PHPca']);
        }

        $this->ruleSettings = array_merge($this->ruleSettings, $configuration);
    }

    /**
     * Sets the name of the coding standard to use.
     *
     * @param string $codingStandard
     * @return null
     */
    public function setCodingStandard($codingStandard)
    {
        $this->codingStandard = $codingStandard;
    }

    /**
     * Returns the coding standard used.
     *
     * @return string
     */
    public function getCodingStandard()
    {
        return $this->codingStandard;
    }

    /**
     * Sets the file extensions to analyze.
     *
     * @param array $extensions
     * @return null
     */
    public function setExtensions(array $extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * Returns the file extensions to analyze.
     *
     * @return array
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Sets the rules to enforce.
     *
     * @param array $extensions
     * @return null
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Returns the rules to enforce.
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Checks whether settings exist for given rule.
     *
     * @param string $rule
     * @return bool
     */
    public function hasSettings($rule)
    {
        return isset($this->ruleSettings[$rule]);
    }

    /**
     * Returns all settings for given rule.
     *
     * @param string $rule
     * @return array
     */
    public function getSettings($rule)
    {
        if (!isset($this->ruleSettings[$rule])) {
            return array();
        }

        return $this->ruleSettings[$rule];
    }

    /**
     * Checks whether a certain setting exists for given rule.
     *
     * @param string $rule
     * @param string $setting
     * @return bool
     */
    public function hasSetting($rule, $setting)
    {
        return isset($this->ruleSettings[$rule]) && isset($this->ruleSettings[$rule][$setting]);
    }

    /**
     * Returns given setting for a given rule.
     *
     * @param string $rule
     * @param string $setting
     * @return string
     */
    public function getSetting($rule, $setting)
    {
        if (!isset($this->ruleSettings[$rule]) && !isset($this->ruleSettings[$rule][$setting])) {
            return '';
        }

        return $this->ruleSettings[$rule][$setting];
    }
}
?>