<?php

final class CBSubviewUpdater {

    /**
     * This function will search the subviews of the $view parameter for a
     * subview with a $key property value equal to $value using the equal
     * comparison operator (==).
     *
     * If a subview is found, it will be updated by having the $subview
     * parameter merged with it.
     *
     * If a subview is not found, the $subview parameter will be unshifted onto
     * the $view parameter's array of views.
     *
     * @param object $view
     *
     *      The view whose subviews will be updated.
     *
     * @param string $key
     *
     *      The first subview found with its $key property value matching the
     *      $value parameter will be updated.
     *
     * @param mixed $value
     *
     * @param object $subview
     *
     *      This object provides either updates to an existing subview or
     *      becomes a new subview of the $view parameter. It should have a
     *      className property.
     *
     * @return void
     */
    static function unshift(
        stdClass $view,
        string $key,
        /* mixed */ $value,
        stdClass $subview
    ): void {
        $originalSubview = CBView::findSubview($view, $key, $value);

        if (empty($originalSubview)) {
            $subviews = CBView::getSubviews($view);

            array_unshift($subviews, $subview);

            CBView::setSubviews($subviews);
        } else {
            CBModel::merge($originalSubview, $subview);
        }
    }
}
