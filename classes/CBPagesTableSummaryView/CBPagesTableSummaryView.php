<?php

final class CBPagesTableSummaryView {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [Colby::flexpath(__CLASS__, 'v374.css', cbsysurl())];
    }

    /**
     * @return void
     */
    static function CBView_render(stdClass $model = null) {
        $publishedClause = ($model->type === 'published') ?
            '`published` IS NOT NULL' : '`published` IS NULL';
        $titleAsHTML = ($model->type === 'published') ?
            'Published Pages' : 'Unpublished Pages';

        ?>

        <div class="CBPagesTableSummaryView">
            <div>
                <h1><?= $titleAsHTML ?></h1>

                <?php

                $SQL = <<<EOT

                    SELECT      `className`,
                                `classNameForKind`,
                                count(*) AS `count`
                    FROM        `ColbyPages`
                    WHERE       {$publishedClause}
                    GROUP BY    `className`, `classNameForKind`

EOT;

                $result = Colby::query($SQL);

                while ($row = $result->fetch_object()) {
                    $className = ($row->className !== null) ? $row->className : 'NULL';
                    $classNameForKind = ($row->classNameForKind !== null) ? $row->classNameForKind : 'NULL';

                    ?>

                    <div class="nameset">
                        <div class="classNames">
                            <div class="data className"><?= $className ?></div>
                            <div class="data classNameForKind"><?= $classNameForKind ?></div>
                        </div>
                        <div class="data count"><?= $row->count ?></div>
                    </div>

                    <?php

                }

                $result->free();

                ?>

            </div>
        </div>

        <?php
    }
}
