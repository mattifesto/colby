"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBThemedTextViewEditor */
/* globals
    CBAjax,
    CBUI,
    CBUIBooleanEditor,
    CBUINavigationView,
    CBUIPanel,
    CBUISpecClipboard,
    CBUISpecEditor,
    CBUIStringEditor,
    CBUIStringEditor2,
*/


var CBThemedTextViewEditor = {

    /**
     * @param model spec
     *
     * @return undefined
     */
    convertToCBMessageView: function (spec, specChangedCallback) {
        CBUIPanel.confirmText(
            `
                Are you sure you want to convert this CBThemedTextView into a
                CBMessageView?
            `
        ).then(
            function (wasConfirmed) {
                if (!wasConfirmed) {
                    return;
                }

                CBUISpecClipboard.specs = [spec];

                return CBAjax.call(
                    "CBThemedTextView",
                    "convertToCBMessageView",
                    spec
                ).then(
                    function (convertedSpec) {
                        Object.assign(spec, convertedSpec);

                        specChangedCallback();

                        let editor = CBUISpecEditor.create(
                            {
                                spec: spec,
                                specChangedCallback: specChangedCallback,
                            }
                        );

                        CBUINavigationView.replace(
                            {
                                element: editor.element,
                                title: spec.className,
                            }
                        );
                    }
                );
            }
        ).catch(
            function (error) {
                CBUIPanel.displayAndReportError(error);
            }
        );
    },
    /* convertToCBMessageView() */



    /**
     * @param object args
     *
     *      {
     *          CBUISpecEditor_getSpec() -> object
     *          CBUISpecEditor_getSpecChangedCallback() -> function
     *      }
     *
     * @return Element
     */
    CBUISpecEditor_createEditorElement: function(
        args
    ) {
        let spec = args.CBUISpecEditor_getSpec();
        let specChangedCallback = args.CBUISpecEditor_getSpecChangedCallback();
        let element;

        {
            let elements = CBUI.createElementTree(
                "CBThemedTextViewEditor",
                "CBUI_container1",
                "CBUI_button1"
            );

            element = elements[0];

            let buttonElement = elements[2];
            buttonElement.textContent = "Convert to CBMessageView";

            buttonElement.addEventListener(
                "click",
                function () {
                    CBThemedTextViewEditor.convertToCBMessageView(
                        spec,
                        specChangedCallback
                    );
                }
            );
        }

        {
            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            element.appendChild(
                elements[0]
            );

            let sectionElement = elements[1];

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "titleAsMarkaround",
                    "Title",
                    specChangedCallback
                )
            );

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "contentAsMarkaround",
                    "Content",
                    specChangedCallback
                )
            );
        }

        {
            let section = CBUI.createSection();

            {
                let item = CBUI.createSectionItem();
                item.appendChild(CBUIStringEditor.createEditor({
                    labelText: "URL",
                    propertyName: "URL",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }).element);
                section.appendChild(item);
            }

            element.appendChild(section);
            element.appendChild(CBUI.createHalfSpace());
        }

        {
            let section = CBUI.createSection();

            /* titleColor */
            {
                let item = CBUI.createSectionItem();
                item.appendChild(CBUIStringEditor.createEditor({
                    labelText: "Title Color",
                    propertyName: "titleColor",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }).element);
                section.appendChild(item);
            }

            /* contentColor */
            {
                let item = CBUI.createSectionItem();
                item.appendChild(CBUIStringEditor.createEditor({
                    labelText: "Content Color",
                    propertyName: "contentColor",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }).element);
                section.appendChild(item);
            }

            /* center */
            {
                let item = CBUI.createSectionItem();
                item.appendChild(CBUIBooleanEditor.create({
                    labelText: "Center",
                    propertyName: "center",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }).element);
                section.appendChild(item);
            }

            element.appendChild(section);
            element.appendChild(CBUI.createHalfSpace());
        }

        {
            let section = CBUI.createSection();

            {
                let item = CBUI.createSectionItem();

                item.appendChild(CBUIStringEditor.createEditor({
                    labelText: "Styles Template",
                    propertyName: "stylesTemplate",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }).element);
                section.appendChild(item);
            }

            element.appendChild(section);
            element.appendChild(CBUI.createHalfSpace());
        }

        return element;
    },
    /* CBUISpecEditor_createEditorElement() */



    /**
     * @param string? spec.contentAsMarkaround
     * @param string? spec.titleAsMarkaround
     *
     * @return string|undefined
     */
    CBUISpec_toDescription: function (spec) {
        return spec.titleAsMarkaround || spec.contentAsMarkaround;
    },
    /* CBUISpec_toDescription() */

};
