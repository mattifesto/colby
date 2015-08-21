<section class="CBPageKindView library <?= $themeClass ?>">
    <header>
        <h1>Library</h1>
        <nav>
            <a href="<?= ColbyConvert::textToHTML(CBRequest::canonicalQueryString()); ?>">Most Recent</a>
        </nav>
    </header>


    <?php

    array_walk($dataByYear, function($dataByMonth, $year) {
        echo "<section class=\"year\"><h1>{$year}</h1><div>";

        array_walk($dataByMonth, function($data, $month) {
            $queryString        = CBRequest::canonicalQueryString([
                ['CBPageKindViewType',  'month'],
                ['CBPageKindViewMonth', $data->publishedMonth]
            ]);
            $queryStringAsHTML  = ColbyConvert::textToHTML($queryString);

            echo "<div><a href=\"{$queryStringAsHTML}\">{$month} ({$data->count})</a></div>";
        });

        echo '</div></section>';
    });

    ?>

</section>
