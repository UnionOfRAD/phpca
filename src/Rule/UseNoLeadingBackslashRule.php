<?php

namespace spriebsch\PHPca\Rule;

class UseNoLeadingBackslashRule extends Rule
{
    /**
     * Performs the rule check.
     *
     * @returns null
     */
    protected function doCheck()
    {
        while ($this->file->seekTokenId(T_USE)) {
            $this->file->next();
            $this->file->next();
            $token = $this->file->current();

            if ($token->getText() == '\\') {
                $this->addViolation('`use` operator followed by class with leading backslash', $token);
            }
        }
    }
}

?>