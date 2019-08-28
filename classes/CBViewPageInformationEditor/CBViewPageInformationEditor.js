"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBViewPageInformationEditor */
/* globals
    CBImage,
    CBModel,
    CBUI,
    CBUIActionLink,
    CBUIBooleanEditor,
    CBUIImageChooser,
    CBUISectionItem4,
    CBUISelector,
    CBUISpecPropertyEditor,
    CBUIStringEditor,
    CBUIStringsPart,
    CBUIUnixTimestampEditor,
    Colby,

    CBViewPageEditor,

    CBPageClassNamesForLayouts,
    CBUsersWhoAreAdministrators,
    CBViewPageInformationEditor_currentUserNumericID,
    CBViewPageInformationEditor_frameClassNames,
    CBViewPageInformationEditor_kindClassNames,
    CBViewPageInformationEditor_pagesAdminURL,
    CBViewPageInformationEditor_settingsClassNames,
*/

var CBViewPageInformationEditor = {

    /**
     * @param object args
     *
     *      {
     *          handleTitleChanged: function
     *          makeFrontPageCallback: function
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @return Element
     */
    createEditor: function (args) {
        var section, item;

        let element = CBUI.createElement(
            "CBViewPageInformationEditor"
        );

        element.appendChild(
            createEditor_createPropertiesSectionElement()
        );

        /**
         * imageChooser will be set in
         * createEditor_createPageThumbnailEditorElement()
         */

        let imageChooser;

        element.appendChild(
            createEditor_createPageThumbnailEditorElement()
        );

        /* actions */

        element.appendChild(CBUI.createHalfSpace());

        {
            let sectionTitleElement = CBUI.createElement("CBUI_title1");
            sectionTitleElement.textContent = "Actions";

            element.appendChild(sectionTitleElement);
        }

        section = CBUI.createSection();

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIActionLink.create(
                {
                    labelText: "Preview",
                    callback: function () {
                        var name = "preview_" + args.spec.ID;
                        window.open(
                            "/admin/pages/preview/?ID=" +
                            args.spec.ID,
                            name
                        );
                    },
                }
            ).element
        );

        section.appendChild(item);

        item = CBUI.createSectionItem();

        item.appendChild(
            CBUIActionLink.create(
                {
                    labelText: "Use as Front Page",
                    callback: args.makeFrontPageCallback,
                }
            ).element
        );

        section.appendChild(item);


        /* copy */
        {
            let sectionItem = CBUISectionItem4.create();

            sectionItem.callback = function () {
                let newID = Colby.random160();

                let URI =
                `/admin/?c=CBModelEditor&ID=${newID}&copyID=${args.spec.ID}`;

                window.location = URI;
            };

            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = "Copy";

            stringsPart.element.classList.add("action");
            sectionItem.appendPart(stringsPart);
            section.appendChild(sectionItem.element);
        }
        /* copy */


        /* move to trash */
        {
            let sectionItem = CBUISectionItem4.create();

            sectionItem.callback = function () {
                if (
                    window.confirm(
                        "Are you sure you want to move this page to the trash?"
                    )
                ) {
                    Colby.callAjaxFunction(
                        "CBPages",
                        "moveToTrash",
                        {
                            ID: args.spec.ID
                        }
                    ).then(
                        function () {
                            window.alert(
                                "The page is the trash. You will be " +
                                "redirected to pages administration."
                            );

                            window.location =
                            CBViewPageInformationEditor_pagesAdminURL;
                        }
                    ).catch(
                        function (error) {
                            Colby.displayAndReportError(error);
                        }
                    );
                }
            };

            let stringsPart = CBUIStringsPart.create();
            stringsPart.string1 = "Move to Trash";

            stringsPart.element.classList.add("action");
            sectionItem.appendPart(stringsPart);
            section.appendChild(sectionItem.element);
        }
        /* move to trash */


        element.appendChild(section);

        /* layout */

        if (args.spec.layout) {
            element.appendChild(CBUI.createHalfSpace());

            var options = CBPageClassNamesForLayouts.map(
                function (className) {
                    return {
                        title: className,
                        value: className
                    };
                }
            );

            options.unshift(
                {
                    title: "None",
                    value: undefined
                }
            );

            var editor = CBUISpecPropertyEditor.create(
                {
                    labelText: "Layout",
                    options: options,
                    propertyName: "layout",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            );

            element.appendChild(editor.element);
        }

        return element;


        /* -- closures -- -- -- -- -- */

        /**
         * @return Element
         */
        function createEditor_createPropertiesSectionElement() {
            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer"
            );

            let sectionElement = CBUI.createElement(
                "CBUI_section"
            );

            sectionContainerElement.appendChild(sectionElement);

            /* title */

            item = CBUI.createSectionItem();

            item.appendChild(
                CBUIStringEditor.createEditor(
                    {
                        labelText: "Title",
                        propertyName: 'title',
                        spec: args.spec,
                        specChangedCallback: function () {
                            args.handleTitleChanged();
                            args.specChangedCallback();
                        },
                    }
                ).element
            );

            sectionElement.appendChild(item);

            /* description */

            item = CBUI.createSectionItem();

            item.appendChild(
                CBUIStringEditor.createEditor(
                    {
                        labelText: "Description",
                        propertyName: 'description',
                        spec: args.spec,
                        specChangedCallback: args.specChangedCallback,
                    }
                ).element
            );

            sectionElement.appendChild(item);

            /* editors */

            var URIEditor = CBUIStringEditor.createEditor(
                {
                    labelText: "URI",
                    propertyName: "URI",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            );

            var publicationDateEditor = CBUIUnixTimestampEditor.create(
                {
                    labelText: "Publication Date",
                    propertyName: "publicationTimeStamp",
                    spec: args.spec,
                    specChangedCallback: args.specChangedCallback,
                }
            );

            /* isPublished */

            item = CBUI.createSectionItem();
            item.appendChild(
                CBUIBooleanEditor.create(
                    {
                        labelText: "Published",
                        propertyName: "isPublished",
                        spec: args.spec,
                        specChangedCallback: function () {
                            var URI = CBModel.valueToString(
                                args.spec,
                                "URI"
                            ).trim();

                            if (args.spec.isPublished) {
                                if (URI === "") {
                                    URIEditor.updateValueCallback(
                                        Colby.textToURI(args.spec.title)
                                    );
                                }

                                if (args.spec.publicationTimeStamp === undefined) {
                                    args.spec.publicationTimeStamp = (
                                        Math.floor(Date.now() / 1000)
                                    );

                                    publicationDateEditor.refresh();
                                }
                            }

                            args.specChangedCallback();
                        },
                    }
                ).element
            );

            sectionElement.appendChild(item);

            /* publicationTimeStamp */

            item = CBUI.createSectionItem();
            item.appendChild(publicationDateEditor.element);
            sectionElement.appendChild(item);

            /* URI */

            item = CBUI.createSectionItem();
            item.appendChild(URIEditor.element);
            sectionElement.appendChild(item);

            /* publishedBy */

            if (!args.spec.publishedBy) {
                args.spec.publishedBy =
                CBViewPageInformationEditor_currentUserNumericID;
            }

            var users = CBUsersWhoAreAdministrators.map(
                function (user) {
                    return {
                        title: user.name,
                        value: user.ID,
                    };
                }
            );

            item = CBUI.createSectionItem();

            item.appendChild(
                CBUISelector.create(
                    {
                        labelText: "Published By",
                        propertyName: "publishedBy",
                        spec: args.spec,
                        specChangedCallback: args.specChangedCallback,
                        options: users,
                    }
                ).element
            );

            sectionElement.appendChild(item);

            /* classNameForSettings */

            {
                let selector = CBUISelector.create();
                selector.title = "Page Settings";
                selector.value = args.spec.classNameForSettings;
                selector.onchange = function () {
                    args.spec.classNameForSettings = selector.value;
                    args.specChangedCallback();
                };

                let options = [
                    {
                        title: "None",
                        value: undefined,
                    },
                ];

                CBViewPageInformationEditor_settingsClassNames.forEach(
                    function (settingsClassName) {
                        options.push(
                            {
                                title: settingsClassName,
                                value: settingsClassName,
                            }
                        );
                    }
                );

                selector.options = options;

                sectionElement.appendChild(selector.element);
            }

            /* classNameForKind */

            {
                let selector = CBUISelector.create();
                selector.title = "Page Kind";
                selector.value = args.spec.classNameForKind;
                selector.onchange = function () {
                    args.spec.classNameForKind = selector.value;
                    args.specChangedCallback();
                };

                let options = [
                    {
                        title: "None",
                        value: undefined,
                    },
                ];

                CBViewPageInformationEditor_kindClassNames.forEach(
                    function (kindClassName) {
                        options.push({
                            title: kindClassName,
                            value: kindClassName,
                        });
                    }
                );

                selector.options = options;

                sectionElement.appendChild(selector.element);
            }

            /* frameClassName */

            {
                let selector = CBUISelector.create();
                selector.title = "Page Frame";
                selector.value = args.spec.frameClassName;
                selector.onchange = function () {
                    args.spec.frameClassName = selector.value;
                    args.specChangedCallback();
                };

                let options = [
                    {
                        title: "None",
                        value: undefined,
                    },
                ];

                CBViewPageInformationEditor_frameClassNames.forEach(
                    function (frameClassName) {
                        options.push(
                            {
                                title: frameClassName,
                                value: frameClassName,
                            }
                        );
                    }
                );

                selector.options = options;

                sectionElement.appendChild(selector.element);
            }

            { /* selectedMainMenuItemName */
                let sectionItemElement = CBUI.createSectionItem();
                let stringEditor = CBUIStringEditor.create();
                stringEditor.title = "Selected Main Menu Item Name";

                if (args.spec.selectedMainMenuItemName !== undefined) {
                    stringEditor.value = args.spec.selectedMainMenuItemName;
                }

                stringEditor.changed = function () {
                    args.spec.selectedMainMenuItemName = stringEditor.value;
                    args.specChangedCallback();
                };

                sectionItemElement.appendChild(stringEditor.element);
                sectionElement.appendChild(sectionItemElement);
            }

            return sectionContainerElement;
        }
        /* createEditor_createPropertiesSectionElement() */


        /**
         * @return Element
         */
        function createEditor_createPageThumbnailEditorElement() {
            let pageThumbnailEditorElement = CBUI.createElement(
                "CBViewPageInformationEditor_pageThumbnailEditor"
            );

            {
                let sectionTitleElement = CBUI.createElement("CBUI_title1");
                sectionTitleElement.textContent = "Page Thumbnail Image";

                pageThumbnailEditorElement.appendChild(sectionTitleElement);
            }

            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer"
            );

            pageThumbnailEditorElement.appendChild(sectionContainerElement);

            let sectionElement = CBUI.createElement(
                "CBUI_section"
            );

            sectionContainerElement.appendChild(sectionElement);

            /**
             * The imageChooser variable is declared in createEditor()
             */

            imageChooser = CBUIImageChooser.create();
            imageChooser.chosen = createEditor_handleImageChosen;
            imageChooser.removed = createEditor_handleImageRemoved;

            imageChooser.element.classList.add("CBUIImageChooser_thumbnail");

            CBViewPageEditor.thumbnailChangedCallback =
            createEditor_handlePageThumbnailChanged;

            item = CBUI.createSectionItem();
            item.appendChild(imageChooser.element);
            sectionElement.appendChild(item);

            if (args.spec.image) {
                imageChooser.src = CBImage.toURL(
                    args.spec.image,
                    "rw320"
                );
            }

            return pageThumbnailEditorElement;
        }
        /* createEditor_createPageThumbnailEditorElement() */


        /**
         * @return undefined
         */
        function createEditor_handleImageChosen() {
            Colby.callAjaxFunction(
                "CBImages",
                "upload",
                {},
                imageChooser.file
            ).then(
                function (imageModel) {
                    CBViewPageEditor.setThumbnailImage(imageModel);
                }
            ).catch(
                function (error) {
                    Colby.displayAndReportError(error);
                }
            );
        }
        /* createEditor_handleImageChosen() */


        /**
         * @return undefined
         */
        function createEditor_handleImageRemoved() {
            CBViewPageEditor.setThumbnailImage();
        }
        /* createEditor_handleImageRemoved() */


        /**
         * @param object args
         *
         *      {
         *          spec: object
         *          image: object
         *      }
         */
        function createEditor_handlePageThumbnailChanged(args) {
            if (args.image) {
                imageChooser.src = CBImage.toURL(args.image, "rw640");
            } else {
                imageChooser.src = "";
            }
        }
        /* createEditor_handlePageThumbnailChanged() */
    },
    /* createEditor() */
};
/* CBViewPageInformationEditor */
