<?php

final class
CBView_CBSearchResults
{
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        $arrayOfCSSURLs =
        [
            Colby::flexpath(
                __CLASS__,
                'v675.10.css',
                cbsysurl()
            ),
        ];

        return $arrayOfCSSURLs;
    }
    // CBHTMLOutput_CSSURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        $arrayOfRequiredClassNames =
        [
            'CB_UI',
        ];

        return $arrayOfRequiredClassNames;
    }
    // CBHTMLOutput_requiredClassNames()



    /* -- CBModel interfaces -- */



    /**
     * @param object $viewSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        $viewSpec
    ): stdClass {
        return (object)[
            'CBView_CBSearchResults_searchQuery' => (
                CBView_CBSearchResults::getSearchQuery(
                    $viewSpec
                )
            ),
        ];
    }
    /* CBModel_build() */



    /* -- CBView interfaces -- */



    /**
     * @param object $viewModel
     *
     * @return void
     */
    static function
    CBView_render(
        stdClass $viewModel
    ): void {
        $searchQuery = trim(
            CBView_CBSearchResults::getSearchQuery(
                $viewModel
            )
        );

        /**
         * @NOTE 2021_02_17
         *
         *      I don't love that this view directly accesses the query
         *      variable. At some point look at this situation in detail and
         *      determine if that is the correct way of doing things and if so,
         *      document here.
         */

        if (
            $searchQuery === ''
        ) {
            $searchQuery = trim(
                cb_query_string_value(
                    'search-for'
                )
            );
        }

        if (
            $searchQuery === ''
        ) {
            return;
        }

        ?>

        <div class="CBView_CBSearchResults CBUI_view">
            <div class="CBUI_viewContent">

                <?php

                /* results from CBModels */

                $searchResults = CBModels::fetchSearchResults(
                    $searchQuery
                );

                foreach (
                    $searchResults as $searchResult
                ) {
                    CBView_CBSearchResults::renderResult(
                        $searchResult
                    );
                }

                /* results from CBPages */

                $searchClause = CBPages::searchClauseFromString(
                    $searchQuery
                );

                if (
                    empty($searchClause)
                ) {
                    $pagesKeyValueData = [];
                } else {
                    $SQL = <<<END

                        SELECT      keyValueData
                        FROM        ColbyPages
                        WHERE       published IS NOT NULL AND
                                    {$searchClause}
                        ORDER BY    published

                    END;

                    $pagesKeyValueData = CBDB::SQLToArray(
                        $SQL,
                        [
                            'valueIsJSON' => true,
                        ]
                    );
                }

                if (
                    count($pagesKeyValueData) > 0
                ) {
                    foreach (
                        $pagesKeyValueData as $pageKeyValueData
                    ) {
                        CBView_CBSearchResults::renderResult(
                            $pageKeyValueData
                        );
                    }
                }

                ?>

            </div>
        </div>

        <?php
    }
    /* CBView_render() */



    /* -- accessors -- */



    /**
     * @param object $viewSpec
     *
     * @return string
     */
    static function
    getSearchQuery(
        stdClass $viewSpec
    ): string {
        return CBModel::valueToString(
            $viewSpec,
            'CBView_CBSearchResults_searchQuery'
        );
    }
    /* getSearchQuery() */



    /**
     * @param object $viewSpec
     * @param string $searchQuery
     *
     * @return void
     */
    static function
    setSearchQuery(
        stdClass $viewSpec,
        string $searchQuery
    ): void {
        $viewSpec->CBView_CBSearchResults_searchQuery = $searchQuery;
    }
    /* setSearchQuery() */



    /* -- functions -- */



    /**
     * @param object $searchResult
     *
     * @return void
     */
    private static function
    renderResult(
        stdClass $searchResult
    ): void {
        $className = CBModel::getClassName(
            $searchResult
        );

        if (
            $className === 'CB_SearchResult'
        ) {
            $URL = CB_SearchResult::getURL(
                $searchResult
            );

            $title = CB_SearchResult::getTitle(
                $searchResult
            );

            $description = '';
            $imageURL = '';
        } else {
            $pageKeyValueData = $searchResult;

            $URI = CBModel::valueToString(
                $pageKeyValueData,
                'URI'
            );

            $URL = cbsiteurl() . "/{$URI}/";

            $title = CBModel::valueToString(
                $pageKeyValueData,
                'title'
            );

            $description = CBModel::valueToString(
                $pageKeyValueData,
                'description'
            );

            $imageURL = CBImage::valueToFlexpath(
                $pageKeyValueData,
                'image',
                'rl320',
                cbsiteurl()
            );

            if (
                empty($imageURL)
            ) {
                $imageURL = CBModel::valueToString(
                    $pageKeyValueData,
                    'thumbnailURL'
                );
            }
        }

        ?>

        <article class="CBView_CBSearchResults_result CBUI_view">
            <div class="CBView_CBSearchResults_resultContent CBUI_viewContent">

                <a
                    class="CBView_CBSearchResults_thumbnail"
                    href="<?= $URL ?>"
                >

                    <?php

                    if (
                        empty(
                            $imageURL
                        )
                    ) {
                        CBArtworkElement::render(
                            [
                                'aspectRatioWidth' => 1,
                                'aspectRatioHeight' => 1,
                                'maxWidth' => '150',
                                'URL' => $imageURL,
                            ]
                        );
                    } else {
                        CBArtworkElement::render(
                            [
                                'aspectRatioWidth' => 1,
                                'aspectRatioHeight' => 1,
                                'maxWidth' => '150',
                                'URL' => $imageURL,
                            ]
                        );
                    }

                    ?>

                </a>

                <?php

                CBView_CBSearchResults::renderText(
                    $title,
                    $description,
                    $URL
                );

                ?>

            </div>
        </article>

        <?php
    }
    /* renderResult() */



    /**
     * @param string $title
     * @param string $description
     * @param string $URL
     *
     * @return void
     */
    static function
    renderText(
        string $title,
        string $description,
        string $URL
    ): void {
        $titleAsCBMessage = cbmessage(
            $title
        );

        $descriptionAsCBMessage = cbmessage(
            $description
        );

        $cbmessage = <<<EOT

            --- h3
            ({$titleAsCBMessage} (a {$URL}))
            ---

            {$descriptionAsCBMessage}

        EOT;

        $messageViewSpec = (object)[];

        CBModel::setClassName(
            $messageViewSpec,
            'CBMessageView'
        );

        CBMessageView::setCBMessage(
            $messageViewSpec,
            $cbmessage
        );

        CBMessageView::setCSSClassNames(
            $messageViewSpec,
            'custom CBView_CBSearchResults_text'
        );

        CBView::renderSpec(
            $messageViewSpec
        );
    }
    /* renderText() */

}
