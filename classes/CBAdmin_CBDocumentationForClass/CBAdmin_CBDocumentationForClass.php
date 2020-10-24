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
            $cbmessage = <<<EOT

                There is no documentation for this class.

            EOT;
        } else {
            $cbmessage = file_get_contents(
                $descriptionFilepath
            );
        }

        CBView::renderSpec(
            (object)[
                'className' => 'CBMessageView',
                'markup' => $cbmessage,
            ]
        );
    }
    /* CBAdmin_render() */

}
