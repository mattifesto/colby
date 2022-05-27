/* global
    CB_UI_StringEditor,
    CBAjax,
    CBConvert,
    CBErrorHandler,
    CBImage,
    CBModel,
    CBUI,
    CBUIButton,
    CBUIPanel,
    CBUISpecArrayEditor,
    CBViewPageInformationEditor,

    CBViewPageEditor_addableClassNames,
    CBViewPageEditor_currentFrontPageID,
*/

(function () {
    "use strict";

    let CBViewPageEditor = {

        /**
         * This variable will be set to the spec as soon as the editor is created.
         */
        spec: undefined,



        /**
         * This variable will be set to the specChangedCallback as soon as the
         * editor is created.
         */
        specChangedCallback: undefined,



        /**
         * This will be set to a function by the CBViewPageInformationEditor.
         */
        thumbnailChangedCallback: undefined,



        /**
         * @param object args
         *
         *      {
         *          spec: model
         *          specChangedCallback: function
         *      }
         *
         * @return Element
         */
        CBUISpecEditor_createEditorElement(
            args
        ) {
            let spec = args.spec;
            let specChangedCallback = args.specChangedCallback;

            CBViewPageEditor.spec = spec;
            CBViewPageEditor.specChangedCallback = specChangedCallback;

            var editorContainer = document.createElement("div");

            editorContainer.classList.add("CBViewPageEditor");

            if (spec.ID === CBViewPageEditor_currentFrontPageID) {
                editorContainer.appendChild(
                    createFrontPageNotificationElement()
                );
            }

            editorContainer.append(
                CBViewPageEditor_createAdministrativeTitleEditorElement(
                    spec,
                    specChangedCallback
                )
            );

            /* CBViewPageInformationEditor */
            {
                let handleTitleChanged =
                CBViewPageEditor.handleTitleChanged.bind(
                    undefined,
                    {
                        spec: spec,
                    }
                );

                let makeFrontPageCallback =
                CBViewPageEditor.makeFrontPage.bind(
                    undefined,
                    {
                        ID: spec.ID,
                    }
                );

                editorContainer.appendChild(
                    CBViewPageInformationEditor.CBViewPageEditor_createEditor(
                        {
                            handleTitleChanged: handleTitleChanged,
                            makeFrontPageCallback: makeFrontPageCallback,
                            spec: spec,
                            specChangedCallback: specChangedCallback,
                        }
                    )
                );
            }
            /* CBViewPageInformationEditor */


            /* use views as left sidebar content */
            {
                let button = CBUIButton.create();

                button.CBUIButton_setTextContent(
                    "Use Views as Left Sidebar Content"
                );

                editorContainer.append(
                    button.CBUIButton_getElement()
                );

                let pageModelCBID = spec.ID;

                button.CBUIButton_addClickEventListener(
                    function () {
                        useViewsAsLeftSidebarContent(
                            button,
                            pageModelCBID
                        );
                    }
                );
            }
            /* use views as left sidebar content */


            /* use views as right sidebar content */
            {
                let button = CBUIButton.create();

                button.CBUIButton_setTextContent(
                    "Use Views as Right Sidebar Content"
                );

                editorContainer.append(
                    button.CBUIButton_getElement()
                );

                let pageModelCBID = spec.ID;

                button.CBUIButton_addClickEventListener(
                    function () {
                        useViewsAsRightSidebarContent(
                            button,
                            pageModelCBID
                        );
                    }
                );
            }
            /* use views as right sidebar content */


            /* views */
            {
                if (spec.sections === undefined) {
                    spec.sections = [];
                }

                let titleElement = CBUI.createElement("CBUI_title1");
                titleElement.textContent = "Views";

                editorContainer.appendChild(titleElement);

                let editor = CBUISpecArrayEditor.create(
                    {
                        specs: spec.sections,
                        specsChangedCallback: specChangedCallback,
                        addableClassNames: CBViewPageEditor_addableClassNames,
                    }
                );

                editorContainer.appendChild(editor.element);

                editorContainer.appendChild(
                    CBUI.createHalfSpace()
                );
            }
            /* views */


            CBViewPageEditor.handleTitleChanged(
                {
                    spec: spec,
                }
            );

            editorContainer.appendChild(
                CBUI.createHalfSpace()
            );

            return editorContainer;



            /* -- closures -- -- -- -- -- */



            /**
             * @return Element
             */
            function createFrontPageNotificationElement() {
                let element = CBUI.createElement(
                    "CBViewPageEditor_frontPageNotification " +
                    "CBUI_sectionContainer"
                );

                let sectionElement = CBUI.createElement(
                    "CBUI_section"
                );

                element.appendChild(sectionElement);

                let textContainerElement = CBUI.createElement(
                    "CBUI_container_topAndBottom"
                );

                sectionElement.appendChild(textContainerElement);

                let textElement = CBUI.createElement();

                textElement.textContent = "This page is currently the front page.";

                textContainerElement.appendChild(textElement);

                return element;
            }
            /* createFrontPageNotificationElement() */

        },
        /* CBUISpecEditor_createEditorElement() */



        /**
         * @param object args
         *
         *      {
         *          spec: object
         *      }
         *
         * @return undefined
         */
        handleTitleChanged(
            args
        ) {
            /**
             * Only change the page title if this is a CBModelEditor admin page.
             */

            let elements = document.getElementsByClassName(
                "CBModelEditor"
            );

            if (elements.length === 0) {
                return;
            }

            /**
             * Change the page title.
             */

            let title = CBModel.valueToString(
                args,
                "spec.title"
            ).trim();

            let documentTitle = "Page Editor";

            if (title.length > 0) {
                documentTitle = documentTitle + ": " + title;
            }

            document.title = documentTitle;
        },
        /* handleTitleChanged() */



        /**
         * @param object args
         *
         *      {
         *          ID: ID
         *      }
         *
         * @return undefined
         */
        makeFrontPage(
            args
        ) {
            if (
                window.confirm(
                    "Are you sure you want to use this page as the front page?"
                )
            ) {
                CBAjax.call(
                    "CBSitePreferences",
                    "setFrontPageID",
                    {
                        ID: args.ID
                    }
                ).then(
                    function (response) {
                        CBUIPanel.displayText(response.message);
                    }
                ).catch(
                    function (error) {
                        CBUIPanel.displayError(error);

                        CBErrorHandler.report(
                            error
                        );
                    }
                );
            }
        },
        /* makeFrontPage() */



        /**
         * @param model? image
         *
         * @return undefined
         */
        setThumbnailImage(
            image
        ) {
            var spec = CBViewPageEditor.spec;

            if (spec === undefined) {
                return;
            }

            if (image === undefined) {
                spec.image = undefined;
                spec.thumbnailURL = undefined;
            } else {
                spec.image = image;
                spec.thumbnailURL = CBImage.toURL(
                    image,
                    "rw640"
                );
            }

            if (CBViewPageEditor.thumbnailChangedCallback) {
                CBViewPageEditor.thumbnailChangedCallback(
                    {
                        spec: spec,
                        image: image
                    }
                );
            }

            CBViewPageEditor.specChangedCallback.call();
        },
        /* setThumbnailImage() */



        /**
         * @param model image
         *
         * @return undefined
         */
        suggestThumbnailImage(
            image
        ) {
            var spec = CBViewPageEditor.spec;

            if (spec && !spec.image && !spec.thumbnailURL) {
                CBViewPageEditor.setThumbnailImage(image);
            }
        },
        /* suggestThumbnailImage() */



    };
    /* CBViewPageEditor */



    window.CBViewPageEditor = CBViewPageEditor;



    /**
     * @param object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function
    CBViewPageEditor_createAdministrativeTitleEditorElement(
        spec,
        specChangedCallback
    ) // -> Element
    {
        let stringEditor =
        CB_UI_StringEditor.create();

        stringEditor.CB_UI_StringEditor_setTitle(
            "Administrative Title"
        );

        stringEditor.CB_UI_StringEditor_setValue(
            CBModel.valueToString(
                spec,
                'CBViewPage_administrativeTitle_property'
            )
        );

        stringEditor.CB_UI_StringEditor_setChangedEventListener(
            function (
            ) // -> undefined
            {
                let newAdministrativeTitle =
                stringEditor.CB_UI_StringEditor_getValue();

                spec.CBViewPage_administrativeTitle_property =
                newAdministrativeTitle;

                specChangedCallback();
            }
        );

        return (
            stringEditor.CB_UI_StringEditor_getElement()
        );
    }
    // CBViewPageEditor_createAdministrativeTitleEditorElement()



    /**
     * @param object button
     * @param CBID pageModelCBID
     *
     * @return undefined
     */
    async function
    useViewsAsLeftSidebarContent(
        button,
        pageModelCBID
    ) {
        if (
            button.CBUIButton_getIsDisabled()
        ) {
            return;
        }

        {
            let userConfirmed = window.confirm(
                CBConvert.stringToCleanLine(`

                    Are you sure you want to use the views of this page as the
                    content of the left sidebar?

                `)
            );

            if (
                !userConfirmed
            ) {
                return;
            }
        }

        button.CBUIButton_setIsDisabled(
            true
        );

        try {
            await CBAjax.call2(
                "CB_Ajax_StandardPageFrame_SetLeftSidebarPage",
                {
                    CB_Ajax_StandardPageFrame_SetLeftSidebarPage_pageModelCBID: (
                        pageModelCBID
                    ),
                }
            );
        } catch (
            error
        ) {
            CBErrorHandler.report(
                error
            );
        } finally {
            button.CBUIButton_setIsDisabled(
                false
            );
        }
    }
    /* useViewsAsLeftSidebarContent() */



    /**
     * @param object button
     * @param CBID pageModelCBID
     *
     * @return undefined
     */
    async function
    useViewsAsRightSidebarContent(
        button,
        pageModelCBID
    ) {
        if (
            button.CBUIButton_getIsDisabled()
        ) {
            return;
        }

        {
            let userConfirmed = window.confirm(
                CBConvert.stringToCleanLine(`

                    Are you sure you want to use the views of this page as the
                    content of the right sidebar?

                `)
            );

            if (
                !userConfirmed
            ) {
                return;
            }
        }

        button.CBUIButton_setIsDisabled(
            true
        );

        try {
            await CBAjax.call(
                "CB_StandardPageFrame",
                "setRightSidebarPageModelCBID",
                {
                    pageModelCBID,
                }
            );
        } catch (
            error
        ) {
            CBErrorHandler.report(
                error
            );
        } finally {
            button.CBUIButton_setIsDisabled(
                false
            );
        }
    }
    /* useViewsAsRightSidebarContent() */

})();
