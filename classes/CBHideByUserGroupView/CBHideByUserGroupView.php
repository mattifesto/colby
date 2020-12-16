<?php

final class CBHideByUserGroupView {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBViewCatalog::installView(
            __CLASS__
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBViewCatalog',
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     *      {
     *          hideFromMembers: ?bool
     *          hideFromNonmembers: ?bool
     *          subviews: ?[model]
     *
     *          userGroupClassName: ?string
     *
     *              A value that returns null from CBConvert::valueAsName()
     *              means that the subviews will not be shown to any users. This
     *              is the default.
     *      }
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $spec
    ): stdClass {
        return (object)[
            'hideFromMembers' => CBModel::valueToBool(
                $spec,
                'hideFromMembers'
            ),

            'hideFromNonmembers' => CBModel::valueToBool(
                $spec,
                'hideFromNonmembers'
            ),

            'subviews' => array_values(
                array_filter(
                    array_map(
                        'CBModel::build',
                        CBModel::valueToArray($spec, 'subviews')
                    )
                )
            ),

            'userGroupClassName' => CBModel::valueAsName(
                $spec,
                'userGroupClassName'
            ),
        ];
    }
    /* CBModel_build() */



    /**
     * @param object $model
     *
     * @return string
     */
    static function CBModel_toSearchText(stdClass $model): string {
        return implode(
            ' ',
            array_map(
                'CBModel::toSearchText',
                CBModel::valueToArray($model, 'subviews')
            )
        );
    }



    /**
     * @param object $spec
     *
     * @return object
     */
    static function
    CBModel_upgrade(
        stdClass $spec
    ): stdClass {
        $spec->subviews = array_values(
            array_filter(
                array_map(
                    'CBModel::upgrade',
                    CBModel::valueToArray($spec, 'subviews')
                )
            )
        );

        return $spec;
    }
    /* CBModel_upgrade() */



    /* -- CBView interfaces -- -- -- -- -- */



    /**
     * @param object $model
     *
     * @return void
     */
    static function CBView_render(stdClass $model): void {

        $userGroupClassName = CBModel::valueAsName(
            $model,
            'userGroupClassName'
        );

        /**
         * If no user group class name has been set, the subviews are not
         * rendered.
         */
        if ($userGroupClassName === null) {
            return;
        }

        $subviews = CBModel::valueToArray(
            $model,
            'subviews'
        );

        if (empty($subviews)) {
            return;
        }

        $isMember = CBUserGroup::userIsMemberOfUserGroup(
            ColbyUser::getCurrentUserCBID(),
            $userGroupClassName
        );

        if ($isMember) {
            if (!empty($model->hideFromMembers)) {
                return;
            }
        } else if (!empty($model->hideFromNonmembers)) {
            return;
        }

        array_walk(
            $model->subviews,
            function ($subviewModel) {
                CBView::render($subviewModel);
            }
        );
    }
    /* CBView_render() */

}
