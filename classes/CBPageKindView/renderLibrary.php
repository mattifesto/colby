<section class="CBPageKindView library <?= $themeClass ?>">
    <header>
        <h1>Library</h1>
        <nav>
            <a href="<?= $URLAsHTML, ColbyConvert::textToHTML(CBRequest::canonicalQueryString()); ?>">Most Recent</a>
        </nav>
    </header>


    <?php

    array_walk($dataByYear, function($dataByMonth, $year) use ($URLAsHTML) {
        echo "<section class=\"year\"><h1>{$year}</h1><div>";

        array_walk($dataByMonth, function($data, $month) use ($URLAsHTML) {
            $queryString        = CBRequest::canonicalQueryString([
                ['CBPageKindViewType',  'month'],
                ['CBPageKindViewMonth', $data->publishedMonth]
            ]);
            $queryStringAsHTML  = ColbyConvert::textToHTML($queryString);
            $dateTime           = DateTime::createFromFormat('!m', $month);
            $monthNameAsHTML    = $dateTime->format('F');
            echo "<div><a href=\"{$URLAsHTML}{$queryStringAsHTML}\">{$monthNameAsHTML}</a> <span>({$data->count})</span></div>";
        });

        echo '</div></section>';
    });

    ?>

</section>
