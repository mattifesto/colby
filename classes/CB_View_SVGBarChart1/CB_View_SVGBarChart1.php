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
                '2022_09_13_1663105786',
                'css',
                cbsysurl()
            ),
        ];

        return $cssURLs;
    }
    // CBHTMLOutput_CSSURLs()



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
                <svg
                    class="CB_View_SVGBarChart1_svg_element"
                    style="width: 280px;"
                    viewBox="0 0 280 ${graphHeightAsPixels}"
                >

        EOT;

        $initialMaxValue =
        1;

        $maxValue =
        array_reduce(
            $values,
            function (
                float $previousMaxValue,
                float $currentValue
            ): float
            {
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

        $columnIndex =
        0;

        $previousValue =
        $values[0];

        foreach (
            $values as
            $currentValue
        ) {
            if (
                $previousValue < $currentValue
            ) {
                $barColor =
                'green';
            }

            else if (
                $previousValue > $currentValue
            ) {
                $barColor =
                'red';
            }

            else
            {
                $barColor =
                'gray';
            }

            $columnHeightAsPixels =
            $graphHeightAsPixels *
            (
                $currentValue /
                $maxValue
            );

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
                    fill="${barColor}"
                    height="${columnHeightAsPixels}"
                    width="${columnWidthAsPixels}"
                    x="${columnXAsPixels}"
                    y="${columnYAsPixels}"
                />

            EOT;

            $previousValue =
            $currentValue;

            $columnIndex +=
            1;
        }

        echo <<<EOT

                </svg>
            </div>

        EOT;
    }
    // CBView_render()

}
