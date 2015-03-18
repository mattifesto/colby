<?php

class CBUnitTestsForCBView {

    /**
     * @return void
     */
    public static function runAll() {
        self::runTestsForSpecToModel();
    }

    /**
     * @return void
     */
    public static function runTestsForSpecToModel() {
        $tests[]    = ['null',                          'CBView'];
        $tests[]    = ['{"className":"CBView"}',        'CBView'];
        $tests[]    = ['{"className":"MyFakeView"}',    'MyFakeView'];

        foreach ($tests as $test) {
            $spec   = json_decode($test[0]);
            $model  = CBView::specToModel($spec);

            if ($model->className != $test[1]) {
                throw new RuntimeException("The spec {$test[0]} produced a model with a `className` of \"{$model->className}\" when \"{$test[1]}\" was expected.");
            }
        }
    }
}
