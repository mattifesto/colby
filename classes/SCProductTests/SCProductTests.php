<?php

final class SCProductTests {

    /* -- CBAjax interfaces -- -- -- -- -- */

    /**
     * @return void
     */
    static function CBAjax_installTestProducts(): void {
        SCProductTests::installTestProducts();
    }



    /**
     * @return string
     */
    static function CBAjax_installTestProducts_getUserGroupClassName(): string {
        return 'CBDevelopersUserGroup';
    }



    /**
     * @return void
     */
    static function CBAjax_uninstallTestProducts(): void {
        SCProductTests::uninstallTestProducts();
    }



    /**
     * @return string
     */
    static function CBAjax_uninstallTestProducts_getUserGroupClassName(): string {
        return 'CBDevelopersUserGroup';
    }



    /* -- functions -- -- -- -- -- */



    /**
     * @return void
     */
    static function installTestProducts(): void {
        CBModels::save(
            [
                (object)[
                    'className' => 'SCProduct',
                    'ID' => SCProductTests::testProductID1(),
                    'priceInCents' => 1000,
                    'productCode' => SCProductTests::testProductCode1(),
                    'title' => 'Test Product 1',
                ],
                (object)[
                    'className' => 'SCProduct',
                    'ID' => SCProductTests::testProductID2(),
                    'priceInCents' => 2000,
                    'productCode' => SCProductTests::testProductCode2(),
                    'title' => 'Test Product 2',
                ],
            ],
            /* force: */ true
        );
    }

    /**
     * @return string
     */
    static function testProductCode1(): string {
        return 'SCProductTest_1';
    }

    /**
     * @return string
     */
    static function testProductCode2(): string {
        return 'SCProductTest_2';
    }

    /**
     * @return string
     */
    static function testProductID1(): string {
        return SCProduct::productCodeToProductID(
            SCProductTests::testProductCode1()
        );
    }

    /**
     * @return string
     */
    static function testProductID2(): string {
        return SCProduct::productCodeToProductID(
            SCProductTests::testProductCode2()
        );
    }

    /**
     * @return void
     */
    static function uninstallTestProducts(): void {
        CBModels::deleteByID(
            [
                SCProductTests::testProductID1(),
                SCProductTests::testProductID2(),
            ]
        );
    }
}
