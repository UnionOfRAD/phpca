<?php

namespace spriebsch\PHPca\Rule;

use spriebsch\PHPca\Token;

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
        $lines = explode(
            stripcslashes($this->configuration->getLineEndings()),
            $this->file->getSourceCode()
        );
        foreach ($lines as $i => $line) {
            $length = mb_strlen($line, 'UTF8');
            $tabs = array();
            $tabs = preg_match_all("/\t/", $line, $tabs);
            $length += ($tabs * 3);

            if ($length > $this->settings['line_length']) {
                $this->addViolation(
                    'Maximum line length exceeded',
                    null, $i + 1, $this->settings['line_length'] + 1
                );
            }
        }
    }
}
?>