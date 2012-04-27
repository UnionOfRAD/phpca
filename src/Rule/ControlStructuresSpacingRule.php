<?php

namespace spriebsch\PHPca\Rule;

use spriebsch\PHPca\Token;

/**
 * Ensures that the control structures have a space before the parenthesis
 * and a space between the parenthesis and the brace.
 */
class ControlStructuresSpacingRule extends Rule
{

    protected $controlTokens = array(
        T_IF,
        T_WHILE,
        T_FOREACH,
        T_FOR,
        T_SWITCH
    );

    /**
     * Performs the rule check
     *
     * @return void
     */
    protected function doCheck()
    {
        foreach ($this->controlTokens as $id) {
            while ($this->file->seekTokenId($id)) {
                $controlToken = $this->file->current();
                $name = $controlToken->getText();
                $this->file->next();
                $token = $this->file->current();

                if ($token->getId() != T_WHITESPACE) {
                    $this->addViolation("No space after `{$name}`", $controlToken);
                    $this->file->prev();
                }

                while ($this->file->valid()) {
                    $this->file->seekTokenId(T_CLOSE_BRACKET);

                    while ($this->file->valid()) {
                        $this->file->next();

                        if (!$this->file->valid()) {
                            break(2);
                        }

                        if ($this->file->current()->getId() !== T_WHITESPACE) {
                            continue(2);
                        }
                        if ($this->file->current()->getId() !== T_OPEN_CURLY) {
                            break(2);
                        }
                    }
                }
                if ($this->file->current()->getId() === T_OPEN_CURLY) {
                    $curlyToken = $this->file->current();

                    if ($controlToken->getLine() == $curlyToken->getLine()) {
                        $this->file->prev();
                        $token = $this->file->current();

                        if ($token->getId() != T_WHITESPACE) {
                            $this->addViolation("No space before brace in `{$name}` statement", $token);
                        }
                    }
                }

                $this->file->seekToken($controlToken);
                $this->file->next();
            }
            $this->file->rewind();
        }
    }
}

?>