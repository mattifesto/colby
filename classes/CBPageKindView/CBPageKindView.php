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
     * @return  {stdClass} | false
     */
    private static function parseMonth($month) {
        if (preg_match('/[0-9]{6}/', $month)) {
            $monthNumber = substr($month, 4, 2);

            if ($monthNumber > 0 && $monthNumber < 13) {
                $dateTime               = DateTime::createFromFormat('!m', $monthNumber);
                $data                   = new stdClass();
                $data->monthName        = $dateTime->format('F');
                $data->monthNameAsHTML  = ColbyConvert::textToHTML($data->monthName);
                $data->year             = substr($month, 0, 4);

                return $data;
            }
        }

        return false;
    }

    /**
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        if ($model->classNameForKind === null) {
            echo '<!-- CBPageKindView: You must set a page kind for this view to work propertly. -->';
            return;
        }

        $themeClass = $model->themeID ? "T{$model->themeID}" : 'NoTheme';
        $type       = isset($_GET['CBPageKindViewType']) ? $_GET['CBPageKindViewType'] : null;
        $URLAsHTML  = ColbyConvert::textToHTML(CBSiteURL . strtok($_SERVER['REQUEST_URI'], '?'));

        CBHTMLOutput::addCSSURL(CBPageKindView::URL('CBPageKindView.css'));

        switch ($type) {
            case 'library':
                $dataByYear = CBPageKindView::fetchCatalogDataByYearForPageKind($model->classNameForKind);
                include __DIR__ . '/renderLibrary.php';
                break;

            case 'month':
                $month      = isset($_GET['CBPageKindViewMonth']) ? $_GET['CBPageKindViewMonth'] : '';

                if ($monthData = CBPageKindView::parseMonth($month)) {
                    $summaries      = CBPageKindView::fetchSummariesForMonth($month, $model->classNameForKind);
                    $titleAsHTML    = "{$monthData->monthNameAsHTML} {$monthData->year}";
                } else {
                    $titleAsHTML    = 'Invalid Month';
                    $summaries      = 'invalid month';
                }

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
    private static function renderPageSummaryModelAsHTML(stdClass $model) {
        ?>

        <article class="summary">
                <div class="thumbnail">
                    <img src="<?= $model->thumbnailURL ?>" alt="">
                </div>
                <div class="content">
                    <h1><a href="<?= CBSiteURL . "/{$model->URI}" ?>">
                        <?= $model->titleHTML ?>
                    </a></h1>

                    <p><?= $model->descriptionHTML ?>
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
        $model->themeID             = false;

        return $model;
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBPageKindView/{$filename}";
    }
}
