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
     *          groupName: ?string
     *          subviews: ?[model]
     *      }
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        return (object)[
            'hideFromMembers' => CBModel::valueToBool(
                $spec,
                'hideFromMembers'
            ),

            'hideFromNonmembers' => CBModel::valueToBool(
                $spec,
                'hideFromNonmembers'
            ),

            'groupName' => trim(
                CBModel::valueToString(
                    $spec,
                    'groupName'
                )
            ),

            'subviews' => array_values(
                array_filter(
                    array_map(
                        'CBModel::build',
                        CBModel::valueToArray($spec, 'subviews')
                    )
                )
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
    static function CBModel_upgrade(stdClass $spec): stdClass {
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



    /* -- CBView interfaces -- -- -- -- -- */



    /**
     * @param object $model
     *
     * @return void
     */
    static function CBView_render(stdClass $model): void {
        if (empty($model->subviews)) {
            return;
        }

        if (empty($model->groupName)) {
            $isMember = true;
        } else {
            $isMember = ColbyUser::currentUserIsMemberOfGroup(
                $model->groupName
            );
        }

        if ($isMember) {
            if (!empty($model->hideFromMembers)) {
                return;
            }
        } else if (!empty($model->hideFromNonmembers)) {
            return;
        }

        array_walk(
            $model->subviews,
            'CBView::render'
        );
    }
    /* CBView_render() */

}
