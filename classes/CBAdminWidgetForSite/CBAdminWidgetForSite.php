<?php

final class CBAdminWidgetForSite {

    /**
     * @return null
     */
    public static function render() {
        $version = CBSiteVersionNumber;
        ?>
        <section class="widget">
            <header><h1>Website</h1></header>

            <div class="version-numbers">
                <section class="version-number">
                    <h1>Version</h1>
                    <div class="number"><?= $version ?></div>
                </section>
            </div>
        </section>
        <?php
    }
}
