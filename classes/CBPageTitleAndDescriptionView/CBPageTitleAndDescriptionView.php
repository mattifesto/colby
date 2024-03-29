<?php

final class
CBPageTitleAndDescriptionView
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
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2023_03_19_1679252420',
                'css',
                cbsysurl()
            ),
        ];

        return $arrayOfCSSURLs;
    }
    // CBHTMLOutput_CSSURLs()



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBViewCatalog::installView(
            __CLASS__
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBViewCatalog',
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param model $spec
     *
     * @return ?object
     */
    static function CBModel_build(stdClass $spec): ?stdClass
    {
        $colorForProperty =
        function ($propertyName) use ($spec)
        {
            return CBModel::value(
                $spec,
                $propertyName,
                null,
                'CBConvert::stringToCSSColor'
            );
        };

        $model =
        (object)[
            'CSSClassNames' =>
            CBModel::valueToNames($spec, 'CSSClassNames'),

            'showPublicationDate' =>
            CBModel::value($spec, 'showPublicationDate', false, 'boolval'),

            /* the following properties are all deprecated */

            'descriptionColor' =>
            $colorForProperty('descriptionColor'),

            'hideDescription' =>
            CBModel::value($spec, 'hideDescription', false, 'boolval'),

            'publishedColor' =>
            $colorForProperty('publishedColor'),

            'titleColor' =>
            $colorForProperty('titleColor'),

            'useLightTextColors' =>
            CBModel::value($spec, 'useLightTextColors', false, 'boolval'),
        ];

        // localCSS (uses nonstandard stylesCSS property for this view)
        $localCSSTemplate = CBModel::value($spec, 'stylesTemplate', '', 'trim');

        if (!empty($localCSSTemplate)) {
            $localCSSClassName = 'ID_' . CBID::generateRandomCBID();
            $model->CSSClassNames[] = $localCSSClassName;

            $model->stylesCSS =
            CBView::localCSSTemplateToLocalCSS(
                $localCSSTemplate,
                'view',
                ".{$localCSSClassName}"
            );
        }

        return $model;
    }
    // CBModel_build()



    // -- CBView interfaces



    /**
     * @param string? $model->descriptionColor @deprecated use CSS
     * @param bool? $model->hideDescription  @deprecated use CSS
     * @param string? $model->publishedColor @deprecated use CSS
     * @param bool? $model->showPublicationDate
     * @param string? $model->titleColor @deprecated use CSS
     * @param bool? $model->useLightTextColors @deprecated use CSS
     *
     * @return void
     */
    static function CBView_render(stdClass $model): void {
        $CSSClassNames = CBModel::valueToArray($model, 'CSSClassNames');

        if (!in_array('custom', $CSSClassNames)) {
            $CSSClassNames[] = 'CBPageTitleAndDescriptionView_default';
        }

        if (!empty($model->stylesID)) {
            $CSSClassNames[] = "T{$model->stylesID}";
        }

        /* @deprecated use CBDarkTheme CSS class name */
        if (!empty($model->useLightTextColors)) {
            $CSSClassNames[] = 'light';
        }

        if (!empty($model->stylesCSS)) {
            CBHTMLOutput::addCSS($model->stylesCSS);
        }

        $info = CBHTMLOutput::pageInformation();

        $CSSClassNames = implode(' ', $CSSClassNames);

        ?>

        <header class="CBPageTitleAndDescriptionView <?= $CSSClassNames ?>">
            <div>

                <?php

                if (!empty($info->title)) {
                    if (empty($model->titleColor)) {
                        $style = '';
                    } else {
                        $style = "color: {$model->titleColor}";
                    }

                    ?>

                    <h1 class="title" style="<?= $style ?>">
                        <?= cbhtml($info->title) ?>
                    </h1>

                    <?php
                }

                if (
                    !empty($info->description) &&
                    empty($model->hideDescription)
                ) {
                    if (empty($model->descriptionColor)) {
                        $style = '';
                    } else {
                        $style = "color: {$model->descriptionColor}";
                    }

                    ?>

                    <div class="description" style="<?= $style ?>">
                        <div><?= cbhtml($info->description) ?></div>
                    </div>

                    <?php
                }

                if (!empty($model->showPublicationDate)) {
                    if (empty($model->publishedColor)) {
                        $style = '';
                    } else {
                        $style = "color: {$model->publishedColor}";
                    }

                    $publishedAsHTML = ColbyConvert::timestampToHTML(
                        CBModel::valueAsInt($info, 'publishedTimestamp'),
                        'Unpublished'
                    );

                    ?>

                    <div class="published" style="<?= $style ?>">
                        <?= $publishedAsHTML ?>
                    </div>

                    <?php
                }

            ?>

            </div>
        </header>

        <?php
    }

}
