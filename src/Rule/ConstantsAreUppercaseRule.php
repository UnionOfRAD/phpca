<?php

namespace spriebsch\PHPca\Rule;

use spriebsch\PHPca\Token;

/**
 * Ensures that constants are upper case.
 */
class ConstantsAreUppercaseRule extends Rule
{
    /**
     * Performs the rule check.
     *
     * @returns null
     */
    public function doCheck()
    {
        $source = $this->file->getSourceCode();

        $namedConstants = array();
        $definePattern  = '/' . $this->configuration->getLineEndings() . '.*?define\("(.*?)".*[^';
        $definePattern .= $this->configuration->getLineEndings() . ']/';

        preg_match_all($definePattern, $source, $namedConstants);
        $declarations = array_shift($namedConstants);
        $namedConstants = array_shift($namedConstants);

        foreach($namedConstants as $i => $constant) {
            if ($constant != strtoupper($constant)) {
                $lines = array();
                $line = preg_match_all(
                    '/.*?' . $this->configuration->getLineEndings() . '.*?/',
                    substr($source, 0, strpos($source, 'define("' . $constant)),
                    $lines
                );

                $this->addViolation(
                    "Constant `{$constant}` not uppercase",
                    null,
                    $line + 1,
                    strpos($declarations[$i], $constant)
                );
            }
        }
    }
}

?>