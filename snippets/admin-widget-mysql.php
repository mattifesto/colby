<section class="widget">
    <header><h1>MySQL</h1></header>

    <div class="version-numbers">
        <section class="version-number">
            <h1>Version</h1>
            <div class="number"><?= CBDB::SQLToValue('SELECT @@version') ?></div>
        </section>
    </div>
</section>
