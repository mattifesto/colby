<?php

final class CBPageLayoutTests {

    /**
     * @return null
     */
    static function buildTest() {
        $spec1 = (object)[
            'className' => 'CBPageLayout',
        ];

        $expectedModel1 = (object)[
            'className' => 'CBPageLayout',
            'CSSClassNames' => [],
            'customLayoutClassName' => '',
            'customLayoutProperties' => (object)[],
            'isArticle' => false,
        ];

        $actualModel1 = CBModel::build($spec1);

        if ($actualModel1 != $expectedModel1) {
            $firstLine = (
                'In CBPageLayoutTests::build() the actual model did not ' .
                'match the expected model'
            );

            $expectedModel1AsJSON = CBMessageMarkup::stringToMarkup(
                CBConvert::valueToPrettyJSON($expectedModel1)
            );

            $actualModel1AsJSON = CBMessageMarkup::stringToMarkup(
                CBConvert::valueToPrettyJSON($actualModel1)
            );

            $message = <<<EOT

                {$firstLine}

                Expected Model:

                --- pre\n{$expectedModel1AsJSON}
                ---

                Actual Model:

                --- pre\n{$actualModel1AsJSON}
                ---

EOT;

            CBLog::log((object)[
                'className' => __CLASS__,
                'message' => $message,
                'severity' => 3,
            ]);

            throw new Exception($firstLine);
        }
    }
    /* buildTest() */
}
