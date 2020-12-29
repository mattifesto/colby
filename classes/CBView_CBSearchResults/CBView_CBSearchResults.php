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
                'v675.9.css',
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

        <section class="CBView_CBSearchResults">

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

                $summaries = CBDB::SQLToArray(
                    $SQL,
                    [
                        'valueIsJSON' => true,
                    ]
                );
            }

            if (count($summaries) > 0) {
                foreach ($summaries as $model) {
                    $URI = CBModel::valueToString(
                        $model,
                        'URI'
                    );

                    $URL = cbsiteurl() . "/{$URI}/";

                    $title = CBModel::valueToString(
                        $model,
                        'title'
                    );

                    $description = CBModel::valueToString(
                        $model,
                        'description'
                    );

                    $imageURL = CBImage::valueToFlexpath(
                        $model,
                        'image',
                        'rl320',
                        cbsiteurl()
                    );

                    if (empty($imageURL)) {
                        $imageURL = CBModel::valueToString(
                            $model,
                            'thumbnailURL'
                        );
                    }

                    ?>

                    <article class="CBView_CBSearchResults_result">
                        <div class="CBView_CBSearchResults_thumbnail">
                            <?php

                            CBArtworkElement::render(
                                [
                                    'aspectRatioWidth' => 1,
                                    'aspectRatioHeight' => 1,
                                    'maxWidth' => '150',
                                    'URL' => $imageURL,
                                ]
                            );

                            ?>
                        </div>
                        <div class="text">
                            <h1>
                                <a href="<?= $URL ?>"><?= cbhtml($title) ?></a>
                            </h1>
                            <div>
                                <p><?= cbhtml($description) ?>
                            </div>
                        </div>
                    </article>

                    <?php
                }
            }

            ?>

        </section>

        <?php
    }
    /* CBView_render() */

}
