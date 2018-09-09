<?php

final class CBPageListView2 {

    /**
     * @param string $_POST['classNameForKind']
     *
     * @return null
     */
    static function fetchPagesForAjax() {
        $response = new CBAjaxResponse();
        $classNameForKindAsSQL = CBDB::stringToSQL(cb_post_value('classNameForKind', ''));

        if (empty($_POST['publishBeforeTimestamp'])) {
            $publishedBeforeClause = '';
        } else {
            $publishBeforeTimestamp = intval($_POST['publishBeforeTimestamp']);
            $publishedBeforeClause = "AND `published` < {$publishBeforeTimestamp}";
        }

        $SQL = <<<EOT

            SELECT  `keyValueData`
            FROM    `ColbyPages`
            WHERE   `classNameForKind` = {$classNameForKindAsSQL} AND
                    `published` IS NOT NULL
                    {$publishedBeforeClause}
            ORDER BY `published` DESC
            LIMIT 10

EOT;

        $response->pages = CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);
        $response->message = count($response->pages) . " pages were fetched.";
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    static function fetchPagesForAjaxPermissions() {
        return (object)['group' => 'Public'];
    }

    /**
     * @param string $model->classNameForKind
     * @param [string]? $model->CSSClassNames
     *
     * @return void
     */
    static function CBView_render(stdClass $model): void {
        if (empty($model->classNameForKind)) {
            echo '<!-- CBPageListView2 with no classNameForKind -->';
            return;
        }

        $CSSClassNames = CBModel::valueToArray($model, 'CSSClassNames');

        if (!in_array('custom', $CSSClassNames)) {
                $CSSClassNames[] = 'CBPageListView2_default';
        }

        array_walk($CSSClassNames, 'CBHTMLOutput::requireClassName');

        $CSSClassNames = cbhtml(implode(' ', $CSSClassNames));

        ?>

        <div class="CBPageListView2 <?= $CSSClassNames ?>" data-classnameforkind="<?= $model->classNameForKind ?>">
        </div>

        <?php
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBArtworkElement', 'CBUI'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v453.css', cbsysurl()),
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v453.js', cbsysurl()),
        ];
    }

    /**
     * @param model $spec
     *
     * @return ?object
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        return (object)[
            'classNameForKind' => CBModel::value($spec, 'classNameForKind', '', 'trim'),
            'CSSClassNames' => CBModel::valueToNames($spec, 'CSSClassNames'),
        ];
    }
}
