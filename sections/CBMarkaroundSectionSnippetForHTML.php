<?php

    $ID = "s{$sectionModel->sectionID}";

?>

<section id="<?php echo $ID; ?>">
    <style scoped>

        <?php

        echo <<<EOT

            #{$ID} h1
            {
                font-size: 3em;
            }

            #{$ID} h2
            {
                font-size: 2em;
            }

EOT;

        ?>

    </style>
    <header>

        <?php

        if ($sectionModel->heading1HTML)
        {
            echo "<h1>{$sectionModel->heading1HTML}</h1>\n";
        }

        if ($sectionModel->heading2HTML)
        {
            echo "<h2>{$sectionModel->heading2HTML}</h2>\n";
        }

        ?>

    </header>

    <?php

    if ($sectionModel->contentHTML)
    {
        echo $sectionModel->contentHTML;
    }

    ?>

</section>
