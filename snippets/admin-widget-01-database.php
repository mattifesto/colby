<section class="widget">
    <header><h1>Colby System</h1></header>
    <div class="version-numbers">

        <?php

        $sql = 'SELECT ColbySchemaVersionNumber() AS `schemaVersionNumber`';

        $result = Colby::query($sql);

        $schemaVersionNumber = $result->fetch_object()->schemaVersionNumber;

        $result->free();

        ?>

        <section class="version-number">
            <h1>Version</h1>
            <div class="number"><?php echo COLBY_VERSION_NUMBER; ?></div>
        </section>

        <section class="version-number">
            <h1>Database Version</h1>
            <div class="number"><?php echo $schemaVersionNumber; ?></div>
        </section>
    </div>
</section>
