<?php

final class
CB_View_SVGBarChart1
{
    // -- CBAdmin_CBDocumentationForClass interfaces



    /**
     * This function renders some key samples of this view used for develoopment
     * purposes.
     *
     * @return void
     */
    static function
    CBAdmin_CBDocumentationForClass_render()
    : void
    {
        $spec = CBModel::createSpec(
            'CB_View_SVGBarChart1'
        );

        CB_View_SVGBarChart1::setTitles(
            $spec,
            [
                '06-19', '06-20', '06-21', '06-22', '06-23',
                '06-24', '06-25', '06-26', '06-27', '06-28',
                '06-29', '06-30', '07-01', '07-02', '07-03',
                '07-04', '07-05', '07-06', '07-07', '07-08',
                '07-09', '07-10', '07-11', '07-12', '07-13',
                '07-14', '07-15', '07-16',
            ]
        );

        CB_View_SVGBarChart1::setValues(
            $spec,
            [
                null, null, 5, 5, 5,
                10, 5, 1, 8, 9,
                10, 11, 12, 13, 14,
                10, 11, 12, 13, 14,
                10, 11, 12, 13, 14,
                15, 16, 17,
            ]
        );

        CBView::renderSpec($spec);

        $spec = CBModel::createSpec(
            'CB_View_SVGBarChart1'
        );

        CB_View_SVGBarChart1::setTitles(
            $spec,
            [
                '06-19', '06-20', '06-21', '06-22', '06-23',
                '06-24', '06-25', '06-26', '06-27', '06-28',
                '06-29', '06-30', '07-01', '07-02', '07-03',
                '07-04', '07-05', '07-06', '07-07', '07-08',
                '07-09', '07-10', '07-11', '07-12', '07-13',
                '07-14', '07-15', '07-16',
            ]
        );

        CB_View_SVGBarChart1::setValues(
            $spec,
            [
                null, null, null, null, null,
                null, null, null, null, null,
                null, null, null, null, null,
                null, null, null, null, null,
                null, null, 702000, 702000, 702000,
                703000, 703000, 703000,
            ]
        );

        CBView::renderSpec($spec);
    }
    // CBAdmin_CBDocumentationForClass_render()



    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        $arrayOfCSSURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_11_08_1667914714',
                'css',
                cbsysurl()
            ),
        ];

        return $arrayOfCSSURLs;
    }
    // CBHTMLOutput_CSSURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        $arrayOfJavaScriptURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                'v2023.2.4',
                'js',
                cbsysurl()
            ),
            'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js',
        ];

        return $arrayOfJavaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        $arrayOfRequiredClassNames =
        [
            'CBJavaScript',
        ];

        return $arrayOfRequiredClassNames;
    }
    // CBHTMLOutput_requiredClassNames()



    // -- CBModel interfaces



    /**
     * @param object $spec
     *
     * @return ?object
     */
    static function
    CBModel_build(
        stdClass $viewSpec
    ): ?stdClass
    {
        $viewModel =
        (object)[];

        CB_View_SVGBarChart1::setTitles(
            $viewModel,
            CB_View_SVGBarChart1::getTitles(
                $viewSpec
            )
        );

        CB_View_SVGBarChart1::setValues(
            $viewModel,
            CB_View_SVGBarChart1::getValues(
                $viewSpec
            )
        );

        return $viewModel;
    }
    // CBModel_build()



    // -- accessors



    /**
     * @param object $viewModel
     *
     * @return [string]
     */
    static function
    getTitles(
        stdClass $viewModel
    ): array
    {
        $titles =
        CBModel::valueToArray(
            $viewModel,
            'CB_View_SVGBarChart1_titles_property'
        );

        return $titles;
    }
    // getTitles()



    /**
     * @param object $viewModel
     * @param [string] $newTalues
     *
     * @return void
     */
    static function
    setTitles(
        stdClass $viewModel,
        array $newTitles
    ): void
    {
        $viewModel->CB_View_SVGBarChart1_titles_property =
        $newTitles;
    }
    // setTitles()



    /**
     * @param object $viewModel
     *
     * @return [float]
     */
    static function
    getValues(
        stdClass $viewModel
    ): array
    {
        $values =
        CBModel::valueToArray(
            $viewModel,
            'CB_View_SVGBarChart1_values_property'
        );

        return $values;
    }
    // getValues()



    /**
     * @param object $viewModel
     * @param [float] $newValues
     *
     * @return void
     */
    static function
    setValues(
        stdClass $viewModel,
        array $newValues
    ): void
    {
        $viewModel->CB_View_SVGBarChart1_values_property =
        $newValues;
    }
    // setValues()



    /**
     * @param model $model
     *
     * @return void
     */
    static function
    CBView_render(
        stdClass $viewModel
    ): void
    {
        $values =
        CB_View_SVGBarChart1::getValues(
            $viewModel
        );

        $titles =
        CB_View_SVGBarChart1::getTitles(
            $viewModel
        );

        $valuesCount =
        count($values);

        $thereIsNothingToRender =
        $valuesCount <
        1;

        if (
            $thereIsNothingToRender
        ) {
            return;
        }

        $graphHeightAsPixels =
        100;

        $valuesAsJSONAsHTML =
        cbhtml(
            json_encode(
                $values
            )
        );

        $titlesAsJSONAsHTML =
        cbhtml(
            json_encode(
                $titles
            )
        );

        echo <<<EOT

            <div
                class="CB_View_SVGBarChart1_root_element"
                data-values="{$valuesAsJSONAsHTML}"
                data-titles="{$titlesAsJSONAsHTML}"
            >
                <div class="CB_View_SVGBarChart1_content_element">
                    <canvas
                        class="CB_View_SVGBarChart1_chartjs_container_element"
                    >
                    </canvas>
                </div>
            </div>

        EOT;
    }
    // CBView_render()

}
