<?php

namespace spriebsch\PHPca\Rule;

use spriebsch\PHPca\Token;

/**
 * Ensures that the control portions of switch blocks follows a consistent
 * indent pattern, such that the closing break statement should be indented
 * to the same level as the corresponding opening case statement.
 */
class SwitchBlockIndentationRule extends Rule
{
    /**
     * Performs the rule check
     *
     * @return void
     */
    protected function doCheck()
    {
        while ($this->file->valid()) {
            $prev = $this->file->current();
            $this->file->next();

            if (!$this->file->valid()) {
                break;
            }
            $current = $this->file->current();

            if ($current->getId() != T_BREAK) {
                continue;
            }

            $expected = str_repeat("\t", $current->getBlockLevel());
            $indentation = $prev->getText();

            if ($indentation != $expected) {
                 $this->addViolation(
                     "Inconsistent indentation: `break` doesn't correspond to `case`",
                     $this->file->current()
                 );
            }
        }
    }
}

?>