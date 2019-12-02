<?php

/**
 * This class renders a view the displays a toggle that calls an Ajax function
 * that will toggle a speific user's membership in a specific user group.
 */
final class CBUserGroupMembershipToggleView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v555.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
            'CBUIBooleanSwitchPart',
            'Colby',
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
        $userGroupClassName = CBModel::valueAsName(
            $viewModel,
            'userGroupClassName'
        );

        if ($userGroupClassName === null) {
            throw new CBExceptionWithValue(
                'The "userGroupClassName" property of this model is not valid.',
                $viewModel,
                'ddeea848c9367902f5fa40b88e9939acc18497e3'
            );
        }

        $userCBID = CBModel::valueAsID(
            $viewModel,
            'userCBID'
        );

        if ($userCBID === null) {
            throw new CBExceptionWithValue(
                'The "userCBID" property of this model is not valid.',
                $viewModel,
                '24cb524cfffb743c2ca1228292bdccbb6b104577'
            );
        }

        /**
         * @NOTE
         *
         *      Because $userGroupClassName and $userCBID were accessed via
         *      specific valueAs functions, they are already HTML safe and don't
         *      need to be escaped.
         */

        ?>

        <div
            class="CBUserGroupMembershipToggleView"
            data-user-group-class-name="<?= $userGroupClassName ?>"
            data-user-c-b-i-d="<?= $userCBID ?>"
        >
        </div>

        <?php
    }
    /* CBView::render() */

}
