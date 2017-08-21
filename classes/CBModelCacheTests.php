<?php

final class CBModelCacheTests {

    public static function test() {
        Colby::query('START TRANSACTION');

        $ID     = CBHex160::random();
        $model  = CBModelCache::modelByID($ID);

        if ($model !== false) {
            throw new Exception('Model appears to be cached when it should not be.');
        }

        $model  = CBModelCache::fetchModelByID($ID);

        if ($model !== false) {
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

        CBModelCache::uncacheModelsByID([$ID]);

        $model = CBModelCache::modelByID($ID);

        if ($model !== false) {
            throw new Exception('The model does not appear to have been uncached properly.');
        }

        CBModelCache::fetchModelLazilyByID($ID);

        $model = CBModelCache::fetchModelByID(CBHex160::random());

        if ($model !== false) {
            throw new Exception('The false model appears to exist.');
        }

        $model = CBModelCache::modelByID($ID);

        if (!isset($model->className) || $model->className !== 'CBTextView2') {
            throw new Exception('The model does not appear to have been cached as a needed model properly.');
        }

        Colby::query('ROLLBACK');
    }
}
