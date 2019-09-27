"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBViewPageInformationEditor */
/* globals
    CBErrorHandler,
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
        let editorElement = CBUI.createElement(
            "CBViewPageInformationEditor"
        );

        editorElement.appendChild(
            createEditor_createPropertiesSectionElement()
        );

        /**
         * imageChooser will be set in
         * createEditor_createPageThumbnailEditorElement()
         */

        let imageChooser;

        editorElement.appendChild(
            createEditor_createPageThumbnailEditorElement()
        );

        editorElement.appendChild(
            createEditor_createActionsElement()
        );

        editorElement.appendChild(
            createEditor_createLayoutEditorElement()
        );

        return editorElement;


        /* -- closures -- -- -- -- -- */

        /**
         * @return Element
         */
        function createEditor_createActionsElement() {
            let actionsElement = CBUI.createElement(
                "CBViewPageInformationEditor_actions"
            );

            {
                let sectionTitleElement = CBUI.createElement("CBUI_title1");
                sectionTitleElement.textContent = "Actions";

                actionsElement.appendChild(sectionTitleElement);
            }

            let sectionContainerElement = CBUI.createElement(
                "CBUI_sectionContainer"
            );

            actionsElement.appendChild(sectionContainerElement);

            let sectionElement = CBUI.createElement(
                "CBUI_section"
            );

            sectionContainerElement.appendChild(sectionElement);

            /* preview */
            {
                let item = CBUI.createSectionItem();

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

                sectionElement.appendChild(item);
            }
            /* preview */


            /* use as front page */
            {
                let item = CBUI.createSectionItem();

                item.appendChild(
                    CBUIActionLink.create(
                        {
                            labelText: "Use as Front Page",
                            callback: args.makeFrontPageCallback,
                        }
                    ).element
                );

                sectionElement.appendChild(item);
            }
            /* use as front page */


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

                sectionElement.appendChild(sectionItem.element);
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
                                CBErrorHandler.displayAndReport(error);
                            }
                        );
                    }
                };

                let stringsPart = CBUIStringsPart.create();
                stringsPart.string1 = "Move to Trash";

                stringsPart.element.classList.add("action");
                sectionItem.appendPart(stringsPart);

                sectionElement.appendChild(sectionItem.element);
            }
            /* move to trash */


            return actionsElement;
        }
        /* createEditor_createActionsElement() */


        /**
         * @return Element
         */
        function createEditor_createLayoutEditorElement() {
            let layoutEditorElement = CBUI.createElement(
                "CBViewPageInformationEditor_layoutEditor"
            );

            if (args.spec.layout) {
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

                layoutEditorElement.appendChild(editor.element);
            }

            return layoutEditorElement;
        }
        /* createEditor_createLayoutEditorElement() */


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
            {
                let item = CBUI.createSectionItem();

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
            }
            /* title */


            /* description */
            {
                let item = CBUI.createSectionItem();

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
            }
            /* description */


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


            /* is published */
            {
                let item = CBUI.createSectionItem();

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
            }
            /* is published */



            /* publication timestamp */
            {
                let item = CBUI.createSectionItem();

                item.appendChild(publicationDateEditor.element);

                sectionElement.appendChild(item);
            }
            /* publication timestamp */



            /* URI */
            {
                let item = CBUI.createSectionItem();

                item.appendChild(URIEditor.element);

                sectionElement.appendChild(item);
            }
            /* URI */



            /* published by */
            {
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

                let item = CBUI.createSectionItem();

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
            }
            /* published by */


            /* class name for settings */
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
            /* class name for settings */


            /* class name for kind */
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
            /* class name for kind */


            /* frame class name */
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
            /* frame class name */


            /* selected main menu item name */
            {
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
            /* selected main menu item name */

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

            let item = CBUI.createSectionItem();

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
                    CBErrorHandler.displayAndReport(error);
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
