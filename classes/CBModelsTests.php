<?php

final class CBModelsTests {

    const testModelIDs = [
        'dbc96d7b92337a3c6b9274c89473ee547ee518d7',
        '728038c2af4e49fb265c82971ac441f96d591056',
        '95397735c2fd6d7e75a2d059fededae693665f50'
    ];

    /**
     * This function assumes that the CBModels functionality is working. The
     * functions that call it will be testing to see if that is actually true.
     *
     * A transaction should be started before this function is called.
     *
     * @return null
     */
    private static function createTestEnvironment() {
        $specs = CBModels::fetchSpecsByID(CBModelsTests::testModelIDs, [
            'createSpecForIDCallback' => function($ID) {
                $spec           = CBModels::modelWithClassName('CBModelTest', ['ID' => $ID]);
                $spec->title    = "Title {$ID}";
                return $spec;
            }
        ]);

        CBModels::save($specs);
    }

    /**
     * @return null
     */
    public static function fetchModelByIDTest() {
        Colby::query('START TRANSACTION');

        CBModelsTests::createTestEnvironment();

        $ID     = CBModelsTests::testModelIDs[2];
        $model  = CBModels::fetchModelByID($ID);

        CBModelTest::checkModelWithID($model, $ID, 1);

        Colby::query('ROLLBACK');
    }
    /**
     * Tests the behavior of making a version for a new model
     */
    public static function makeVersionsForUpdateTest() {
        $ID0 = '3222378291fdbc3b958f95c69e0233cda61cc0a8';

        Colby::query('START TRANSACTION');

        $versions = CBModels::makeVersionsForUpdate([$ID0]);

        if ($versions[$ID0] !== null) {
            throw new Exception('The version returned for a new model is not correct.');
        }

        $IDAsSQL    = CBHex160::toSQL($ID0);
        $count      = CBDB::SQLToValue("SELECT COUNT(*) FROM `CBModels` WHERE `ID` = {$IDAsSQL} AND `version` = 0");

        if ($count !== '1') {
            throw new Exception('A new model should exist in the `CBModels` table with a verison of 0.');
        }

        CBModels::cancelUpdate();
        Colby::query('ROLLBACK');

        $count = CBDB::SQLToValue("SELECT COUNT(*) FROM `CBModels` WHERE `ID` = {$IDAsSQL}");

        if ($count !== '0') {
            throw new Exception('A new model should be removed from the `CBModels` if the update is not completed.');
        }
    }

    /**
     * Tests the behavior of making a spec for a new model
     */
    public static function makeSpecsForUpdateTest() {
        Colby::query('START TRANSACTION');

        $ID     = 'a5db2923b26175ef624aacbd3e4fe344bc317486';
        $specs  = CBModels::makeSpecsForUpdate([$ID]);

        if ($specs[$ID]->ID !== $ID) {
            throw new Exception('The spec returned for a new model has the wrong ID.');
        }

        $IDAsSQL    = CBHex160::toSQL($ID);
        $count      = CBDB::SQLToValue("SELECT COUNT(*) FROM `CBModels` WHERE `ID` = {$IDAsSQL} AND `version` = 0");

        if ($count !== '1') {
            throw new Exception('A new model should exist in the `CBModels` table with a verison of 0.');
        }

        CBModels::cancelUpdate();
        Colby::query('ROLLBACK');

        $count = CBDB::SQLToValue("SELECT COUNT(*) FROM `CBModels` WHERE `ID` = {$IDAsSQL}");

        if ($count !== '0') {
            throw new Exception('A new model should be removed from the `CBModels` if the update is not completed.');
        }
    }

    /**
     * @return null
     */
    public static function makeCustomSpecsForUpdateTest() {
        Colby::query('START TRANSACTION');

        $ID     = '38ab6bc56796350379e0e579c38e95a97d7669a8';
        $specs  = CBModels::makeSpecsForUpdate([$ID], function($ID) {
            return (object)['className' => 'CBModelsTests', 'ID' => $ID];
        });

        if ($specs[$ID]->ID != $ID || $specs[$ID]->className != 'CBModelsTests') {
            throw new Exception('The custom spec was not created propertly.');
        }

        CBModels::cancelUpdate();
        Colby::query('ROLLBACK');
    }

