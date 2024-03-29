"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBYouTubeViewEditor */
/* globals
    CBAjax,
    CBModel,
    CBUI,
    CBUIPanel,
    CBUISelector,
    CBUIStringEditor2,
*/

var CBYouTubeViewEditor = {

    /**
     * @param object args
     *
     *      {
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @return Element
     */
    CBUISpecEditor_createEditorElement: function (args) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;

        let elements = CBUI.createElementTree(
            "CBYouTubeViewEditor",
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let element = elements[0];
        let sectionElement = elements[2];

        /* video id */
        {
            let timeoutID;
            let videoIDEditor = CBUIStringEditor2.create();

            sectionElement.appendChild(
                videoIDEditor.CBUIStringEditor2_getElement()
            );

            videoIDEditor.CBUIStringEditor2_setTitle(
                "Video ID or URL"
            );

            videoIDEditor.CBUIStringEditor2_setValue(
                CBModel.valueToString(
                    spec,
                    "videoID"
                )
            );

            /**
             * @NOTE 2020_05_06
             *
             *      This function is a first draft. What happens if additional
             *      input is provided while a check is being performed has not
             *      been tested.
             *
             *      Also, this sort of "wait one second then do" is a common
             *      pattern that needs to be solidified and documented.
             */
            videoIDEditor.CBUIStringEditor2_setChangedEventListener(
                function () {
                    videoIDEditor.CBUIStringEditor2_setTitle(
                        "Video ID (checking)"
                    );

                    if (timeoutID !== undefined) {
                        window.clearTimeout(
                            timeoutID
                        );
                    }

                    timeoutID = window.setTimeout(
                        check,
                        1000
                    );

                    /**
                     * @return undefined
                     */
                    function check() {
                        timeoutID = undefined;

                        CBAjax.call(
                            "CBYouTubeViewEditor",
                            "checkVideoID",
                            {
                                suggestedValue: (
                                    videoIDEditor.CBUIStringEditor2_getValue()
                                ),
                            }
                        ).then(
                            function (response) {
                                if (response.isValid) {
                                    spec.videoID = response.videoID;

                                    videoIDEditor.CBUIStringEditor2_setTitle(
                                        "Video ID (accepted)"
                                    );

                                    specChangedCallback();
                                } else {
                                    spec.videoID = undefined;

                                    videoIDEditor.CBUIStringEditor2_setTitle(
                                        "Video ID (not valid)"
                                    );
                                }
                            }
                        ).catch(
                            function (error) {
                                CBUIPanel.displayAndReportError(
                                    error
                                );
                            }
                        );
                    }
                }
            );
        }
        /* video id */


        /* caption */
        {
            let stringEditor = CBUIStringEditor2.create();

            stringEditor.CBUIStringEditor2_initializeObjectPropertyEditor(
                spec,
                "captionAsMessage",
                "Caption",
                specChangedCallback
            );

            sectionElement.appendChild(
                stringEditor.CBUIStringEditor2_getElement()
            );
        }
        /* caption */


        /* max width */
        {
            let item = CBUI.createSectionItem();

            item.appendChild(
                CBUISelector.create(
                    {
                        labelText: "Maximum Display Width",
                        options: [
                            {
                                title: "320 CSS pixels",
                                value: "320",
                            },
                            {
                                title: "480 CSS pixels",
                                value: "480",
                            },
                            {
                                title: "640 CSS pixels",
                                value: "640",
                            },
                            {
                                title: "800 CSS pixels (default)",
                            },
                            {
                                title: "960 CSS pixels",
                                value: "960",
                            },
                            {
                                title: "1280 CSS pixels",
                                value: "1280",
                            },
                            {
                                title: "Page Width",
                                description: (
                                    "The uploaded image will always use " +
                                    "the full width of the page regardless " +
                                    "of its size."
                                ),
                                value: "page",
                            },
                        ],
                        propertyName: "width",
                        spec: args.spec,
                        specChangedCallback: args.specChangedCallback,
                    }
                ).element
            );

            sectionElement.appendChild(item);
        }

        return element;
    },
    /* CBViewPageEditor_createEditor() */



    /**
     * @param object spec
     *
     *      {
     *          captionAsMessage: string?
     *      }
     *
     * @return string|undefined
     */
    CBUISpec_toDescription: function (spec) {
        return spec.captionAsMessage;
    },
    /* CBUISpec_toDescription() */
};
