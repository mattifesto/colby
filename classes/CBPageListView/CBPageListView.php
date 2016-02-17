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
        $SQL = <<<EOT

            SELECT  `published`, `subtitleHTML`, `thumbnailURL`, `titleHTML`, `URI`
            FROM `ColbyPages`
            WHERE `classNameForKind` = {$classNameForKindForSQL} AND
                  `published` IS NOT NULL
            ORDER BY `published` DESC
            LIMIT 10

EOT;

        $pages = CBDB::SQLToObjects($SQL);

        CBHTMLOutput::addCSSURL(CBTheme::IDToCSSURL($model->themeID));

        ?>

        <div class="CBPageListView <?= CBTheme::IDToCSSClass($model->themeID) ?>"><?php

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
            <?php } ?>

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
}
