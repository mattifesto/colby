<?php

/**
 * Sites that use this page template should also return CBBlogPostPageKind as
 * one of the class names returned by CBPageHelpers::classNamesForPageKinds().
 */
final class CBBlogPostPageTemplate {

    /**
     * @return object
     */
    static function CBModelTemplate_spec() {
        $spec = (object)[
            'className' => 'CBViewPage',
            'classNameForKind' => 'CBBlogPostPageKind',
            'layout' => (object)[
                'className' => 'CBPageLayout',
                'CSSClassNames' => 'endContentWithWhiteSpace',
                'isArticle' => true,
            ],
        ];

        $spec->sections[] = (object)[
            'className' => 'CBPageTitleAndDescriptionView',
            'showPublicationDate' => true,
        ];

        $spec->sections[] = (object)[
            'className' => 'CBArtworkView',
        ];

        $spec->sections[] = (object)[
            'className' => 'CBMessageView',
        ];

        return $spec;
    }

    /**
     * @return string
     */
    static function CBModelTemplate_title() {
        return 'Blog Post';
    }
}
