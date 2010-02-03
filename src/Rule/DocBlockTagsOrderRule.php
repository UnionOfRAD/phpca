<?php

namespace spriebsch\PHPca\Rule;

use spriebsch\PHPca\Token;

/**
 * Ensures documentation tags have a specific order.
 */
class DocBlockTagsOrderRule extends Rule
{
    public function configure(array $settings)
    {
        if (isset($settings['order'])) {
            $settings['order'] = array_map('trim', explode(',', $settings['order']));
        }
        return parent::configure($settings);
    }

    /**
     * Performs the rule check.
     *
     * All tags will be referenced against the list specified by
     * `DocBlockTagsOrderRule::$settings['order']`. If they
     * appear out of order, a violation will be raised. Simple
     * order check regardless of missing tags.
     *
     * @return null
     */
    protected function doCheck()
    {
        while ($this->file->seekTokenId(T_DOC_COMMENT)) {
            $token = $this->file->current();
            $docText = $token->getText();

            // Grab ordered array of the tags in this token
            $docTags = array();
            preg_match_all('/@([a-z]+)\s/', $docText, $docTags);
            $docTags = $docTags[1];

            $docIndex = 0;
            $lastTag = "";
            foreach ($docTags as $tag) {
                $tagIndex = array_search($tag, $this->settings['order']);

                if ($tagIndex !== false) {
                    if ($tagIndex < $docIndex) {
                        $this->addViolation(
                            "DocBlock tag `{$tag}` not ordered correctly, came after `{$lastTag}` tag",
                            $token
                        );
                        continue;
                    }

                    $docIndex = $tagIndex;
                    $lastTag = $tag;
                }

            }
            $this->file->next();
        }
    }
}
?>