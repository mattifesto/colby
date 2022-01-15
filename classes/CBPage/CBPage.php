<?php

final class
CBPage {

    /* -- functions -- -- -- -- -- */



    /**
     * @TODO 2019_07_05
     *
     *      CBPage::render() should be modified to have this behavior.
     *
     *      CBPage::render() will either completely finish writing a page to the
     *      buffer or it will write nothing to the buffer and throw an
     *      exception.
     *
     *      CBPage::render() takes care all of the CBHTMLOutput
     *      responsibilities. The caller should not, and the implementers of
     *      CBPage_render() should not.
     *
     *      If CBPage::render() throws an exception the first time, feel free to
     *      call it a second time with a different model to render an error
     *      page.
     *
     *      If CBPage::render() throws an exception the second time, you should
     *      manually render content to the buffer because there is something
     *      serious going on.
     *
     * @NOTE
     *
     *      These changes will require some simplifications to CBHTMLOutput
     *      also.
     *
     * @param object $model
     *
     * @return void
     */
    static function
    render(
        stdClass $pageModel
    ): void {
        $className = CBModel::valueToString(
            $pageModel,
            'className'
        );

        $functionName = "{$className}::CBPage_render";

        if (
            is_callable(
                $functionName
            )
        ) {
            CBHTMLOutput::requireClassName(
                $className
            );

            call_user_func(
                $functionName,
                $pageModel
            );
        } else {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The class ({$className}) for this page model has not
                    implemented CBPage_render()

                EOT),
                $pageModel,
                '5b1831c54024accfe5ed6a4c4eb2d724a5d19845'
            );
        }
    }
    /* render() */



    /**
     * @param object $spec
     *
     * @return void
     */
    static function renderSpec(stdClass $spec): void {
        CBPage::render(
            CBModel::build($spec)
        );
    }



    /**
     * A summary object is a summary of a page model. Summaries are use to
     * create lists of pages. The summary object will be stored in the
     * ColbyPages table. The object is not technically a model because:
     *
     *      - it does not have a className property
     *      - its ID is the ID of an already existing model
     *
     *
     * Summary object properties that may be set by this function.
     *
     *      className
     *
     *          This property will be unset if it is set by an implementer of
     *          CBPage_toSummary().
     *
     *
     *      ID: hex160
     *
     *          The ID of the page model. Implementers of CBPage_toSummary()
     *          do not need to set this property, it will be set by this
     *          function regardless.
     *
     *      title: string
     *
     *          If an implementer of CBPage_toSummary() doesn't set this
     *          property, the string value of the page model's title property
     *          will be set as the title property on the summary.
     *
     *
     * Summary object properties recommended to be set by implementers of
     * CBPage_toSummary(). An unset value is valid.
     *
     *      title: string
     *      description: string
     *      URI: string
     *
     *      created: int (timestamp)
     *      updated: int (timestamp)
     *
     *      isPublished: bool
     *      publicationTimeStamp: int (timestamp)
     *
     *      image: model (CBImage)
     *      thumbnailURL: string
     *
     *
     * Topic: image and thumbnailURL
     *
     *      The "image" and "thumbnailURL" properties work together to specify
     *      an image that represents a page. Most of the time, "image" is used.
     *      But a CBImage model can only represent a local Colby image. If a
     *      site needs to represent the page with an image on another site, then
     *      "thumbnailURL" will be used.
     *
     *      Only one of the two properties should ever be set but if both are
     *      set, "image" takes priority.
     *
     *      The functionality of CBImage is much greater and more flexible that
     *      an image URL. The end goal is to move all images to CBImage. In the
     *      future, CBImage may be able to specify a site where the image
     *      resides. Alternatively, all uses of thumbnailURL may be removed. In
     *      either case, at that point, thumbnailURL should be removed.
     *
     *      This is the official documentation location for the concept of
     *      "image" and "thumbnailURL".
     *
     *
     * @NOTE 2018.02.08
     *
     *      The "updated" property should be renamed to "modified". This is
     *      related to the update property on the CBViewPage model.
     *
     *      The "publicationTimeStamp" property should be renamed to
     *      "publicationTimestamp". This is related to the
     *      "publicationTimeStamp" property on the CBViewPage model.
     *
     *
     * @return object
     */
    static function toSummary(stdClass $pageModel): stdClass {
        $className = CBModel::valueToString(
            $pageModel,
            'className'
        );

        $function = "{$className}::CBPage_toSummary";

        if (is_callable($function)) {
            $summary = call_user_func(
                $function,
                $pageModel
            );
        } else {
            $summary = (object)[];
        }

        $CBID = CBModel::valueAsCBID(
            $pageModel,
            'ID'
        );

        if ($CBID !== null) {
            $summary->ID = $CBID;
        }

        $summary->title = CBModel::valueToString(
            $pageModel,
            'title'
        );

        unset($summary->className);

        return $summary;
    }
    /* toSummary() */

}
