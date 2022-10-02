<?php

final class
CB_View_SVGBarChart1
{
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        $cssURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_09_15_1663204595',
                'css',
                cbsysurl()
            ),
        ];

        return $cssURLs;
    }
    // CBHTMLOutput_CSSURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        $javaScriptURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_10_02_1664751254',
                'js',
                cbsysurl()
            ),
        ];

        return $javaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        $requiredClassNames =
        [
            'CBJavaScript',
        ];

        return $requiredClassNames;
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

        echo <<<EOT

            <div class="CB_View_SVGBarChart1_root_element">
                <div class="CB_View_SVGBarChart1_content_element">
                    <svg
                        class="CB_View_SVGBarChart1_svg_element"
                        style="width: 280px;"
                        viewBox="0 0 280 ${graphHeightAsPixels}"
                    >

        EOT;


        $unitValues =
        CB_View_SVGBarChart1::calculateUnitValues(
            $values
        );


        $columnIndex =
        0;

        $previousUnitValue =
        null;

        for (
            $loopIndex
            = 0;

            $loopIndex
            < count($values);

            $loopIndex
            += 1
        ) {
            $loopUnitValue
            = $unitValues[
                $loopIndex
            ];

            if (
                isset(
                    $titles[
                        $loopIndex
                    ]
                )
            ) {
                $loopTitle =
                $titles[
                    $loopIndex
                ];
            }

            else
            {
                $loopTitle
                = '';
            }

            $barColor =
            CB_View_SVGBarChart1::calculateBarColor(
                $loopIndex,
                $loopUnitValue,
                $previousUnitValue
            );

            $columnHeightAsPixels
            = $graphHeightAsPixels
            * $loopUnitValue;

            $columnContainerWidthAsPixels =
            10;

            $columnWidthAsPixels =
            6;

            $columnXAsPixels =
            (
                $columnIndex *
                $columnContainerWidthAsPixels
            ) +
            (
                (
                    $columnContainerWidthAsPixels -
                    $columnWidthAsPixels
                ) /
                2
            );

            $columnYAsPixels =
            $graphHeightAsPixels -
            $columnHeightAsPixels;

            echo <<<EOT

                <rect
                    class="CB_View_SVGBarChart1_barBackground_element"
                    fill="#333"
                    height="${graphHeightAsPixels}"
                    width="${columnWidthAsPixels}"
                    x="${columnXAsPixels}"
                    y="0"
                />

                <rect
                    class="CB_View_SVGBarChart1_bar_element"
                    fill="${barColor}"
                    height="${columnHeightAsPixels}"
                    width="${columnWidthAsPixels}"
                    x="${columnXAsPixels}"
                    y="${columnYAsPixels}"
                />

                <rect
                    class="CB_View_SVGBarChart1_transparentFullBar_element"
                    data-value="${loopTitle}"
                    fill="transparent"
                    height="${graphHeightAsPixels}"
                    width="${columnWidthAsPixels}"
                    x="${columnXAsPixels}"
                    y="0"
                />

            EOT;

            $previousUnitValue =
            $loopUnitValue;

            $columnIndex +=
            1;
        }

        echo <<<EOT

                    </svg>
                    <div
                        class="CB_View_SVGBarChart1_currentValue_element"
                    >
                        &nbsp;
                    </div>
                </div>
            </div>

        EOT;
    }
    // CBView_render()



    // -- functions



    private static function
    calculateBarColor(
        int $barIndex,
        ?float $currentValue,
        ?float $previousValue
    ): string
    {
        if (
            $barIndex
            === 0
        ) {
            $barColor
            = 'gray';
        }

        else if (
            $previousValue
            < $currentValue
        ) {
            $barColor =
            'green';
        }

        else if (
            $previousValue
            > $currentValue
        ) {
            $barColor =
            'red';
        }

        else
        {
            $barColor =
            'gray';
        }

        return $barColor;
    }
    // calculateBarColor()



    private static function
    calculateMaxValue(
        array $valuesArgument
    ): float
    {
        $initialMaxValue =
        1.0;

        $maxValue =
        array_reduce(
            $valuesArgument,
            function (
                ?float $previousMaxValue,
                ?float $currentValue
            ): ?float
            {
                if (
                    $currentValue ===
                    null
                ) {
                    return $previousMaxValue;
                }

                if (
                    $previousMaxValue ===
                    null
                ) {
                    return $currentValue;
                }

                if (
                    $currentValue >
                    $previousMaxValue
                ) {
                    return $currentValue;
                }

                return $previousMaxValue;
            },
            $initialMaxValue
        );

        return $maxValue;
    }
    // calculateMaxValue



    private static function
    calculateMinValue(
        array $valuesArgument
    ): float
    {
        $initialMinValue
        = null;

        $minValue =
        array_reduce(
            $valuesArgument,
            function (
                ?float $previousMinValue,
                ?float $currentValue
            ): ?float
            {
                if (
                    $currentValue ===
                    null
                ) {
                    return $previousMinValue;
                }

                if (
                    $previousMinValue ===
                    null
                ) {
                    return $currentValue;
                }

                if (
                    $currentValue <
                    $previousMinValue
                ) {
                    return $currentValue;
                }

                return $previousMinValue;
            },
            $initialMinValue
        );

        return $minValue;
    }
    // calculateMinValue()



    /**
     * This function calculates unit values (0 - 1) from an array of floats. The
     * returned unit values are meant to be used to render graphs showing the
     * differences in the values.
     *
     * If the differenct between the maximum values and the minimum values is
     * less than 75 percent of the available area the values will be reduced to
     * make that range equal to 75 percent of the area so that the changes
     * between values can be seen better.
     */
    private static function
    calculateUnitValues(
        array $arrayOfValuesArgument
    ): array
    {
        $maxValue =
        CB_View_SVGBarChart1::calculateMaxValue(
            $arrayOfValuesArgument
        );

        $minValue =
        CB_View_SVGBarChart1::calculateMinValue(
            $arrayOfValuesArgument
        );

        if (
            $minValue
            === null
        ) {
            $minValue
            = $maxValue;
        }

        $changeRange =
        $maxValue
        - $minValue;

        $changeRangeToFullRangeRatio =
        $changeRange
        / $maxValue;

        if (
            $minValue
            === $maxValue
        ) {
            $reducedValues =
            $arrayOfValuesArgument;
        }

        else if (
            $changeRangeToFullRangeRatio
            > .75
        ) {
            $reducedValues =
            $arrayOfValuesArgument;
        }

        else
        {
            $valueReduction =
            $maxValue
            - (
                $changeRange /
                0.75
            );

            $maxValue -=
            $valueReduction;

            $minValue -=
            $valueReduction;

            $reducedValues =
            [];

            for (
                $arrayOfValuesArgumentIndex =
                0;

                $arrayOfValuesArgumentIndex
                < count($arrayOfValuesArgument);

                $arrayOfValuesArgumentIndex
                += 1
            ) {
                $originalValue =
                $arrayOfValuesArgument[
                    $arrayOfValuesArgumentIndex
                ];

                if (
                    $originalValue
                    === null
                ) {
                    $reducedValue
                    = null;
                }

                else
                {
                    $reducedValue =
                    $originalValue -
                    $valueReduction;
                }

                array_push(
                    $reducedValues,
                    $reducedValue
                );
            }
        }

        $unitValues =
        [];

        for (
            $arrayOfValuesArgumentIndex =
            0;

            $arrayOfValuesArgumentIndex
            < count($arrayOfValuesArgument);

            $arrayOfValuesArgumentIndex
            += 1
        ) {
            $reducedValue =
            $reducedValues[
                $arrayOfValuesArgumentIndex
            ];

            if (
                $reducedValue
                === null
            ) {
                $unitValue
                = null;
            }

            else
            {
                $unitValue
                = $reducedValue
                / $maxValue;
            }

            array_push(
                $unitValues,
                $unitValue
            );
        }

        return $unitValues;
    }
    // calculateUnitValues()

}