    /**
     * @return null
     */
    private static function checkSQL(array $checks) {
        array_walk($checks, function(array $check) {
            $value = CBDB::SQLToValue($check[0]);
            if ($value !== $check[1]) {
                $actual     = json_encode($value);
                $expected   = json_encode($check[1]);
                throw new Exception("Check returned {$actual} instead of {$expected}: {$check[0]}");
            }
        });
    }

    /**
     * @return null
     */
    public static function updateAndFetchTest() {
        Colby::query('START TRANSACTION');

        $ID                 = 'cc347c84af65b626520b6961612e5e6e8be7c8aa';
        $IDAsSQL            = CBHex160::toSQL($ID);
        $specs              = CBModels::makeSpecsForUpdate([$ID]);
        $spec               = $specs[$ID];
        $spec->className    = __CLASS__;
        $spec->title        = 'Hello, world!';
        $model              = self::specToModel($spec);

        CBModels::updateModels([(object)['spec' => $spec, 'model' => $model]]);

        self::checkSQL([
            ["SELECT COUNT(*) FROM `CBModels` WHERE `ID` = {$IDAsSQL} AND `version` = 1", '1'],
            ["SELECT COUNT(*) FROM `CBModelVersions` WHERE `ID` = {$IDAsSQL}", '1']
        ]);


        $specs              = CBModels::makeSpecsForUpdate([$ID]);
        $spec               = $specs[$ID];
        $spec->title        = 'Mama Mia!';
        $model              = self::specToModel($spec);

        CBModels::updateModels([(object)['spec' => $spec, 'model' => $model]]);

        self::checkSQL([
            ["SELECT COUNT(*) FROM `CBModels` WHERE `ID` = {$IDAsSQL} AND `version` = 2", '1'],
            ["SELECT COUNT(*) FROM `CBModelVersions` WHERE `ID` = {$IDAsSQL}", '2']
        ]);


        $specs              = CBModels::makeSpecsForUpdate([$ID]);
        $spec               = $specs[$ID];
        $spec->title        = 'Gimme! Gimme! Gimme!';
        $model              = self::specToModel($spec);

        CBModels::updateModels([(object)['spec' => $spec, 'model' => $model]]);

        self::checkSQL([
            ["SELECT COUNT(*) FROM `CBModels` WHERE `ID` = {$IDAsSQL} AND `version` = 3", '1'],
            ["SELECT COUNT(*) FROM `CBModelVersions` WHERE `ID` = {$IDAsSQL}", '3']
        ]);

        $models = CBModels::fetchModels([$ID]);

        foreach ([
            ['$models[$ID]->version',   3],
            ['$models[$ID]->title',     'Gimme! Gimme! Gimme!']
        ] as $condition) {
            $actual     = eval("return {$condition[0]};");
            $expected   = $condition[1];
            if ($actual !== $expected) {
                $a = json_encode($actual);
                $e = json_encode($expected);
                throw new Exception("Expression evaluates to {$a} instead of {$e}: \"{$condition[0]}\"");
            }
        }

        $model = CBModels::fetchModelWithVersion($ID, 2);

        foreach ([
            ['$model->version', 2],
            ['$model->title',   'Mama Mia!']
        ] as $condition) {
            $actual     = eval("return {$condition[0]};");
            $expected   = $condition[1];
            if ($actual !== $expected) {
                $a = json_encode($actual);
                $e = json_encode($expected);
                throw new Exception("Expression evaluates to {$a} instead of {$e}: \"{$condition[0]}\"");
            }
        }

        Colby::query('ROLLBACK');
    }
}


final class CBModelTest {

    /**
     * @return null
     */
    public static function checkModelWithID(stdClass $model, $ID, $version = false) {
        if ($model->ID !== $ID) {
            throw new Exception('Incorrect model ID');
        }

        if ($model->title !== "Title {$ID}") {
            throw new Exception('Incorrect title');
        }

        if ($model->titleAsHTML !== "Title {$ID}") {
            throw new Exception('Incorrect titleAsHTML');
        }

        if ($version !== false && $model->version !== $version) {
            $actual     = json_encode($model->version);
            $expected   = json_encode($version);
            throw new Exception("Model version: {$actual}, Expected version: {$expected}");
        }
    }

    /**
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model              = CBModels::modelWithClassName(__CLASS__);
        $model->title       = isset($spec->title) ? (string)$spec->title : '';
        $model->titleAsHTML = ColbyConvert::textToHTML($model->title);

        return $model;
    }
}
