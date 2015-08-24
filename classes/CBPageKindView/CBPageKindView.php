<?php

final class CBPageKindView {

    /**
     * @return {stdClass}
     */
    private static function fetchCatalogDataByYearForPageKind($classNameForKind) {
        $classNameForKindAsSQL  = ColbyConvert::textToSQL($classNameForKind);
        $SQL                    = <<<EOT

            SELECT      `publishedMonth`, COUNT(*) AS `count`
            FROM        `ColbyPages`
            WHERE       `classNameForKind` = '{$classNameForKindAsSQL}' AND
                        `publishedMonth` IS NOT NULL
            GROUP BY    `publishedMonth`
            ORDER BY    `publishedMonth` DESC

EOT;

        $dataByMonth    = CBDB::SQLToObjects($SQL);
        $dataByYear     = array_reduce($dataByMonth, function($carry, $monthData) {
            $year   = substr($monthData->publishedMonth, 0, 4);
            $month  = substr($monthData->publishedMonth, 4, 2);

            if (isset($carry[$year])) {
                $yearData = $carry[$year];
            } else {
                $yearData = [];
            }

            $yearData[$month]   = $monthData;
            $carry[$year]       = $yearData;

            return $carry;
        }, []);

        return $dataByYear;
    }
    /**
     * @return [{stdClass}]
     */
    private static function fetchRecentlyPublishedSummariesForPageKind($classNameForKind, $args = []) {
        $count = 10;
        extract($args, EXTR_IF_EXISTS);

        $classNameForKindAsSQL  = ColbyConvert::textToSQL($classNameForKind);
        $countAsSQL             = (int)$count;
        $SQL                    = <<<EOT

            SELECT      `keyValueData`
            FROM        `ColbyPages`
            WHERE       `classNameForKind`  = '$classNameForKindAsSQL' AND
                        `publishedMonth`    IS NOT NULL
            ORDER BY    `publishedMonth` DESC, `published` DESC

EOT;

        return CBDB::SQLToArray($SQL, [ 'valueIsJSON' => true ]);
    }

    /**
     * @param   {string}    $month
     *  Ex: "201508"
     * @param   {string}    $classNameForKind
     *
     * @return  [{stdClass}]
     */
    private static function fetchSummariesForMonth($month, $classNameForKind) {
        $monthAsSQL             = ColbyConvert::textToSQL($month);
        $classNameForKindAsSQL  = ColbyConvert::textToSQL($classNameForKind);
        $SQL                    = <<<EOT

            SELECT      `keyValueData`
            FROM        `ColbyPages`
            WHERE       `classNameForKind`  = '{$classNameForKindAsSQL}' AND
                        `publishedMonth`    = '{$monthAsSQL}'
            ORDER BY    `published` DESC

EOT;

        return CBDB::SQLToArray($SQL, [ 'valueIsJSON' => true ]);
    }

    /**
     * @return [{string}]
     */
    public static function editorURLsForCSS() {
        return [
            CBPageKindView::URL('CBPageKindViewEditor.css')
        ];
    }

    /**
     * @return [{string}]
     */
    public static function editorURLsForJavaScript() {
        return [
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBPageKindView::URL('CBPageKindViewEditorFactory.js')
        ];
    }

    /**
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        if ($model->classNameForKind === null) {
            echo '<!-- CBPageKindView: You must specify a class name for kind property value for this view to work properly. -->';
            return;
        }

        $pageModel = CBViewPage::modelContext();

        if ($pageModel->classNameForKind !== 'CBPageKindLibraryPageKind') {
            echo '<!-- CBPageKindView: The page kind for this page must be "CBPageKindLibraryPageKind" for this view to work properly. -->';
            return;
        }

        $headerThemeClass   = $model->headerThemeID ? "T{$model->headerThemeID}" : 'DefaultTheme';
        $summaryThemeClass  = $model->summaryThemeID ? "T{$model->summaryThemeID}" : 'DefaultTheme';
        $yearThemeClass     = $model->yearThemeID ? "T{$model->yearThemeID}" : 'DefaultTheme';
        $type               = isset($_GET['CBPageKindViewType']) ? $_GET['CBPageKindViewType'] : null;
        $URLAsHTML          = ColbyConvert::textToHTML(CBSiteURL . strtok($_SERVER['REQUEST_URI'], '?'));

        CBHTMLOutput::addCSSURL(CBPageKindView::URL('CBPageKindView.css'));

        switch ($type) {
            case 'library':
                $dataByYear = CBPageKindView::fetchCatalogDataByYearForPageKind($model->classNameForKind);
                include __DIR__ . '/renderLibrary.php';
                break;

            case 'month':
                $monthData      = $pageModel->modelForKind->monthData;
                $summaries      = CBPageKindView::fetchSummariesForMonth($monthData->month, $model->classNameForKind);
                $titleAsHTML    = "{$monthData->monthNameAsHTML} {$monthData->year}";

                include __DIR__ . '/renderMonth.php';
                break;

            default:
                $summaries = CBPageKindView::fetchRecentlyPublishedSummariesForPageKind($model->classNameForKind);
                include __DIR__ . '/renderRecent.php';
        }
    }

    /**
     * @return null
     */
    private static function renderPageSummaryModelAsHTML(stdClass $model, $themeClass) {
        ?>

        <article class="summary">
                <div class="thumbnail">
                    <img src="<?= $model->thumbnailURL ?>" alt="">
                </div>
                <div class="CBTextBoxView  <?= $themeClass ?>">
                    <h1>
                        <a href="<?= CBSiteURL . "/{$model->URI}" ?>"><?= $model->titleHTML ?></a>
                    </h1>
                    <div>
                        <?= $model->descriptionHTML ?>
                        <p><?= ColbyConvert::timestampToHTML($model->publicationTimeStamp) ?>
                    </div>
                </div>
        </article>

        <?php
    }

    /**
     * @return  {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model                      = CBModels::modelWithClassName(__CLASS__);
        $model->classNameForKind    = isset($spec->classNameForKind) ? $spec->classNameForKind : null;
        $model->headerThemeID       = false;
        $model->summaryThemeID      = false;
        $model->yearThemeID         = false;

        return $model;
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBPageKindView/{$filename}";
    }
}
