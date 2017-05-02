<?php

final class CBPageListView2 {

    /**
     * @param string $_POST['classNameForKind']
     *
     * @return null
     */
    static function fetchPagesForAjax() {
        $response = new CBAjaxResponse();
        $classNameForKindAsSQL = CBDB::stringToSQL($_POST['classNameForKind']);

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
     * @param bool? $model->isCustom
     *
     *      If this is true, the renderer will not include the standard CSS
     *      class names and will only include the class names specified by the
     *      CSSClassNames property. This disables the standard formatting of the
     *      view and allows for fully customized presentation.
     *
     * @return null
     */
    static function renderModelAsHTML(stdClass  $model) {
        if (empty($model->classNameForKind)) {
            echo '<!-- CBPageListView2 with no classNameForKind -->';
            return;
        }

        if (empty($model->isCustom)) {
            $defaultCSSClassNames = ['CBPageListView2StandardLayout'];
        } else {
            $defaultCSSClassNames = [];
        }

        $modelCSSClassNames = CBModel::value($model, 'CSSClassNames', []);

        if (is_array($modelCSSClassNames)) {
            $CSSClassNames = array_unique(array_merge($defaultCSSClassNames, $modelCSSClassNames));
        } else {
            $CSSClassNames = $defaultCSSClassNames;
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
    static function requiredClassNames() {
        return ['CBArtworkElement'];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }

    static function specToModel(stdClass $spec) {
        $model = (object)[
            'className' => __CLASS__,
            'classNameForKind' => CBModel::value($spec, 'classNameForKind', '', 'trim'),
            'isCustom' => CBModel::value($spec, 'isCustom', false, 'boolval'),
        ];

        // CSSClassNames
        $CSSClassNames = CBModel::value($spec, 'CSSClassNames', '');
        $CSSClassNames = preg_split('/[\s,]+/', $CSSClassNames, null, PREG_SPLIT_NO_EMPTY);

        if ($CSSClassNames === false) {
            throw new RuntimeException("preg_split() returned false");
        }

        $model->CSSClassNames = $CSSClassNames;

        return $model;
    }
}
