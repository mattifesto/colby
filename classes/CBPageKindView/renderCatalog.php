<section class="CBPageKindView catalog <?= $themeClass ?>">
    <h1>Catalog</h1>
    <pre><?= ColbyConvert::textToHTML(json_encode($dataByYear)) ?></pre>

    <?php

    array_walk($dataByYear, function($dataByMonth, $year) {
        echo "<section><h1>{$year}</h1>";

        array_walk($dataByMonth, function($data, $month) {
            $queryString        = CBRequest::canonicalQueryString([
                ['CBPageKindViewType',  'catalogformonth'],
                ['CBPageKindViewMonth', $data->publishedMonth]
            ]);
            $queryStringAsHTML  = ColbyConvert::textToHTML($queryString);

            echo "<div><a href=\"{$queryStringAsHTML}\">{$month} ({$data->count})</a></div>";
        });

        echo '</section>';
    });

    ?>

</section>
