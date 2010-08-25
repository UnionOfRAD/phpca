<?php

namespace spriebsch\PHPca\Rule;

use spriebsch\PHPca\Token;
use spriebsch\PHPca\Pattern\Token as PatternToken;
use spriebsch\PHPca\Pattern\Pattern;
use spriebsch\PHPca\Finder;

/**
 * Ensures that the control structures have a space before the parenthesis
 * and a space between the parenthesis and the brace.
 */
class ControlStructuresBracesRule extends Rule
{
    protected $blacklist = array();

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
        // exclude do ... while statements by putting them on the blacklist
        $pattern = new Pattern();
        $pattern->token(T_DO)
                ->token(T_WHITESPACE)
                ->token(T_OPEN_CURLY)
                ->zeroOrMore(new PatternToken(T_ANY))
                ->token(T_CLOSE_CURLY)
                ->token(T_WHITESPACE)
                ->token(T_WHILE);

        $this->blacklist = array();

        foreach (Finder::findPattern($this->file, $pattern) as $match) {
            if ($match[0]->getBlockLevel() == $match[sizeof($match) - 1]->getBlockLevel()) {
                $this->blacklist[] = $match[sizeof($match) - 1];
            }
        }
        $this->file->rewind();

        foreach ($this->controlTokens as $id) {
            while ($this->file->seekTokenId($id)) {
                $controlToken = $this->file->current();
                $name = $controlToken->getText();

                if (in_array($controlToken, $this->blacklist)) {
                  $this->file->seekToken($controlToken);
                } elseif ($this->file->seekTokenId(T_OPEN_CURLY)) {
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