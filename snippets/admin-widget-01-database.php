<section class="widget">
    <header><h1>System</h1></header>
    <div class="version-numbers">

        <?php

        $tuple                  = CBDictionaryTuple::initWithKey('CBSystemVersionNumber');
        $DBSystemVersionNumber  = $tuple->value;

        ?>

        <section class="version-number">
            <h1>Version</h1>
            <div class="number"><?php echo CBSystemVersionNumber ?></div>
        </section>

        <section class="version-number">
            <h1>Database Version</h1>
            <div class="number"><?php echo $DBSystemVersionNumber; ?></div>
        </section>
    </div>
</section>
