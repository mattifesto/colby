"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBArtworkElement,
    CBImage,
    CBMessageMarkup,
    CBModel,
    CBUI,
    CBUIExpander,
    CBUIImageChooser,
    CBUINavigationArrowPart,
    CBUINavigationView,
    CBUIPanel,
    CBUISectionItem4,
    CBUIStringEditor,
    CBUIStringsPart,
    Colby,

    CBModelInspector_associatedImageModel,
    CBModelInspector_modelID,
*/

var CBModelInspector = {

    /**
     * @return undefined
     */
    init: function () {
        let navigator = CBUINavigationView.create();

        let spec = {
            ID: CBModelInspector_modelID,
        };

        let navigationHomeElement = document.createElement("div");
        let modelInformationElement = document.createElement("div");

        let IDDidChangeCallback = CBModelInspector.IDDidChange.bind(
            undefined,
            {
                spec: spec,
                container: modelInformationElement,
            }
        );

        navigationHomeElement.appendChild(CBUI.createHalfSpace());

        {
            let sectionElement = CBUI.createSection();
            let sectionItemElement = CBUI.createSectionItem();

            sectionItemElement.appendChild(
                CBUIStringEditor.createEditor(
                    {
                        labelText: "ID",
                        propertyName: "ID",
                        spec: spec,
                        specChangedCallback: IDDidChangeCallback,
                    }
                ).element
            );

            sectionElement.appendChild(sectionItemElement);

            navigationHomeElement.appendChild(sectionElement);
            navigationHomeElement.appendChild(CBUI.createHalfSpace());
        }

        navigationHomeElement.appendChild(modelInformationElement);
        navigationHomeElement.appendChild(CBUI.createHalfSpace());

        let mainElement = document.getElementsByTagName("main")[0];
        mainElement.appendChild(navigator.element);

        navigator.navigate(
            {
                element: navigationHomeElement,
                title: "Inspector",
            }
        );

        IDDidChangeCallback();
    },
    /* init() */


    /**
     * @param object args
     *
     *      {
     *          container: Element
     *          spec: object
     *
     *              {
     *                  ID: string
     *              }
     *      }
     *
     * @return undefined
     */
    IDDidChange: function (args) {
        let containerElement = args.container;
        let model, modelData;

        let modelID = CBModel.valueAsID(
            args.spec,
            "ID"
        );

        if (modelID === undefined) {
            document.title = "Inspector: Invalid ID";
            containerElement.textContent = "";
            return;
        }

        Colby.callAjaxFunction(
            "CBModelInspector",
            "fetchModelData",
            {
                ID: modelID,
            }
        ).then(
            function (value) {
                modelData = value;

                if (modelData.modelVersions.length > 0) {
                    model = JSON.parse(
                        modelData.modelVersions[0].modelAsJSON
                    );
                } else {
                    model = undefined;
                }

                IDDidChange_render();
            }
        ).catch(
            function (error) {
                Colby.displayAndReportError(error);
            }
        );

        return;


        /* -- closures -- -- -- -- -- */

        /**
         * @return Element
         */
        function IDDidChange_createAssociationsElement() {
            let element = CBUI.createElement(
                "CBModelInspector_associations"
            );

            element.textContent = "associations";

            return element;
        }
        /* IDDidChange_createAssociationsElement() */


        /**
         * @return undefined
         */
        function IDDidChange_render() {
            let section;

            args.container.textContent = "";

            section = CBUI.createSection();

            if (model === undefined) {
                document.title = "Inspector: " + modelData.modelID;

                section.appendChild(
                    CBUI.createKeyValueSectionItem(
                        {
                            key: "Notice",
                            value: "This ID has no model."
                        }
                    ).element
                );
            } else {
                document.title = (
                    "Inspector: " +
                    (
                        model.title ?
                        model.title.trim() :
                        model.className
                    )
                );

                if (model.className === "CBImage") {
                    let container = document.createElement("div");
                    container.style.display = "flex";
                    container.style.padding = "0 20px";
                    container.style.justifyContent = "center";

                    let artworkElement = CBArtworkElement.create({
                        URL: CBImage.toURL(model, "rw1600"),
                        aspectRatioWidth: model.width,
                        aspectRatioHeight: model.height,
                        maxWidth: 800,
                    });

                    container.appendChild(artworkElement);
                    args.container.appendChild(container);
                    args.container.appendChild(CBUI.createHalfSpace());
                }

                section.appendChild(CBUI.createKeyValueSectionItem({
                    key: "Class Name",
                    value: model.className,
                }).element);

                section.appendChild(CBUI.createKeyValueSectionItem({
                    key: "Title",
                    value: model.title,
                }).element);

                section.appendChild(CBUI.createKeyValueSectionItem({
                    key: "Description",
                    value: model.description,
                }).element);

                {
                    let sectionItem = CBUISectionItem4.create();
                    let stringsPart = CBUIStringsPart.create();
                    stringsPart.string1 = "Edit Model";

                    stringsPart.element.classList.add("action");

                    sectionItem.callback = function () {
                        window.location = (
                            '/admin/?c=CBModelEditor&ID=' +
                            model.ID
                        );
                    };

                    sectionItem.appendPart(stringsPart);
                    section.appendChild(sectionItem.element);
                }

                {
                    let sectionItem = CBUISectionItem4.create();
                    sectionItem.callback = function () {
                        confirm();
                    };

                    let stringsPart = CBUIStringsPart.create();
                    stringsPart.string1 = "Delete Model";

                    stringsPart.element.classList.add("action");

                    sectionItem.appendPart(stringsPart);
                    section.appendChild(sectionItem.element);

                    /* stage 1 */
                    let confirm = function () {
                        CBUIPanel.message = (
                            "Are you sure you want to delete this model?"
                        );

                        CBUIPanel.buttons = [
                            {
                                title: "Yes",
                                callback: deleteModel,
                            },
                            {
                                title: "No",
                            },
                        ];

                        CBUIPanel.isShowing = true;
                    };

                    /* stage 2 */
                    let deleteModel = function () {
                        CBUIPanel.message = "Deleting model...";
                        CBUIPanel.buttons = [];

                        Colby.callAjaxFunction(
                            "CBModels",
                            "deleteByID",
                            {
                                ID: model.ID,
                            }
                        ).then(
                            report
                        ).catch(
                            Colby.displayAndReportError
                        );
                    };

                    /* stage 3 */
                    let report = function () {
                        CBUIPanel.message = (
                            "The model has been deleted.\n\nPress OK to " +
                            "navigate to the models admin page."
                        );

                        CBUIPanel.buttons = [
                            {
                                title: "OK",
                                callback: navigate,
                            }
                        ];
                    };

                    /* stage 4 */
                    let navigate = function () {
                        window.location = "/admin/?c=CBModelsAdmin";
                    };
                }
            }

            args.container.appendChild(section);
            args.container.appendChild(CBUI.createHalfSpace());

            /* associated image */
            {
                let titleElement = document.createElement("div");
                titleElement.className = "CBUI_title1";
                titleElement.textContent = "Associated Image";
                args.container.appendChild(titleElement);

                let sectionContainerElement = document.createElement("div");
                sectionContainerElement.className = "CBUI_section_container";
                args.container.appendChild(sectionContainerElement);

                let sectionElement = document.createElement("div");
                sectionElement.className = "CBUI_section";
                sectionContainerElement.appendChild(sectionElement);

                let chooser = CBUIImageChooser.create();
                sectionElement.appendChild(chooser.element);

                if (CBModelInspector_associatedImageModel !== null) {
                    chooser.src = CBImage.toURL(
                        CBModelInspector_associatedImageModel,
                        "rw1600"
                    );
                }

                chooser.chosen = function (args) {
                    Colby.callAjaxFunction(
                        "CBImages",
                        "upload",
                        undefined,
                        args.file
                    ).then(
                        function (imageSpec) {
                            return Colby.callAjaxFunction(
                                "CBModelToCBImageAssociation",
                                "replaceImageID",
                                {
                                    modelID: CBModelInspector_modelID,
                                    imageID: imageSpec.ID,
                                }
                            ).then(
                                function () {
                                    chooser.src = CBImage.toURL(
                                        imageSpec,
                                        "rw1600"
                                    );
                                }
                            );
                        }
                    ).catch(
                        function (error) {
                            Colby.displayAndReportError(error);
                        }
                    );
                };
            }
            /* associated image */


            /* associations */

            args.container.appendChild(
                IDDidChange_createAssociationsElement()
            );


            /* versions */

            if (modelData.modelVersions.length > 0) {
                {
                    let titleElement = document.createElement("div");
                    titleElement.className = "CBUI_title1";
                    titleElement.textContent = "Versions";
                    args.container.appendChild(titleElement);
                }

                section = CBUI.createSection();
                let unixNow = Math.floor(Date.now() / 1000);

                modelData.modelVersions.forEach(function (version) {
                    let versionCreated = new Date(version.timestamp * 1000);
                    let info = "";

                    if (version.replaced !== null) {
                        let deathspan = (
                            unixNow - version.replaced
                        ).toLocaleString();

                        let lifespan = (
                            version.replaced - version.timestamp
                        ).toLocaleString();

                        info = `(${deathspan} / ${lifespan} ${version.action})`;
                    }

                    let sectionItem = CBUISectionItem4.create();
                    let stringsPart = CBUIStringsPart.create();
                    stringsPart.string1 = `Version ${version.version} ${info}`;

                    stringsPart.element.classList.add("titledescription");

                    Colby.requestTimeUpdate(
                        function (javascriptTimestamp) {
                            let now = new Date(javascriptTimestamp);

                            stringsPart.string2 = (
                                Colby.dateToRelativeLocaleString(
                                    versionCreated,
                                    now
                                )
                            );
                        }
                    );

                    sectionItem.callback = function () {
                        let element = document.createElement("div");

                        element.appendChild(CBUI.createHalfSpace());

                        {
                            let sectionElement = CBUI.createSection();
                            let sectionItem = CBUISectionItem4.create();
                            let stringsPart = CBUIStringsPart.create();
                            stringsPart.string1 = "Revert to This Version";

                            stringsPart.element.classList.add("action");

                            sectionItem.callback = revert;

                            sectionItem.appendPart(stringsPart);
                            sectionElement.appendChild(sectionItem.element);
                            element.appendChild(sectionElement);
                            element.appendChild(CBUI.createHalfSpace());
                        }

                        {
                            let message = CBMessageMarkup.stringToMarkup(
                                JSON.stringify(
                                    JSON.parse(version.specAsJSON),
                                    undefined,
                                    2
                                )
                            );

                            let expander = CBUIExpander.create();
                            expander.expanded = true;
                            expander.message = `

                                Spec

                                --- pre\n${message}
                                ---

                            `;

                            element.appendChild(expander.element);
                            element.appendChild(CBUI.createHalfSpace());
                        }

                        {
                            let message = CBMessageMarkup.stringToMarkup(
                                JSON.stringify(
                                    JSON.parse(
                                        version.modelAsJSON
                                    ),
                                    undefined,
                                    2
                                )
                            );

                            let expander = CBUIExpander.create();
                            expander.expanded = true;
                            expander.message = `

                                Model

                                --- pre\n${message}
                                ---

                            `;

                            element.appendChild(expander.element);
                            element.appendChild(CBUI.createHalfSpace());
                        }

                        CBUINavigationView.context.navigate(
                            {
                                element: element,
                                title: `Version ${version.version}`,
                            }
                        );


                        /**
                         * @return undefined
                         */
                        function revert() {
                            Colby.callAjaxFunction(
                                "CBModels",
                                "revert",
                                {
                                    ID: modelData.modelID,
                                    version: version.version,
                                }
                            ).then(
                                function () {
                                    location.reload(true);
                                }
                            ).catch(
                                function (error) {
                                    Colby.displayAndReportError(error);
                                }
                            );
                        }
                    };

                    sectionItem.appendPart(stringsPart);
                    sectionItem.appendPart(CBUINavigationArrowPart.create());
                    section.appendChild(sectionItem.element);
                });

                args.container.appendChild(section);
            }

            args.container.appendChild(CBUI.createHalfSpace());

            if (modelData.rowFromColbyPages) {
                args.container.appendChild(
                    CBUIExpander.create(
                        {
                            message: (
                                "ColbyPages Row\n\n--- pre\n" +
                                CBMessageMarkup.stringToMarkup(
                                    JSON.stringify(
                                        modelData.rowFromColbyPages,
                                        undefined,
                                        2
                                    )
                                ) +
                                "\n---"
                            ),
                        }
                    ).element
                );
            }

            if (modelData.rowFromCBImages) {
                args.container.appendChild(
                    CBUIExpander.create(
                        {
                            message: (
                                "CBImages Row\n\n--- pre\n" +
                                CBMessageMarkup.stringToMarkup(
                                    JSON.stringify(
                                        modelData.rowFromCBImages,
                                        undefined,
                                        2
                                    )
                                ) +
                                "\n---"
                            ),
                        }
                    ).element
                );
            }

            if (modelData.dataStoreFiles.length > 0) {
                let links = modelData.dataStoreFiles.map(
                    function (file) {
                        let text = CBMessageMarkup.stringToMarkup(file.text);
                        let URL = CBMessageMarkup.stringToMarkup(file.URL);

                        return `(${text} (a ${URL}))`;
                    }
                );

                args.container.appendChild(
                    CBUIExpander.create(
                        {
                            message: (
                                "Data Store Files\n\n--- ul\n" +
                                links.join("\n\n") +
                                "\n---"
                            ),
                        }
                    ).element
                );
            }

            if (modelData.archive.length > 0) {
                args.container.appendChild(
                    CBUIExpander.create(
                        {
                            message: (
                                "Archive\n\n--- pre\n" +
                                CBMessageMarkup.stringToMarkup(
                                    modelData.archive
                                ) +
                                "\n---"
                            ),
                        }
                    ).element
                );
            }

            Colby.updateTimes();
        }
        /* IDDidChange_render() */
    },
    /* IDDidChange() */
};


Colby.afterDOMContentLoaded(
    CBModelInspector.init
);
