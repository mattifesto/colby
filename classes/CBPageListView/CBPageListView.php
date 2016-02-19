<?php

final class CBPageListView {

    /**
     * @param stdClass $model->classNameForKind
     *
     * @return string
     */
    public static function modelToSearchText(stdClass $model) {
        return $model->classNameForKind;
    }

    /**
     * @param stdClass $model
     *
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model) {
        $classNameForKindForSQL = CBDB::stringToSQL($model->classNameForKind);
        $year = isset($_GET['year']) ? $_GET['year'] : null;
        $wheres = [];
        $wheres[] = "`classNameForKind` = {$classNameForKindForSQL}";
        if (preg_match('/^[0-9]{4}$/', $year)) {
            $limit = '';
            $title = $year;
            $wheres[] = CBPageListView::whereClauseForYear($year);
        } else if ($year !== null) {
            CBHTMLOutput::render404();
        } else {
            $limit = 'LIMIT 10';
            $title = 'Recent';
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

        $pages = CBDB::SQLToObjects($SQL);

        CBHTMLOutput::addCSSURL(CBTheme::IDToCSSURL($model->themeID));

        ?>

        <div class="CBPageListView <?= CBTheme::IDToCSSClass($model->themeID) ?>">
            <h1><?= $title ?></h1><?php

            foreach ($pages as $page) { ?>
                <a href="<?= CBSiteURL . "/{$page->URI}/" ?>" class="link"><?php
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
            <?php }

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

            $query = $_GET;
            unset($query['year']);
            $query = http_build_query($query);

            ?><div class="archives">
                <a href="?<?= $query ?>">Recent</a>
                <?php

                foreach ($years as $year) {
                    $query = $_GET;
                    $query['year'] = $year;
                    $query = http_build_query($query);
                    echo " | <a href=\"?{$query}\">{$year}</a>";
                }
            ?></div>
        </div>

        <?php
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
