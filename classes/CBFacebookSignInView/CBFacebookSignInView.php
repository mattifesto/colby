<?php

final class CBFacebookSignInView {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [Colby::flexpath(__CLASS__, 'css', cbsysurl())];
    }

    /**
     * @param model $spec
     *
     * @return ?model
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        return (object)[];
    }

    /**
     * @param model $model
     *
     * @return void
     */
    static function CBView_render(stdClass $model): void {
        ?>

        <div class="CBFacebookSignInView">
            <a class="button" href="<?= CBFacebook::loginURL() ?>">
                Sign in with Facebook
            </a>
        </div>

        <?php
    }
}
