<?php

if (!ColbyUser::current()->isOneOfThe('Developers')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('PHP Information');
CBHTMLOutput::setDescriptionHTML('Information about the version and setup of PHP running for this website.');

$selectedMenuItemID     = 'develop';
$selectedSubmenuItemID  = 'php';

include CBSystemDirectory . '/sections/admin-page-menu.php';

$iniValues = ini_get_all(null, false);

?>

<main>
    <style>

        table.phpini
        {
            font-family:    "Source Sans Pro";
            margin:         0px auto;
            table-layout:   fixed;
            width:          800px;
        }

        table.phpini td,
        table.phpini th,
        table.phpini tr
        {
            padding:    0px 5px;
            width:      50%;
        }

    </style>
    <table class="phpini">
        <tbody>
            <tr>
                <th style="text-align: right;">Version</th>
                <td><?= phpversion() ?></td>
            </tr>
            <tr>
                <th style="text-align: right;">username</th>
                <td><?= cbhtml(`whoami`) ?></td>
            </tr>

            <?php

            foreach ($iniValues as $key => $value)
            {
                if (empty($value))
                {
                    $value = '<no value>';
                }

                $keyHTML = ColbyConvert::textToHTML($key);
                $valueHTML = ColbyConvert::textToHTML($value);

                echo <<<EOT

                    <tr>
                        <th style="text-align: right;">{$keyHTML}</th>
                        <td>{$valueHTML}</td>
                    </tr>

EOT;
            }

            ?>

        </tbody>
    </table>
</main>

<?php

include CBSystemDirectory . '/sections/admin-page-footer-2.php';

CBHTMLOutput::render();
