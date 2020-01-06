<?php

final class CBCurrentUserView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v564.js', cbsysurl()),
        ];
    }



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        $currentUserCBID = ColbyUser::getCurrentUserCBID();
        $currentUserFullName = null;
        $currentUserEmail = null;

        if ($currentUserCBID !== null) {
            $currentUserModel = CBModelCache::fetchModelByID(
                $currentUserCBID
            );

            $currentUserFullName = CBModel::valueToString(
                $currentUserModel,
                'title'
            );

            $currentUserEmail = CBModel::valueToString(
                $currentUserModel,
                'email'
            );
        }

        return [
            [
                'CBCurrentUserView_initialUserFullName',
                $currentUserFullName,
            ],
            [
                'CBCurrentUserView_initialUserEmail',
                $currentUserEmail,
            ],
            [
                'CBCurrentUserView_userCBID',
                $currentUserCBID,
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBErrorHandler',
            'CBUI',
            'CBUIPanel',
            'CBUIPasswordEditor',
            'CBUIStringEditor',
            'Colby',

            'CBContentStyleSheet',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $spec
    ): stdClass {
        return (object)[];
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
        ?>

        <div class="CBCurrentUserView">
        </div>

        <?php
    }
    /* CBView_render() */

}
