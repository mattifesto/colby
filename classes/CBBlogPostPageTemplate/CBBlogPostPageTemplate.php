<?php

/**
 * Sites that use this page template should also return CBBlogPostPageKind as
 * one of the class names returned by CBPageHelpers::classNamesForPageKinds().
 */
final class CBBlogPostPageTemplate {

    /**
     * @return object
     */
    static function model($classNameForPageKind = 'CBBlogPostPageKind') {
        $spec = CBStandardPageTemplate::model();

        $spec->classNameForKind = $classNameForPageKind;
        $spec->layout->isArticle = true;
        $spec->sections[0]->showPublicationDate = true;

        return $spec;
    }

    /**
     * @return string
     */
    static function title() {
        return 'Blog Post';
    }
}
