<?php

final class CBThemedTextView {

    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return model
     */
    static function CBAjax_convertToCBMessageView($spec): stdClass {
        return CBThemedTextView::convertToCBMessageView($spec);
    }



    /**
     * @return string
     */
    static function CBAjax_convertToCBMessageView_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v458.css', cbsysurl()),
        ];
    }



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBViewCatalog::installView(
            __CLASS__,
            (object)[
                'isDeprecated' => true,
            ]
        );
    }



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBViewCatalog',
        ];
    }



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        $model = (object)[
            'useLightTextColors' => CBModel::valueToBool(
                $spec,
                'useLightTextColors'
            ),
        ];

        $model->center = CBModel::value($spec, 'center', false, 'boolval');

        $model->contentAsMarkaround = trim(
            CBModel::valueToString($spec, 'contentAsMarkaround')
        );

        $model->contentAsHTML = CBMarkaround::markaroundToHTML(
            $model->contentAsMarkaround
        );

        $model->contentColor = CBModel::value(
            $spec,
            'contentColor',
            null,
            'CBConvert::stringToCSSColor'
        );

        $model->titleAsMarkaround = trim(
            CBModel::valueToString($spec, 'titleAsMarkaround')
        );

        $model->title = CBMarkaround::paragraphToText(
            $model->titleAsMarkaround
        );

        $model->titleAsHTML = CBMarkaround::paragraphToHTML(
            $model->titleAsMarkaround
        );

        $model->titleColor = CBModel::value(
            $spec,
            'titleColor',
            null,
            'CBConvert::stringToCSSColor'
        );

        $model->URL = trim(
            CBModel::valueToString($spec, 'URL')
        );

        $model->URLAsHTML = cbhtml($model->URL);

        $stylesTemplate = trim(
            CBModel::valueToString($spec, 'stylesTemplate')
        );

        if (!empty($stylesTemplate)) {
            $model->stylesID = CBID::generateRandomCBID();
            $localCSSClassName = "T{$model->stylesID}";
            $model->stylesCSS = CBView::localCSSTemplateToLocalCSS(
                $stylesTemplate,
                'view',
                ".{$localCSSClassName}"
            );
        }

        return $model;
    }
    /* CBModel_build() */



    /**
     * @param model $model
     *
     * @return string
     */
    static function CBModel_toSearchText(stdClass $model): string {
        return implode(
            ' ',
            [
                CBModel::valueToString($model, 'title'),
                CBModel::valueToString($model, 'contentAsMarkaround'),
            ]
        );
    }



    /* -- CBView interfaces -- -- -- -- -- */



    /**
     * @param bool? $model->center;
     * @param string? $model->contentAsHTML
     * @param string? $model->titleAsHTML
     * @param string? $model->URLAsHTML
     * @param bool? $model->useLightTextColors
     *
     * @return null
     */
    static function CBView_render(stdClass $model) {
        if (empty($model->titleAsHTML) && empty($model->contentAsHTML)) {
            return;
        }

        $class = "CBThemedTextView CBThemedTextView_default";

        if (!empty($model->stylesID)) {
            $stylesClass = "T{$model->stylesID}";
            $class = "{$class} {$stylesClass}";
        }

        if (!empty($model->useLightTextColors)) {
            $class = "{$class} light";
        }

        CBHTMLOutput::addCSS(CBModel::value($model, 'stylesCSS'));

        $style = empty($model->center) ? '' : ' style="text-align: center"';

        if (empty($model->URLAsHTML)) {
            $open = "<section class=\"{$class}\"{$style}>";
            $close = '</section>';
        } else {
            $open = (
                "<a href=\"{$model->URLAsHTML}\" class=\"{$class}\"{$style}>"
            );
            $close = '</a>';
        }

        if (empty($model->titleAsHTML)) {
            $title = '';
        } else {
            $style =
            empty($model->titleColor) ?
            '' :
            " style=\"color: {$model->titleColor}\"";

            $title = (
                "<div class=\"title\">" .
                "<h1{$style}>{$model->titleAsHTML}</h1>" .
                "</div>"
            );
        }

        if (empty($model->contentAsHTML)) {
            $content = '';
        } else {
            $style =
            empty($model->contentColor) ?
            '' :
            " style=\"color: {$model->contentColor}\"";

            $content = (
                "<div class=\"content\" {$style}>{$model->contentAsHTML}</div>"
            );
        }

        echo $open, $title, $content, $close;
    }
    /* CBView_render() */



    /* -- functions -- -- -- -- -- */



    /**
     * This function converts a CBThemedTextView spec into a CBMessageView spec.
     *
     * @param object $spec
     *
     * @return object
     */
    static function convertToCBMessageView(
        stdClass $specIn
    ): stdClass {
        $message = '';
        $CSSClassNames = [];
        $CSSTemplate = [];

        /* title */

        $value = CBMessageMarkup::stringToMessage(
            trim(CBModel::valueToString($specIn, 'titleAsMarkaround'))
        );

        if (!empty($value)) {
            $message .= "--- h1\n{$value}\n---";
        }

        /* content */

        $value = CBMessageMarkup::stringToMessage(
            trim(CBModel::valueToString($specIn, 'contentAsMarkaround'))
        );

        if (!empty($value)) {
            if (!empty($message)) { $message .= "\n\n"; }

            $message .= $value;
        }

        /* CSS template */

        $value = trim(CBModel::valueToString($specIn, 'stylesTemplate'));

        if (!empty($value)) {
            array_push($CSSTemplate, $value);
        }

        /* title color */

        $value = trim(CBModel::valueToString($specIn, 'titleColor'));

        if (!empty($value)) {
            array_push(
                $CSSTemplate,
                "view > .content > h1:first-child { color: {$value} }"
            );
        }

        /* content color */

        $value = trim(CBModel::valueToString($specIn, 'contentColor'));

        if (!empty($value)) {
            array_push($CSSTemplate, "view > .content { color: {$value} }");
        }

        /* center */

        $value = CBModel::valueToBool($specIn, 'center');

        if ($value) {
            array_push($CSSClassNames, 'center');
        }

        /* URL */

        $value = CBMessageMarkup::stringToMessage(
            trim(CBModel::valueToString($specIn, 'URL'))
        );

        if (!empty($value)) {
            if (!empty($message)) { $message .= "\n\n"; }

            $message .= "--- center\n({$value} (a {$value}))\n---";
        }

        /* generate CBMessageView spec */

        $specOut = (object)[
            'className' => 'CBMessageView',
        ];

        if (!empty($message)) {
            $specOut->markup = $message;
        }

        if (!empty($CSSClassNames)) {
            $specOut->CSSClassNames = implode(' ', $CSSClassNames);
        }

        if (!empty($CSSTemplate)) {
            $specOut->CSSTemplate = implode("\n\n", $CSSTemplate);
        }

        return $specOut;
    }
    /* convertToCBMessageView() */

}
