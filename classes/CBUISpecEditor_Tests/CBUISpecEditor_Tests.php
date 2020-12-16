<?php

final class CBUISpecEditor_Tests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.5.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [[<name>, <value>]]
     */
    static function
    CBHTMLOutput_JavaScriptVariables(
    ): array {
        return [
            [
                'CBUISpecEditor_Tests_editableModelClassNames',
                CBUISpecEditor_Tests::fetchEditableModelClassNames()
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return array_merge(
            [
                'CBTest',
                'CBUISpecEditor',
            ],
            CBUISpecEditor_Tests::fetchModelEditorClassNames()
        );
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'allModelEditors',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- functions -- */



    /**
     * @return [string]
     */
    private static function
    fetchEditableModelClassNames(
    ): array {
        static $editableModelClassNames = [];

        if (
            empty($editableModelClassNames)
        ) {
            $editableModelClassNames = CBLibrary::getAllClassDirectoryNames();

            $editableModelClassNames = array_filter(
                $editableModelClassNames,
                function (
                    $className
                ) {
                    return (
                        CBModel::classIsModel(
                            $className
                        )

                        &&

                        CBUISpecEditor::modelClassNameToEditorClassName(
                            $className
                        )
                    );
                }
            );

            $editableModelClassNames = array_values(
                $editableModelClassNames
            );
        }

        return $editableModelClassNames;
    }
    /* getEditableModelClassNames() */



    /**
     * @return [string]
     */
    private static function
    fetchModelEditorClassNames(
    ): array {
        static $modelEditorClassNames = [];

        if (
            empty($modelEditorClassNames)
        ) {
            $editableModelClassNames = (
                CBUISpecEditor_Tests::fetchEditableModelClassNames()
            );

            $modelEditorClassNames = array_map(
                function (
                    $editableModelClassName
                ) {
                    return CBUISpecEditor::modelClassNameToEditorClassName(
                        $editableModelClassName
                    );
                },
                $editableModelClassNames
            );

            $modelEditorClassNames = array_values(
                $modelEditorClassNames
            );
        }

        return $modelEditorClassNames;
    }
    /* getModelEditorClassNames() */

}
/* CBUISpecEditor_Tests */
