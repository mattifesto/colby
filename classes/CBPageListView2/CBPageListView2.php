<?php

final class CBPageListView2 {

    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @param object $args
     *
     *      {
     *          classNameForKind: string
     *          publishedBeforeTimestamp: ?int
     *      }
     *
     * @return object
     *
     *      {
     *          pages: [object]
     *      }
     */
    static function CBAjax_fetchPages(
        $args
    ): stdClass {
        $classNameForKind = CBModel::valueToString(
            $args,
            'classNameForKind'
        );

        $classNameForKindAsSQL = CBDB::stringToSQL(
            $classNameForKind
        );

        $publishedBeforeTimestamp = CBModel::valueAsInt(
            $args,
            'publishedBeforeTimestamp'
        );

        if (empty($publishedBeforeTimestamp)) {
            $publishedBeforeClause = '';
        } else {
            $publishedBeforeClause = "AND `published` < {$publishedBeforeTimestamp}";
        }

        $SQL = <<<EOT

            SELECT      keyValueData

            FROM        ColbyPages

            WHERE       classNameForKind = {$classNameForKindAsSQL} AND
                        published IS NOT NULL
                        {$publishedBeforeClause}

            ORDER BY    published DESC

            LIMIT       10

        EOT;

        return (object)[
            'pages' => CBDB::SQLToArray(
                $SQL,
                [
                    'valueIsJSON' => true,
                ]
            ),
        ];
    }
    /* CBAjax_fetchPages() */



    /**
     * @return string
     */
    static function CBAjax_fetchPages_getUserGroupClassName(): string {
        return 'CBPublicUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v624.css', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v624.js', cbsysurl()),
        ];
    }



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        return [
            [
                'CBPageListView2_currentUserIsDeveloper',
                CBUserGroup::userIsMemberOfUserGroup(
                    ColbyUser::getCurrentUserCBID(),
                    'CBDevelopersUserGroup'
                ),
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBArtworkElement',
            'CBImage',
            'CBUIButton',
            'CBUIPanel',
            'Colby',
        ];
    }



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
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $spec
    ): stdClass {
        return (object)[
            'classNameForKind' => trim(
                CBModel::valueToString(
                    $spec,
                    'classNameForKind'
                )
            ),

            'CSSClassNames' => CBModel::valueToNames(
                $spec,
                'CSSClassNames'
            ),
        ];
    }
    /* CBModel_build() */




    /* -- CBView interfaces -- -- -- -- -- */



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

        $CSSClassNames = CBModel::valueToArray(
            $model,
            'CSSClassNames'
        );

        if (in_array('custom', $CSSClassNames)) {
            /* don't apply any built-in styles */
        } else if (in_array('CBPageListView2_small', $CSSClassNames)) {
            array_push(
                $CSSClassNames,
                'CBPageListView2_small'
            );
        } else {
            array_push(
                $CSSClassNames,
                'CBPageListView2_default'
            );
        }

        array_walk(
            $CSSClassNames,
            'CBHTMLOutput::requireClassName'
        );

        $CSSClassNames = cbhtml(
            implode(
                ' ',
                $CSSClassNames
            )
        );

        ?>

        <div
            class="CBPageListView2 <?= $CSSClassNames ?>"
            data-class-name-for-kind="<?= $model->classNameForKind ?>"
        >
        </div>

        <?php
    }
    /* CBView_render() */

}
