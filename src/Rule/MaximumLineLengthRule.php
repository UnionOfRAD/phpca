<?php

namespace spriebsch\PHPca\Rule;

use spriebsch\PHPca\Token;
use Exception;

/**
 * Ensures that no line is longer than a certain amount of characters.
 */
class MaximumLineLengthRule extends Rule
{
    /**
     * Performs the rule check.
     *
     * @returns null
     */
    protected function doCheck()
    {
        $strlen = $this->_strlen();

        $lines = explode(
            stripcslashes($this->configuration->getLineEndings()),
            $this->file->getSourceCode()
        );
        foreach ($lines as $i => $line) {
            $length = $strlen($line);
            $tabs = array();
            $tabs = preg_match_all("/\t/", $line, $tabs);
            $length += ($tabs * 3);

            if ($length > $this->settings['line_length']) {
                $this->addViolation(
                    "Maximum line length exceeded",
                    null, $i + 1, $this->settings['line_length'] + 1
                );
            }
        }
    }

    protected function _strlen() {
        if ($this->getSetting('multibyte')) {
            if (extension_loaded('intl')) {
                return function($string) { return grapheme_strlen($string); };
            } elseif (extension_loaded('mbstring')) {
                return function($string) { return mb_strlen($string, 'UTF-8'); };
            }
            $message  = 'No multibyte enabled `strlen()` function available. ';
            $message .= 'Install the `intl` or `mbstring` extension.';
            throw new Exception($message);
        }
        return function($string) { return strlen($string); };
    }
}

?>