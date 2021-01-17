<?php

final class CBView_CBSearchResults {



    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.10.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



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
        return (object)[];
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
            cb_query_string_value(
                'search-for'
            )
        );

        if ($searchQuery === '') {
            return;
        }

        ?>

        <div class="CBView_CBSearchResults CBUI_view">
            <div class="CBUI_viewContent">

                <?php

                $searchClause = CBPages::searchClauseFromString(
                    $searchQuery
                );

                if (empty($searchClause)) {
                    $summaries = [];
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

                if (count($pagesKeyValueData) > 0) {
                    foreach ($pagesKeyValueData as $pageKeyValueData) {
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



    /* -- functions -- */



    /**
     * @param object $pageKeyValueData
     *
     * @return void
     */
    private static function
    renderResult(
        stdClass $pageKeyValueData
    ): void {
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

        if (empty($imageURL)) {
            $imageURL = CBModel::valueToString(
                $pageKeyValueData,
                'thumbnailURL'
            );
        }

        ?>

        <article class="CBView_CBSearchResults_result CBUI_view">
            <div class="CBView_CBSearchResults_resultContent CBUI_viewContent">

                <a
                    class="CBView_CBSearchResults_thumbnail"
                    href="<?= $URL ?>"
                >

                    <?php

                    if (empty($imageURL)) {
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
        $titleAsCBMessage = cbmessage($title);
        $descriptionAsCBMessage = cbmessage($description);

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
