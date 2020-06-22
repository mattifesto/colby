<?php

final class CBUser_ResetPasswordView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v622.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
            'CBUIPanel',
            'CBUIStringEditor',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $viewSpec
     *
     * @return void
     */
    static function CBModel_build(
        stdClass $viewSpec
    ): stdClass {
        return (object)[
            'userEmailAddress' => CBModel::valueAsEmail(
                $viewSpec,
                'userEmailAddress'
            ),
        ];
    }



    /* -- CBView interfaces -- -- -- -- -- */



    /**
     * @param object $viewModel
     *
     * @return void
     */
    static function CBView_render(
        stdClass $viewModel
    ): void {
        /**
         * We don't validate the email address in this function. If someone
         * wants to pass in something wacky this is not the place to prevent
         * that.
         */
        $userEmailAddress = CBModel::valueToString(
            $viewModel,
            'userEmailAddress'
        );

        ?>

        <div
            class="CBUser_ResetPasswordView"
            data-user-email-address="<?= cbhtml($userEmailAddress) ?>"
        >
        </div>

        <?php
    }
    /* CBView_render() */

}
