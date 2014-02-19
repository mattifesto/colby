<?php

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


CBHTMLOutput::setTitleHTML('PHP Information');
CBHTMLOutput::setDescriptionHTML('Information about the version and setup of PHP running for this website.');
CBHTMLOutput::begin();


if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    include Colby::findSnippet('authenticate.php');

    goto done;
}


include CBSystemDirectory . '/sections/admin-page-header.php';

$selectedMenuItemID     = 'develop';
$selectedSubmenuItemID  = 'php';

include CBSystemDirectory . '/sections/admin-page-menu.php';

$iniValues = ini_get_all(null, false);

?>

<main>
    <style scoped>

        table.phpini
        {
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

done:

include CBSystemDirectory . '/sections/admin-page-footer.php';

CBHTMLOutput::render();
