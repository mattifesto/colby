"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBViewPageEditor */
/* global
    CBImage,
    CBModel,
    CBUI,
    CBUISpecArrayEditor,
    CBViewPageInformationEditor,
    Colby,

    CBViewPageEditor_addableClassNames,
    CBViewPageEditor_currentFrontPageID,
*/

var CBViewPageEditor = {

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
    CBUISpecEditor_createEditorElement: function (args) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;

        CBViewPageEditor.spec = spec;
        CBViewPageEditor.specChangedCallback = specChangedCallback;

        var editorContainer = document.createElement("div");

        editorContainer.classList.add("CBViewPageEditor");

        if (spec.ID === CBViewPageEditor_currentFrontPageID) {
            editorContainer.appendChild(
                CBUISpecEditor_createEditorElement_createFrontPageNotificationElement()
            );
        }

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
                CBViewPageInformationEditor.createEditor(
                    {
                        handleTitleChanged: handleTitleChanged,
                        makeFrontPageCallback: makeFrontPageCallback,
                        spec: spec,
                        specChangedCallback: specChangedCallback,
                    }
                )
            );

            editorContainer.appendChild(CBUI.createHalfSpace());
        }
        /* CBViewPageInformationEditor */


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
        function CBUISpecEditor_createEditorElement_createFrontPageNotificationElement() {
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
        /* CBUISpecEditor_createEditorElement_createFrontPageNotificationElement() */
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
    handleTitleChanged: function (args) {

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
    makeFrontPage: function (args) {
        if (
            window.confirm(
                "Are you sure you want to use this page as the front page?"
            )
        ) {
            Colby.callAjaxFunction("CBSitePreferences", "setFrontPageID",
                {
                    ID: args.ID
                }
            ).then(
                function (response) {
                    Colby.alert(response.message);
                }
            ).catch(
                Colby.displayAndReportError
            );
        }
    },
    /* makeFrontPage() */


    /**
     * @param model? image
     *
     * @return undefined
     */
    setThumbnailImage: function (image) {
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

        var callback = CBViewPageEditor.thumbnailChangedCallback;

        if (callback) {
            callback(
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
    suggestThumbnailImage: function (image) {
        var spec = CBViewPageEditor.spec;

        if (spec && !spec.image && !spec.thumbnailURL) {
            CBViewPageEditor.setThumbnailImage(image);
        }
    },
    /* suggestThumbnailImage() */
};
/* CBViewPageEditor */
