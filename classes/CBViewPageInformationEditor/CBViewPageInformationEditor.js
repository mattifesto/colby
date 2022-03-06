"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBViewPageInformationEditor */
/* globals
    CBAjax,
    CBConvert,
    CBImage,
    CBModel,
    CBUI,
    CBUIActionLink,
    CBUIBooleanEditor,
    CBUIImageChooser,
    CBUIPanel,
    CBUISectionItem4,
    CBUISelector,
    CBUISpecPropertyEditor,
    CBUIStringEditor2,
    CBUIStringsPart,
    CBUIUnixTimestampEditor,
    Colby,

    CBViewPageEditor,

    CBPageClassNamesForLayouts,
    CBViewPageInformationEditor_administrators,
    CBViewPageInformationEditor_currentUserCBID,
    CBViewPageInformationEditor_frameClassNames,
    CBViewPageInformationEditor_kindClassNames,
    CBViewPageInformationEditor_pagesAdminURL,
    CBViewPageInformationEditor_settingsClassNames,
*/



/**
 * @deprecated 2020_12_14
 *
 *      All of the code in this class belongs in the CBViewPageEditor class.
 *      This is not an editor of a CBViewPageInformation spec.
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
    CBViewPageEditor_createEditor(
        args
    ) {
        let handleTitleChanged = args.handleTitleChanged;
        let makeFrontPageCallback = args.makeFrontPageCallback;
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;

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
                                var name = "preview_" + spec.ID;
                                window.open(
                                    "/admin/pages/preview/?ID=" +
                                    spec.ID,
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
                            callback: makeFrontPageCallback,
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
                    `/admin/?c=CBModelEditor&ID=${newID}&copyID=${spec.ID}`;

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
                    let wasConfirmed = window.confirm(
                        CBConvert.stringToCleanLine(`

                            Are you sure you want to move this page to the
                            trash?

                        `)
                    );

                    if (wasConfirmed) {
                        CBAjax.call(
                            "CBPages",
                            "moveToTrash",
                            {
                                ID: spec.ID
                            }
                        ).then(
                            function () {
                                window.alert(
                                    CBConvert.stringToCleanLine(`

                                        The page is the trash. You will be
                                        redirected to pages administration.

                                    `)
                                );

                                window.location = (
                                    CBViewPageInformationEditor_pagesAdminURL
                                );
                            }
                        ).catch(
                            function (error) {
                                CBUIPanel.displayAndReportError(error);
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

            if (spec.layout) {
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
                        spec: spec,
                        specChangedCallback: specChangedCallback,
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

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "title",
                    "Title",
                    function () {
                        handleTitleChanged();
                        specChangedCallback();
                    }
                )
            );

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "description",
                    "Description",
                    specChangedCallback
                )
            );


            /* editors */

            let URIEditor2 = CBUIStringEditor2.create();

            URIEditor2.CBUIStringEditor2_setTitle(
                "URI"
            );

            URIEditor2.CBUIStringEditor2_setValue(
                CBModel.valueToString(
                    spec,
                    "URI"
                )
            );

            URIEditor2.CBUIStringEditor2_setChangedEventListener(
                function ()
                {
                    let editorElement =
                    URIEditor2.CBUIStringEditor2_getElement();

                    let enteredURI =
                    URIEditor2.CBUIStringEditor2_getValue();

                    enteredURI =
                    enteredURI.trim();

                    let validatedURI =
                    enteredURI.substring(
                        0,
                        100
                    );

                    validatedURI =
                    CBConvert.stringToURI(
                        validatedURI
                    );

                    if (
                        validatedURI === enteredURI
                    ) {
                        editorElement.classList.remove(
                            "CBUIStringEditor2_error"
                        );

                        spec.URI =
                        validatedURI;

                        specChangedCallback();
                    }

                    else {
                        editorElement.classList.add(
                            "CBUIStringEditor2_error"
                        );
                    }
                }
            );

            var publicationDateEditor =
            CBUIUnixTimestampEditor.create(
                {
                    labelText:
                    "Publication Date",

                    propertyName:
                    "publicationTimeStamp",

                    spec:
                    spec,

                    specChangedCallback:
                    specChangedCallback,
                }
            );


            /* is published block */
            {
                let item =
                CBUI.createSectionItem();

                let isPublishedEditor =
                CBUIBooleanEditor.create(
                    {
                        labelText:
                        "Published",

                        propertyName:
                        "isPublished",

                        spec:
                        spec,

                        specChangedCallback:
                        function ()
                        {
                            if (
                                spec.isPublished
                            ) {
                                let currentURIPropertyValue =
                                CBModel.valueToString(
                                    spec,
                                    "URI"
                                ).trim();

                                if (
                                    currentURIPropertyValue === ""
                                ) {
                                    let currentTitlePropertyValue =
                                    CBModel.valueToString(
                                        spec,
                                        "title"
                                    ).trim();

                                    /**
                                     * CBViewPage URIs are currently allowed to
                                     * be at most 100 characters long.
                                     */

                                    let newURIPropertyValue =
                                    currentTitlePropertyValue.substring(
                                        0,
                                        100
                                    );

                                    newURIPropertyValue =
                                    CBConvert.stringToStub(
                                        newURIPropertyValue
                                    );

                                    spec.URI = newURIPropertyValue;

                                    URIEditor2.CBUIStringEditor2_setValue(
                                        spec.URI
                                    );
                                }

                                if (
                                    spec.publicationTimeStamp === undefined
                                ) {
                                    spec.publicationTimeStamp =
                                    Math.floor(
                                        Date.now() / 1000
                                    );

                                    publicationDateEditor.refresh();
                                }
                            }

                            specChangedCallback();
                        },
                        // specChangedCallback property value
                    }
                    // object
                );
                // CBUIBooleanEditor.create()

                item.appendChild(
                    isPublishedEditor.element
                );

                sectionElement.appendChild(
                    item
                );
            }
            /* is published block */


            sectionElement.appendChild(
                publicationDateEditor.element
            );


            sectionElement.appendChild(
                URIEditor2.CBUIStringEditor2_getElement()
            );


            /* published by */
            {
                if (!spec.publishedByUserCBID) {
                    spec.publishedByUserCBID =
                    CBViewPageInformationEditor_currentUserCBID;
                }

                var users = CBViewPageInformationEditor_administrators.map(
                    function (user) {
                        return {
                            title: user.name,
                            value: user.userCBID,
                        };
                    }
                );

                let item = CBUI.createSectionItem();

                item.appendChild(
                    CBUISelector.create(
                        {
                            labelText: "Published By",
                            propertyName: "publishedByUserCBID",
                            spec: spec,
                            specChangedCallback: specChangedCallback,
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
                selector.value = spec.classNameForSettings;
                selector.onchange = function () {
                    spec.classNameForSettings = selector.value;
                    specChangedCallback();
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
                selector.value = spec.classNameForKind;
                selector.onchange = function () {
                    spec.classNameForKind = selector.value;
                    specChangedCallback();
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
                selector.value = spec.frameClassName;
                selector.onchange = function () {
                    spec.frameClassName = selector.value;
                    specChangedCallback();
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

            sectionElement.appendChild(
                CBUIStringEditor2.createObjectPropertyEditorElement(
                    spec,
                    "selectedMenuItemNames",
                    "Selected Menu Item Names",
                    specChangedCallback
                )
            );

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
             * The imageChooser variable is declared at the beginning of
             * CBViewPageEditor_createEditor().
             */

            imageChooser = CBUIImageChooser.create();
            imageChooser.chosen = createEditor_handleImageChosen;
            imageChooser.removed = createEditor_handleImageRemoved;

            imageChooser.element.classList.add("CBUIImageChooser_thumbnail");

            CBViewPageEditor.thumbnailChangedCallback = (
                createEditor_handlePageThumbnailChanged
            );

            let item = CBUI.createSectionItem();

            item.appendChild(imageChooser.element);

            sectionElement.appendChild(item);

            if (spec.image) {
                imageChooser.src = CBImage.toURL(
                    spec.image,
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
            CBAjax.call(
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
                    CBUIPanel.displayAndReportError(error);
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
        function createEditor_handlePageThumbnailChanged(
            args
        ) {
            let image = args.image;

            if (image) {
                imageChooser.src = CBImage.toURL(
                    image,
                    "rw640"
                );
            } else {
                imageChooser.src = "";
            }
        }
        /* createEditor_handlePageThumbnailChanged() */

    },
    /* CBViewPageEditor_createEditor() */

};
/* CBViewPageInformationEditor */
