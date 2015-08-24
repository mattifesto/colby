<section class="CBPageKindLibraryView library">

    <header class="CBTextBoxView <?= $headerThemeClass ?>  ">
        <h1>Library</h1>
        <div>
            <nav>
                <a href="<?= $URLAsHTML, ColbyConvert::textToHTML(CBRequest::canonicalQueryString()); ?>">Most Recent</a>
            </nav>
        </div>
    </header>

    <?php

    array_walk($dataByYear, function($dataByMonth, $year) use ($yearThemeClass, $URLAsHTML) { ?>

        <section class="CBTextBoxView year <?= $yearThemeClass ?>">
            <h1><?= $year ?></h1>
            <div><?php

                array_walk($dataByMonth, function($data, $month) use ($URLAsHTML) {
                    $queryString        = CBRequest::canonicalQueryString([
                        ['CBPageKindLibraryViewType',  'month'],
                        ['CBPageKindLibraryViewMonth', $data->publishedMonth]
                    ]);
                    $queryStringAsHTML  = ColbyConvert::textToHTML($queryString);
                    $dateTime           = DateTime::createFromFormat('!m', $month);
                    $monthNameAsHTML    = $dateTime->format('F');
                    echo "<div><a href=\"{$URLAsHTML}{$queryStringAsHTML}\">{$monthNameAsHTML}</a> <span>({$data->count})</span></div>";
                });

            ?></div>
        </section>

    <?php }); ?>

</section>
