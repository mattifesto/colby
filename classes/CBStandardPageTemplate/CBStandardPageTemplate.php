<?php

final class CBStandardPageTemplate {

    /**
     * @return object
     */
    static function model() {
        $spec = (object)[
            'className' => 'CBViewPage',
            'layout' => (object)[
                'className' => 'CBPageLayout',
                'CSSClassNames' => 'endContentWithWhiteSpace',
            ],
        ];

        if (is_callable($function = 'CBPageHelpers::classNameForPageSettings')) {
            $spec->classNameForSettings = call_user_func($function);
        }

        $spec->sections[] = (object)[
            'className' => 'CBPageTitleAndDescriptionView',
        ];

        $spec->sections[] = (object)[
            'className' => 'CBArtworkView',
        ];

        // text
        $spec->sections[] = (object)[
            'className' => 'CBTextView2',
        ];

        return $spec;
    }

    /**
     * @return string
     */
    static function title() {
        return 'Standard Page';
    }
}
