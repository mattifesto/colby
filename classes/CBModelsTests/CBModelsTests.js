"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBModelsTests */
/* global
    CBModel,
    CBModels,
    CBTest,
    Colby,
*/

var CBModelsTests = {

    /**
     * @return object|Promise
     */
    CBTest_general: function () {
        let ID = "c47207735e32dff3e3541473905b9c933d556c18";

        let storages = [
            localStorage,
            sessionStorage,
        ];

        for (let i = 0; i < storages.length; i += 1) {
            let storage = storages[i];
            let version = 0;

            let spec = {
                className: "CBModelsTest",
                value: Colby.random160(),
            };

            CBModels.delete(ID, storage);

            {
                let actualResult = CBModels.fetch(ID, storage);
                let expectedResult;

                if (actualResult !== expectedResult) {
                    return CBTest.resultMismatchFailure(
                        "delete 1",
                        actualResult,
                        expectedResult
                    );
                }
            }

            CBModels.save(ID, spec, version, storage);

            {
                let value = CBModels.fetch(ID, storage);

                {
                    let actualResult = value.spec;
                    let expectedResult = spec;

                    if (!CBModel.equals(actualResult, expectedResult)) {
                        return CBTest.resultMismatchFailure(
                            "fetch spec 1",
                            actualResult,
                            expectedResult
                        );
                    }
                }

                {
                    let actualResult = value.meta.version;
                    let expectedResult = 1;

                    if (actualResult !== expectedResult) {
                        return CBTest.resultMismatchFailure(
                            "fetch version 1",
                            actualResult,
                            expectedResult
                        );
                    }
                }
            }

            version += 1;
            spec.value = Colby.random160();

            CBModels.save(ID, spec, version, storage);

            {
                let value = CBModels.fetch(ID, storage);

                {
                    let actualResult = value.spec;
                    let expectedResult = spec;

                    if (!CBModel.equals(actualResult, expectedResult)) {
                        return CBTest.resultMismatchFailure(
                            "fetch spec 2",
                            actualResult,
                            expectedResult
                        );
                    }
                }

                {
                    let actualResult = value.meta.version;
                    let expectedResult = 2;

                    if (actualResult !== expectedResult) {
                        return CBTest.resultMismatchFailure(
                            "fetch version 2",
                            actualResult,
                            expectedResult
                        );
                    }
                }
            }

            CBModels.delete(ID, storage);

            {
                let actualResult = CBModels.fetch(ID, storage);
                let expectedResult;

                if (actualResult !== expectedResult) {
                    return CBTest.resultMismatchFailure(
                        "delete 2",
                        actualResult,
                        expectedResult
                    );
                }
            }
        }
        /* for */

        return {
            succeeded: true,
        };
    },
    /* CBTest_general() */
};
