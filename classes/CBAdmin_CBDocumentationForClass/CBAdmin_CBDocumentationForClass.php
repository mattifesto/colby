<?php

final class CBAdmin_CBDocumentationForClass {

    /* -- CBAdmin interfaces -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(
    ): string {
        return 'CBAdministratorsUserGroup';
    }
    /* CBAdmin_getUserGroupClassName() */



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(
    ): array {
        return [
            'help'
        ];
    }
    /* CBAdmin_menuNamePath() */



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        $className = cb_query_string_value(
            'className'
        );

        if (!class_exists($className)) {
            CBHTMLOutput::render404();
        }

        CBHTMLOutput::pageInformation()->title = (
            "{$className} Documentation"
        );

        CBView::renderSpec(
            (object)[
                'className' => 'CBPageTitleAndDescriptionView',
            ]
        );

        $functionName = "{$className}::CBAdmin_CBDocumentationForClass_render";

        if (is_callable($functionName)) {
            call_user_func(
                $functionName
            );

            return;
        }

        $descriptionFilepath = Colby::findFile(
            "classes/{$className}/{$className}_" .
            'CBDocumentation_description.cbmessage'
        );

        if ($descriptionFilepath === null) {
            CBAdmin_CBDocumentationForClass::renderNoDocumentation(
                $className
            );
        } else {
            CBAdmin_CBDocumentationForClass::renderDescriptionFile(
                $descriptionFilepath
            );
        }
    }
    /* CBAdmin_render() */



    /* -- functions -- */



    /**
     * @param string $descriptionFilepath
     *
     * @return void
     */
    private static function
    renderDescriptionFile(
        string $descriptionFilepath
    ): void {
        $messageViewSpec = CBModel::createSpec(
            'CBMessageView'
        );

        $cbmessage = file_get_contents(
            $descriptionFilepath
        );

        CBMessageView::setCBMessage(
            $messageViewSpec,
            $cbmessage
        );

        CBView::renderSpec(
            $messageViewSpec
        );
    }
    /* renderDescriptionFile() */



    /**
     * @param string $targetClassName
     *
     * @return void
     */
    private static function
    renderNoDocumentation(
        string $targetClassName
    ): void {
        $messageViewSpec = CBModel::createSpec(
            'CBMessageView'
        );

        $cbmessage = <<<EOT

            There is no documentation for this class.

        EOT;

        CBMessageView::setCBMessage(
            $messageViewSpec,
            $cbmessage
        );

        CBView::renderSpec(
            $messageViewSpec
        );

        $developerView = CBModel::createSpec(
            'CBView_CBDocumentationDeveloper'
        );

        CBView_CBDocumentationDeveloper::setTargetClassName(
            $developerView,
            $targetClassName
        );

        CBView::renderSpec(
            $developerView
        );
    }
    /* renderNoDocumentation() */

}
