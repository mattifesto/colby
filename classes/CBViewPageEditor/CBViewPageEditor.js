"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBViewPageEditor */
/* global
    CBImage,
    CBUI,
    CBUISpecArrayEditor,
    CBViewPageInformationEditor,
    Colby,

    CBViewPageEditor_addableClassNames,
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
     *          navigateToItemCallback: function
     *          spec: model
     *          specChangedCallback: function
     *      }
     *
     * @return Element
     */
    createEditor: function (args) {
        CBViewPageEditor.spec = args.spec;
        CBViewPageEditor.specChangedCallback = args.specChangedCallback;

        var editorContainer = document.createElement("div");

        editorContainer.classList.add("CBViewPageEditor");

        editorContainer.appendChild(
            CBUI.createHalfSpace()
        );


        /* CBViewPageInformationEditor */
        {
            let handleTitleChanged =
            CBViewPageEditor.handleTitleChanged.bind(
                undefined,
                {
                    spec: args.spec,
                }
            );

            let makeFrontPageCallback =
            CBViewPageEditor.makeFrontPage.bind(
                undefined,
                {
                    ID: args.spec.ID,
                }
            );

            editorContainer.appendChild(
                CBViewPageInformationEditor.createEditor(
                    {
                        handleTitleChanged: handleTitleChanged,
                        makeFrontPageCallback: makeFrontPageCallback,
                        navigateToItemCallback: args.navigateToItemCallback,
                        spec: args.spec,
                        specChangedCallback: args.specChangedCallback,
                    }
                )
            );

            editorContainer.appendChild(CBUI.createHalfSpace());
        }
        /* CBViewPageInformationEditor */


        /* views */
        {
            if (args.spec.sections === undefined) {
                args.spec.sections = [];
            }

            let editor = CBUISpecArrayEditor.create({
                specs: args.spec.sections,
                specsChangedCallback: args.specChangedCallback,
                addableClassNames: CBViewPageEditor_addableClassNames,
                navigateToItemCallback: args.navigateToItemCallback,
            });

            editor.title = "Views";

            editorContainer.appendChild(editor.element);
            editorContainer.appendChild(CBUI.createHalfSpace());
        }
        /* views */


        CBViewPageEditor.handleTitleChanged(
            {
                spec: args.spec,
            }
        );

        editorContainer.appendChild(
            CBUI.createHalfSpace()
        );

        return editorContainer;
    },
    /* createEditor() */


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
        var title = args.spec.title || "";
        title = title.trim();
        title = (title.length > 0) ? ": " + title: "";
        document.title = "Page Editor" + title;
    },

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
        if (window.confirm("Are you sure you want to use this page as the front page?")) {
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
