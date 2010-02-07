<?php

namespace spriebsch\PHPca\Rule;

use spriebsch\PHPca\Token;

/**
 * Ensures that the control structures have a space before the parenthesis
 * and a space between the parenthesis and the brace.
 */
class ControlStructuresBracesRule extends Rule
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

                if ($this->file->seekTokenId(T_OPEN_CURLY)) {
                    $curlyToken = $this->file->current();

                    if (!$this->file->valid() || $controlToken->getLine() != $curlyToken->getLine()) {
                        if ($controlToken->getLine() == $curlyToken->getLine() - 1) {
                            $this->addViolation("Brace for `{$name}` statement on wrong line", $curlyToken);
                        } else {
                            $this->addViolation("No brace for `{$name}` statement", $controlToken);
                            $this->file->seekToken($controlToken);
                        }
                    }
                } else {
                    $this->addViolation("No brace for `{$name}` statement", $controlToken);
                    $this->file->seekToken($controlToken);
                }

                $this->file->next();
            }
            $this->file->rewind();
        }
    }
}

?>