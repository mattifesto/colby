<?php

final class CBUISpecEditor {

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
                'v675.7.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBConvert',
            'CBDefaultEditor',
            'CBException',
            'CBModel',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- functions -- */



    /**
     * @param string $modelClassName
     *
     *      This parameter is not verified by this function to be an actual
     *      model class name.
     *
     * @return string|null
     *
     *      Returns the class name of a class that exists that is named what an
     *      editor class would be named for the model class name. This class is
     *      not verified by this function to be an actual editor class, but it
     *      should be an editor class. (There is a test to test that such a
     *      class is an editor class and actually works.)
     *
     *      Returns null if there is no class that exists with a name that
     *      indicates that it is an editor for the model class name.
     */
    static function
    modelClassNameToEditorClassName(
        $modelClassName
    ): ?string {
        $editorClassName = "CBUISpecEditor_{$modelClassName}";

        if (class_exists($editorClassName)) {
            return $editorClassName;
        }

        $editorClassName = "{$modelClassName}Editor";

        if (class_exists($editorClassName)) {
            return $editorClassName;
        }

        return null;
    }
    /* modelClassNameToEditorClassName() */

}
/* CBUISpecEditor */
