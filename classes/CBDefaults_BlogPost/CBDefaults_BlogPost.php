<?php

final class CBDefaults_BlogPost {

    /* -- functions -- -- -- -- -- */



    /**
     * @return [object]
     *
     *      This function returns an array containing the default view specs for
     *      a blog post. The blog post template created with a new site calls
     *      this function to get its default views. That template can be
     *      customized if this is the desired set of views for a site. Other
     *      templates can also use this function to get the default views.
     *
     *      The reason this function exists is that the default views for a blog
     *      post may change over time. For a specific example, the CBYouTubeView
     *      became very useful. If this function didn't exist and wasn't used,
     *      each site would have to add that view to its defaults through a code
     *      change.
     *
     *      As views become stable and useful, they will be added. Existing
     *      views may be modified or removed if no longer useful.
     */
    static function viewSpecs(): array {
        return [
            (object)[
                'className' => 'CBPageTitleAndDescriptionView',
                'showPublicationDate' => true,
            ],
            (object)[
                'className' => 'CBYouTubeView',
            ],
            (object)[
                'className' => 'CBArtworkView',
            ],
            (object)[
                'className' => 'CBMessageView',
            ]
        ];
    }
    /* viewSpecs() */

}
