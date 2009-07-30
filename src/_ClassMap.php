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

// @codeCoverageIgnoreStart
$_classMap = array(
    'spriebsch\PHPca\Constants'                   => 'Constants.php',
    'spriebsch\PHPca\Helper'                      => 'Helper.php',
    'spriebsch\PHPca\Token'                       => 'Token.php',
    'spriebsch\PHPca\Linter'                      => 'Linter.php',
    'spriebsch\PHPca\Tokenizer'                   => 'Tokenizer.php',
    'spriebsch\PHPca\File'                        => 'File.php',
    'spriebsch\PHPca\PhpFileFilterIterator'       => 'PhpFileFilterIterator.php',
    'spriebsch\PHPca\Finder'                      => 'Finder.php',
    'spriebsch\PHPca\TokenFilterIterator'         => 'TokenFilterIterator.php',
    'spriebsch\PHPca\Result'                      => 'Result.php',
    'spriebsch\PHPca\Message'                     => 'Message.php',
    'spriebsch\PHPca\Warning'                     => 'Warning.php',
    'spriebsch\PHPca\Error'                       => 'Error.php',
    'spriebsch\PHPca\LintError'                   => 'LintError.php',
    'spriebsch\PHPca\UnknownTokenException'       => 'UnknownTokenException.php',
    'spriebsch\PHPca\ProgressPrinterInterface'    => 'ProgressPrinterInterface.php',
    'spriebsch\PHPca\Application'                 => 'Application.php',
    'spriebsch\PHPca\CLI'                         => 'CLI.php',

    'spriebsch\PHPca\RuleError'                                 => 'RuleError.php',
    'spriebsch\PHPca\Rule\Rule'                                 => 'Rule.php',
    'spriebsch\PHPca\Rule\NoTabulatorsRule'                     => 'Rule/NoTabulatorsRule.php',
    'spriebsch\PHPca\Rule\NoCarriageReturnsRule'                => 'Rule/NoCarriageReturnsRule.php',
    'spriebsch\PHPca\Rule\OpenTagAtBeginningRule'               => 'Rule/OpenTagAtBeginningRule.php',
    'spriebsch\PHPca\Rule\CloseTagAtEndRule'                    => 'Rule/CloseTagAtEndRule.php',
    'spriebsch\PHPca\Rule\IncludeAndRequireWithoutBracketsRule' => 'Rule/IncludeAndRequireWithoutBracketsRule.php',
    'spriebsch\PHPca\Rule\OneTrueBraceStyleRule'                => 'Rule/OneTrueBraceStyleRule.php',
    'spriebsch\PHPca\Rule\NoEvalStatementsRule'                 => 'Rule/NoEvalStatementsRule.php',
    'spriebsch\PHPca\Rule\NoVarKeywordsRule'                    => 'Rule/NoVarKeywordsRule.php',

    'spriebsch\PHPca\Helper'                      => 'Helper.php',

    'spriebsch\PHPca\Pattern\PatternInterface'    => 'Pattern/PatternInterface.php',
    'spriebsch\PHPca\Pattern\Pattern'             => 'Pattern/Pattern.php',
    'spriebsch\PHPca\Pattern\Token'               => 'Pattern/Token.php',
    'spriebsch\PHPca\Pattern\OneOf'               => 'Pattern/OneOf.php',
    'spriebsch\PHPca\Pattern\ZeroOrMore'          => 'Pattern/ZeroOrMore.php',
    'spriebsch\PHPca\Pattern\OneOrMore'           => 'Pattern/OneOrMore.php',
);
// @codeCoverageIgnoreEnd
?>