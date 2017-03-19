<?php

final class CBPageListView {

    /**
     * @param string $args->classNameForKind
     * @param string? $args->year
     *
     * @return [stdClass]
     */
    static function fetchPageSummaries($args) {
        $classNameForKindForSQL = CBDB::stringToSQL($args->classNameForKind);
        $wheres = [];
        $wheres[] = "`classNameForKind` = {$classNameForKindForSQL}";

        if (empty($args->year)) {
            $limit = 'LIMIT 25';
        } else {
            $limit = '';
            $wheres[] = CBPageListView::whereClauseForYear($args->year);
        }

        $wheres[] = '`published` IS NOT NULL';
        $wheres = implode(' AND ', $wheres);
        $SQL = <<<EOT

            SELECT  `published`, `subtitleHTML`, `thumbnailURL`, `titleHTML`, `URI`
            FROM `ColbyPages`
            WHERE {$wheres}
            ORDER BY `publishedMonth` DESC, `published` DESC
            {$limit}

EOT;

        return CBDB::SQLToObjects($SQL);
    }

    /**
     * @param string $args->classNameForKind
     *
     * @return [{title, URI}]
     */
    static function fetchArchives($args) {
        $classNameForKindForSQL = CBDB::stringToSQL($args->classNameForKind);
        $SQL = <<<EOT

            SELECT DISTINCT `publishedMonth`
            FROM `ColbyPages`
            WHERE `classNameForKind` = {$classNameForKindForSQL} AND
                  `published` IS NOT NULL
            ORDER BY `publishedMonth` DESC

EOT;

        $months = CBDB::SQLToArray($SQL);
        $years = array_reduce($months, function ($years, $month) {
            $years[] = substr($month, 0, 4);
            return $years;
        }, []);
        $years = array_unique($years);

        $archives = [];

        foreach ($years as $year) {
            $queryVariables = $_GET;
            $queryVariables['year'] = $year;
            $queryString = http_build_query($queryVariables);

            $archives[] = (object)[
                'title' => $year,
                'URI' => "?{$queryString}",
            ];
        }

        /* recent archive link */

        $queryVariables = $_GET;
        unset($queryVariables['year']);
        $queryString = http_build_query($queryVariables);
        $URI = empty($queryString) ? strtok($_SERVER['REQUEST_URI'], '?') : "?{$queryString}";

        array_unshift($archives, (object)[
            'title' => 'Recent',
            'URI' => $URI,
        ]);

        return $archives;
    }

    /**
     * @param stdClass $model->classNameForKind
     *
     * @return string
     */
    public static function modelToSearchText(stdClass $model) {
        return $model->classNameForKind;
    }


    /**
     * @param [stdClass] $args->archives
     * @param stdClass $args->model
     * @param [stdClass] $args->pageSummaries
     * @param string $args->title
     *
     * @return null
     */
    static function renderIndex($args) {
        CBHTMLOutput::addCSSURL(CBTheme::IDToCSSURL($args->model->themeID));

        ?>

        <div class="CBPageListView <?= CBTheme::IDToCSSClass($args->model->themeID) ?>">
            <h1><?= $args->title ?></h1>
            <div class="links">

                <?php foreach ($args->pageSummaries as $page) { ?>
                    <a href="<?= CBSitePreferences::siteURL() . "/{$page->URI}/" ?>" class="link"><?php
                        if (!empty($page->thumbnailURL)) { ?>
                            <div class="thumbnail">
                                <figure style="background-image: url(<?= $page->thumbnailURL ?>);"></figure>
                            </div>
                        <?php } ?>
                        <div>
                            <h1><?= $page->titleHTML ?></h1>
                            <div class="description"><?= $page->subtitleHTML ?></div>
                            <div><?= ColbyConvert::timestampToHTML($page->published) ?></div>
                        </div>
                    </a>
                <?php } ?>

            </div>

            <div class="archives">
                <?php

                $links = array_map(function ($archive) {
                    return "<a href=\"{$archive->URI}\">{$archive->title}</a>";
                }, $args->archives);

                echo implode(' | ', $links);

                ?>
            </div>
        </div>

        <?php
    }

    /**
     * @param string $model->classNameForKind
     *
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        $year = isset($_GET['year']) ? $_GET['year'] : null;

        if (preg_match('/^[0-9]{4}$/', $year)) {
            $title = $year;
        } else if ($year !== null) {
            CBHTMLOutput::render404();
        } else {
            $title = 'Recent';
        }

        $pageSummaries = CBPageListView::fetchPageSummaries((object)[
            'classNameForKind' => $model->classNameForKind,
            'year' => $year,
        ]);

        $archives = CBPageListView::fetchArchives((object)[
            'classNameForKind' => $model->classNameForKind,
        ]);

        if (is_callable($function = "{$model->classNameForKind}::renderIndexForCBPageListView")) {
            call_user_func($function, (object)[
                'archives' => $archives,
                'pageSummaries' => $pageSummaries,
                'title' => $title,
            ]);
        } else {
            CBPageListView::renderIndex((object)[
                'archives' => $archives,
                'model' => $model,
                'pageSummaries' => $pageSummaries,
                'title' => $title,
            ]);
        }
    }

    /**
     * @param stdClass $spec
     *
     * @return stdClass
     */
    public static function specToModel(stdClass $spec) {
        return (object)[
            'className' => __CLASS__,
            'classNameForKind' => CBModel::value($spec, 'classNameForKind', null, 'trim'),
            'themeID' => CBModel::value($spec, 'themeID'),
        ];
    }

    /**
     * @param int $year
     *
     * @return string
     */
    private static function whereClauseForYear($year) {
        $year = (int)$year;
        $low = "{$year}00";
        $high = "{$year}99";

        return "(`publishedMonth` >= {$low} AND `publishedMonth` <= {$high})";
    }
}
