"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBThemedTextViewEditor */
/* globals
    CBErrorHandler,
    CBUI,
    CBUIBooleanEditor,
    CBUINavigationView,
    CBUIPanel,
    CBUISectionItem4,
    CBUISpecClipboard,
    CBUISpecEditor,
    CBUIStringEditor,
    CBUIStringsPart,
    Colby,
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

                return Colby.callAjaxFunction(
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
                CBErrorHandler.displayAndReport(error);
            }
        );
    },
    /* convertToCBMessageView() */



    /**
     * @param object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    createEditor: function(args) {
        var element = document.createElement("div");
        element.className = "CBThemedTextViewEditor";

        element.appendChild(CBUI.createHalfSpace());

        {
            let sectionElement = CBUI.createSection();

            {
                let sectionItem = CBUISectionItem4.create();
                sectionItem.callback = function () {
                    CBThemedTextViewEditor.convertToCBMessageView(
                        args.spec,
                        args.specChangedCallback
                    );
                };

                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Convert to CBMessageView";

                stringsPart.element.classList.add("action");

                sectionItem.appendPart(stringsPart);
                sectionElement.appendChild(sectionItem.element);
            }

            element.appendChild(sectionElement);
            element.appendChild(CBUI.createHalfSpace());
        }

        {
            let section = CBUI.createSection();

            {
                let item = CBUI.createSectionItem();
                item.appendChild(CBUIStringEditor.createEditor({
                    labelText: "Title",
                    propertyName: "titleAsMarkaround",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }).element);
                section.appendChild(item);
            }

            {
                let item = CBUI.createSectionItem();
                item.appendChild(CBUIStringEditor.createEditor({
                    labelText: "Content",
                    propertyName: "contentAsMarkaround",
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
    /* createEditor() */



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
