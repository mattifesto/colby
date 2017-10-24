<?php

if (!ColbyUser::current()->isOneOfThe('Developers')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('PHP Information');
CBHTMLOutput::setDescriptionHTML('Information about the version and setup of PHP running for this website.');

CBView::render((object)[
    'className' => 'CBAdminPageMenuView',
    'selectedMenuItemName' => 'develop',
    'selectedSubmenuItemName' => 'php',
]);

$iniValues = ini_get_all(null, false);

?>

<main>
    <style>
        .phpini > .row {
            display: flex;
        }

        .phpini > .row > * {
            box-sizing: border-box;
            overflow-wrap: break-word;
            padding: 0px 10px;
            width: 50%;
        }

        .phpini > .row > :first-child {
            font-weight: bold;
            text-align: right;
        }

        .phpini .empty {
            color: hsl(0, 0%, 70%);
        }

        @media (max-width: 735px) {
            .phpini > .row {
                display: block;
                padding-bottom: 5px;
            }

            .phpini > .row > * {
                width: auto;
            }

            .phpini > .row > :first-child {
                text-align: left;
            }
        }
    </style>
    <div class="phpini">
        <div class="row">
            <div>Version</div>
            <div><?= phpversion() ?></div>
        </div>
        <div class="row">
            <div>username</div>
            <div><?= cbhtml(`whoami`) ?></div>
        </div>

        <?php

        foreach ($iniValues as $key => $value) {
            $keyHTML = ColbyConvert::textToHTML($key);

            if (empty($value)) {
                $valueHTML = '<span class="empty">empty</span>';
            } else {
                $valueHTML = ColbyConvert::textToHTML($value);
            }

            echo <<<EOT

                <div class="row">
                    <div>{$keyHTML}</div>
                    <div>{$valueHTML}</div>
                </div>

EOT;
        }

        ?>

    </div>
</main>

<?php

CBView::render((object)[
    'className' => 'CBAdminPageFooterView',
]);

CBHTMLOutput::render();
