<?php

final class CBModelCacheTests {

    /**
     * @return [[<class>, <test>]]
     */
    static function CBUnitTests_tests(): array {
        return [
            ['CBModelCache', 'general'],
            ['CBModelCache', 'save'],
        ];
    }

    /**
     * @return object
     */
    static function CBTest_general(): stdClass {
        Colby::query('START TRANSACTION');

        $ID = CBHex160::random();
        $model = CBModelCache::modelByID($ID);

        if (!empty($model)) {
            throw new Exception('Model appears to be cached when it should not be.');
        }

        $model = CBModelCache::fetchModelByID($ID);

        if (!empty($model)) {
            throw new Exception('Model appears to exist when it should not.');
        }

        $spec = (object)[
            'className' => 'CBTextView2',
            'ID' => $ID,
        ];

        CBModels::save([$spec]);

        $model = CBModelCache::fetchModelByID($ID);

        if (!isset($model->className) || $model->className !== 'CBTextView2') {
            throw new Exception('The model does not appear to have been fetched properly.');
        }

        $model = CBModelCache::modelByID($ID);

        if (!isset($model->className) || $model->className !== 'CBTextView2') {
            throw new Exception('The model does not appear to have been cached properly.');
        }

        CBModelCache::uncacheByID([$ID]);

        $model = CBModelCache::modelByID($ID);

        if (!empty($model)) {
            throw new Exception('The model does not appear to have been uncached properly.');
        }

        CBModelCache::fetchModelLazilyByID($ID);

        $model = CBModelCache::fetchModelByID(CBHex160::random());

        if (!empty($model)) {
            throw new Exception('The false model appears to exist.');
        }

        $model = CBModelCache::modelByID($ID);

        if (!isset($model->className) || $model->className !== 'CBTextView2') {
            throw new Exception('The model does not appear to have been cached as a needed model properly.');
        }

        Colby::query('ROLLBACK');

        return (object)[
            'succeeded' => true,
        ];
    }

    /**
     * @return object
     */
    static function CBTest_save(): stdClass {
        $ID = '275097249aeb4eb28b1f6bfa3280485346eb5c94';

        CBModels::deleteByID($ID);

        $model = CBModelCache::modelByID($ID);

        if (!empty($model)) {
            throw new Exception('Subtest 1: The model should not be cached.');
        }

        $spec = (object)[
            'className' => 'CBMessageView',
            'ID' => $ID,
        ];

        CBModels::save($spec);

        $model = CBModelCache::modelByID($ID);

        if (!empty($model)) {
            throw new Exception('Subtest 2: The model should not be cached.');
        }

        $model = CBModelCache::fetchModelByID($ID);

        if ($model->ID !== $ID) {
            throw new Exception('Subtest 3: The model was not fetched.');
        }

        $model = CBModelCache::modelByID($ID);

        if (empty($model)) {
            throw new Exception('Subtest 4: The model was not cached.');
        }

        CBModels::save($spec);

        $model = CBModelCache::modelByID($ID);

        if (!empty($model)) {
            throw new Exception('Subtest 5: The cached model was not uncached when the model was saved.');
        }

        $model = CBModelCache::fetchModelByID($ID);

        if ($model->ID !== $ID) {
            throw new Exception('Subtest 6: The model was not fetched.');
        }

        $model = CBModelCache::modelByID($ID);

        if (empty($model)) {
            throw new Exception('Subtest 7: The model was not cached.');
        }

        CBModels::deleteByID($ID);

        $model = CBModelCache::modelByID($ID);

        if (!empty($model)) {
            throw new Exception('Subtest 8: The cached model was not uncached when the model was deleted.');
        }

        return (object)[
            'succeeded' => true,
        ];
    }
}
