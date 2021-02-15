<?php

final class
CBView_CBDocumentationDeveloper {

    /* -- CBAjax interfaces -- */



    /**
     * @param object $args
     *
     *      {
     *          targetClassName: string
     *      }
     *
     * @return void
     */
    static function CBAjax_createDocumentationFile(
        stdClass $args
    ): void {
        $targetClassName = CBModel::valueToString(
            $args,
            'targetClassName'
        );

        $targetClassFilepath = Colby::findFile(
            "classes/{$targetClassName}/{$targetClassName}.php"
        );

        if ($targetClassFilepath === null) {
            throw new CBExceptionWithValue(
                'This class was not found.',
                $targetClassFilepath,
                '6d98c1a8a9196c22124daa5eb8857279f28a426d'
            );
        }

        $targetClassDocumentationFilepath = (
            dirname(
                $targetClassFilepath
            ) .
            '/' .
            $targetClassName .
            '_CBDocumentation_description.cbmessage'
        );

        if (!file_exists($targetClassDocumentationFilepath)) {
            touch($targetClassDocumentationFilepath);
        }
    }
    /* CBAjax_createDocumentationFile() */



    /**
     * @return string
     */
    static function CBAjax_createDocumentationFile_getUserGroupClassName(
    ): string {
        return 'CBDevelopersUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ) {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.13.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ) {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.13.js',
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
            'CBAjax',
            'CBUI',
            'CBUIPanel',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBModel interfaces -- */



    /**
     * @param object $viewSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $viewSpec
    ): stdClass {
        return (object)[
            'CBView_CBDocumentationDeveloper_targetClassName' => (
                CBView_CBDocumentationDeveloper::getTargetClassName(
                    $viewSpec
                )
            ),
        ];
    }
    /* CBModel_build() */



    /* -- CBView interfaces -- */



    /**
     * @param object $viewModel
     *
     * @return void
     */
    static function
    CBView_render(
        stdClass $viewModel
    ): void {
        $userIsDeveloper = (
            CBUserGroup::currentUserIsMemberOfUserGroup(
                'CBDevelopersUserGroup'
            )
        );

        if ($userIsDeveloper !== true) {
            return;
        }

        $targetClassName = CBView_CBDocumentationDeveloper::getTargetClassName(
            $viewModel
        );

        $targetClassFilepath = Colby::findFile(
            "classes/{$targetClassName}/{$targetClassName}.php"
        );

        if ($targetClassFilepath === null) {
            $classDirectories = CBLibrary::getClassDirectories(
                $targetClassName
            );

            ?>

            <dd class="CBView_CBDocumentationDeveloper_error">
                Warning. There is no class file in:

                <ul>
                    <?php

                        foreach ($classDirectories as $classDirectory) {
                            echo (
                                '<li><code>' .
                                cbhtml($classDirectory) .
                                '</code></li>'
                            );
                        }

                    ?>
                </ul>
            </dd>

            <?php

            return;
        }

        $documentationFilepath = (
            dirname(
                $targetClassFilepath
            ) .
            '/' .
            $targetClassName .
            '_CBDocumentation_description.cbmessage'
        );

        if (file_exists($documentationFilepath)) {
            return;
        }

        ?>

        <dd
            class="CBView_CBDocumentationDeveloper"
            data-target-class-name="<?= cbhtml($targetClassName) ?>"
        >
        </dd>

        <?php
    }
    /* CBView_render() */



    /* -- accessors -- */



    /**
     * @param object $viewModel
     *
     * @return string
     */
    static function
    getTargetClassName(
        stdClass $viewModel
    ) {
        return CBModel::valueToString(
            $viewModel,
            'CBView_CBDocumentationDeveloper_targetClassName'
        );
    }
    /* getTargetClassName() */



    /**
     * @param object $viewSpec
     *
     * @return string
     */
    static function
    setTargetClassName(
        stdClass $viewSpec,
        string $targetClassName
    ) {
        $viewSpec->CBView_CBDocumentationDeveloper_targetClassName = (
            $targetClassName
        );
    }
    /* setTargetClassName() */

}
