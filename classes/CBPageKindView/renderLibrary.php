<section class="CBPageKindView library <?= $themeClass ?>">
    <h1>Library</h1>

    <?php

    array_walk($dataByYear, function($dataByMonth, $year) {
        echo "<section><h1>{$year}</h1>";

        array_walk($dataByMonth, function($data, $month) {
            $queryString        = CBRequest::canonicalQueryString([
                ['CBPageKindViewType',  'month'],
                ['CBPageKindViewMonth', $data->publishedMonth]
            ]);
            $queryStringAsHTML  = ColbyConvert::textToHTML($queryString);

            echo "<div><a href=\"{$queryStringAsHTML}\">{$month} ({$data->count})</a></div>";
        });

        echo '</section>';
    });

    ?>

</section>
