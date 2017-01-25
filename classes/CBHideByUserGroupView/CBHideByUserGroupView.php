<?php

final class CBHideByUserGroupView {

    /**
     * @param [stdClass]? $model->subviews
     *
     * @return string
     */
    static function modelToSearchText(stdClass $model) {
        if (!empty($model->subviews)) {
            return implode(' ', array_map('CBView::modelToSearchText', $model->subviews));
        } else {
            return '';
        }
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
    static function renderModelAsHTML(stdClass $model) {
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

        array_walk($model->subviews, 'CBView::renderModelAsHTML');
    }

    /**
     * @param bool? $spec->hideFromMembers
     * @param bool? $spec->hideFromNonmembers
     * @param string? $spec->groupName
     * @param [stdClass]? $spec->subviews
     *
     * @return stdClass
     */
    static function specToModel(stdClass $spec) {
        return (object)[
            'className' => __CLASS__,
            'hideFromMembers' => CBModel::value($spec, 'hideFromMembers', false, 'boolval'),
            'hideFromNonmembers' => CBModel::value($spec, 'hideFromNonmembers', false, 'boolval'),
            'groupName' => CBModel::value($spec, 'groupName', '', 'trim'),
            'subviews' => CBModel::value($spec, 'subviews', [], function ($subviews) {
                return array_map('CBView::specToModel', $subviews);
            }),
        ];
    }
}
