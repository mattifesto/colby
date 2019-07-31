"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBYouTubeViewEditor */
/* globals
    CBUI,
    CBUISelector,
    CBUIStringEditor,
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
    createEditor: function (args) {
        var element = document.createElement("div");
        element.className = "CBYouTubeViewEditor";

        element.appendChild(
            CBUI.createHalfSpace()
        );

        let sectionElement = CBUI.createSection();

        /* video id */
        {
            let item = CBUI.createSectionItem();

            item.appendChild(
                CBUIStringEditor.createEditor(
                    {
                        labelText: "Video ID",
                        propertyName: "videoID",
                        spec: args.spec,
                        specChangedCallback: args.specChangedCallback,
                    }
                ).element
            );

            sectionElement.appendChild(item);
        }

        /* caption */
        {
            let item = CBUI.createSectionItem();

            item.appendChild(
                CBUIStringEditor.createEditor(
                    {
                        labelText: "Caption",
                        propertyName: "captionAsMessage",
                        spec: args.spec,
                        specChangedCallback: args.specChangedCallback,
                    }
                ).element
            );

            sectionElement.appendChild(item);
        }

        /* max width */
        {
            let item = CBUI.createSectionItem();

            item.appendChild(
                CBUISelector.create(
                    {
                        labelText: "Maximum Display Width",
                        navigateToItemCallback: args.navigateToItemCallback,
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

        element.appendChild(sectionElement);

        element.appendChild(
            CBUI.createHalfSpace()
        );

        return element;
    },
    /* createEditor() */


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
