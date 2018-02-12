<?php

final class CBHideByUserGroupView {

    /**
     * @param object $model
     *
     * @return string
     */
    static function CBModel_toSearchText(stdClass $model) {
        $subviews = CBModel::valueAsObjects($model, 'subviews');
        $strings = array_map('CBModel::toSearchText', $subviews);
        $strings = array_filter($strings);
        return implode(' ', $strings);
    }

    /**
     * @param bool? $spec->hideFromMembers
     * @param bool? $spec->hideFromNonmembers
     * @param string? $model->groupName
     *      An empty group name refers to all visitors.
     * @param [stdClass]? $model->subviews
     *
     * @return null
     */
    static function CBView_render(stdClass $model) {
        if (empty($model->subviews)) {
            return;
        }

        if (empty($model->groupName)) {
            $isMember = true;
        } else {
            $isMember = ColbyUser::currentUserIsMemberOfGroup($model->groupName);
        }

        if ($isMember) {
            if (!empty($model->hideFromMembers)) {
                return;
            }
        } else if (!empty($model->hideFromNonmembers)) {
            return;
        }

        array_walk($model->subviews, 'CBView::render');
    }

    /**
     * @param model $spec
     *
     *      {
     *          hideFromMembers: ?bool
     *          hideFromNonmembers: ?bool
     *          groupName: ?string
     *          subviews: ?[model]
     *      }
     *
     * @return ?model
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        return (object)[
            'hideFromMembers' => CBModel::value($spec, 'hideFromMembers', false, 'boolval'),
            'hideFromNonmembers' => CBModel::value($spec, 'hideFromNonmembers', false, 'boolval'),
            'groupName' => trim(CBModel::valueToString($spec, 'groupName')),
        ];

        /* subviews */

        $model->subviews = [];
        $subviewSpecs = CBModel::valueToArray($spec, 'subviews');

        foreach($subviewSpecs as $subviewSpec) {
            if ($subviewModel = CBModel::build($subviewSpec)) {
                $model->subviews[] = $subviewModel;
            }
        }

        return $model;
    }
}
