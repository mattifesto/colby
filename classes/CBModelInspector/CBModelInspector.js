"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* global
    CBArtworkElement,
    CBErrorHandler,
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

    CBModelInspector,

    CBModelInspector_associatedImageModel,
    CBModelInspector_modelID,
*/



(function () {

    window.CBModelInspector = {

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

            navigationHomeElement.appendChild(
                CBUI.createHalfSpace()
            );

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

                navigationHomeElement.appendChild(
                    CBUI.createHalfSpace()
                );
            }

            navigationHomeElement.appendChild(modelInformationElement);

            navigationHomeElement.appendChild(
                CBUI.createHalfSpace()
            );

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
                    CBErrorHandler.displayAndReport(error);
                }
            );

            return;



            /* -- closures -- -- -- -- -- */



            /**
             * @return Element
             */
            function IDDidChange_createAssociatedWithElement() {
                let element = CBUI.createElement(
                    "CBModelInspector_associatedWith"
                );

                if (modelData.associatedWith.length === 0) {
                    return element;
                }

                let titleElement = CBUI.createElement(
                    "CBUI_title1"
                );

                titleElement.textContent = "Associated With";

                element.appendChild(titleElement);

                let sectionContainerElement = CBUI.createElement(
                    "CBUI_sectionContainer"
                );

                element.appendChild(sectionContainerElement);

                let sectionElement = CBUI.createElement(
                    "CBUI_section"
                );

                sectionContainerElement.appendChild(sectionElement);

                modelData.associatedWith.forEach(
                    function renderAssociation(association) {
                        let associationElement = CBUI.createElement(
                            "CBUI_container_leftAndRight"
                        );

                        sectionElement.appendChild(associationElement);

                        /* associated with ID */

                        let associatedWithIDElement = CBUI.createElement(
                            "CBUI_textSize_small CBUI_fontFamily_monospace",
                            "a"
                        );

                        let associatedWithID = CBModel.valueAsID(
                            association,
                            "ID"
                        );

                        associatedWithIDElement.href = (
                            "/admin/?c=CBModelInspector&ID=" +
                            associatedWithID
                        );

                        associatedWithIDElement.textContent = associatedWithID;

                        associationElement.appendChild(associatedWithIDElement);

                        /* association key */

                        let associationKeyElement = CBUI.createElement();

                        associationKeyElement.textContent = CBModel.valueToString(
                            association,
                            "className"
                        );

                        associationElement.appendChild(associationKeyElement);
                    }
                );

                return element;
            }
            /* IDDidChange_createAssociatedWithElement() */



            /**
             * @return Element
             */
            function IDDidChange_createAssociationsElement() {
                let element = CBUI.createElement(
                    "CBModelInspector_associations"
                );

                if (modelData.associations.length === 0) {
                    return element;
                }

                let titleElement = CBUI.createElement(
                    "CBUI_title1"
                );

                titleElement.textContent = "Associations";

                element.appendChild(titleElement);

                let sectionContainerElement = CBUI.createElement(
                    "CBUI_sectionContainer"
                );

                element.appendChild(sectionContainerElement);

                let sectionElement = CBUI.createElement(
                    "CBUI_section"
                );

                sectionContainerElement.appendChild(sectionElement);

                modelData.associations.forEach(
                    function renderAssociation(association) {
                        let associationElement = CBUI.createElement(
                            "CBUI_container_leftAndRight"
                        );

                        sectionElement.appendChild(associationElement);

                        let keyElement = CBUI.createElement();

                        keyElement.textContent = CBModel.valueToString(
                            association,
                            "className"
                        );

                        associationElement.appendChild(keyElement);

                        let IDElement = CBUI.createElement(
                            "CBUI_textSize_small CBUI_fontFamily_monospace",
                            "a"
                        );

                        let associatedID = CBModel.valueAsID(
                            association,
                            "associatedID"
                        );

                        IDElement.href = (
                            "/admin/?c=CBModelInspector&ID=" +
                            associatedID
                        );

                        IDElement.textContent = associatedID;

                        associationElement.appendChild(IDElement);
                    }
                );

                return element;
            }
            /* IDDidChange_createAssociationsElement() */



            /**
             * @return undefined
             */
            function IDDidChange_render() {
                let section;

                containerElement.textContent = "";

                section = CBUI.createSection();
                containerElement.appendChild(section);

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

                    return;
                }

                document.title = (
                    "Inspector: " +
                    (
                        model.title ?
                        model.title.trim() :
                        model.className
                    )
                );

                section.appendChild(
                    CBUI.createKeyValueSectionItem(
                        {
                            key: "Class Name",
                            value: model.className,
                        }
                    ).element
                );

                section.appendChild(
                    CBUI.createKeyValueSectionItem(
                        {
                            key: "Title",
                            value: model.title,
                        }
                    ).element
                );

                section.appendChild(
                    CBUI.createKeyValueSectionItem(
                        {
                            key: "Description",
                            value: model.description,
                        }
                    ).element
                );

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
                        CBUIPanel.confirmText(
                            "Are you sure you want to delete this model?"
                        ).then(
                            function (wasConfirmed) {
                                if (wasConfirmed) {
                                    deleteModel();
                                }
                            }
                        ).catch(
                            function (error) {
                                CBErrorHandler.displayAndReport(error);
                            }
                        );
                    };

                    /* stage 2 */
                    let deleteModel = function () {
                        let controller = CBUIPanel.displayBusyText(
                            "Deleting model..."
                        );

                        Colby.callAjaxFunction(
                            "CBModels",
                            "deleteByID",
                            {
                                ID: model.ID,
                            }
                        ).then(
                            function () {
                                return new Promise(
                                    function (resolve) {
                                        window.setTimeout(
                                            resolve,
                                            3000
                                        );
                                    }
                                );
                            }
                        ).then(
                            function () {
                                report();
                            }
                        ).catch(
                            function (error) {
                                CBErrorHandler.displayAndReport(error);
                            }
                        ).finally(
                            function () {
                                controller.hide();
                            }
                        );
                    };

                    /* stage 3 */
                    let report = function () {
                        CBUIPanel.displayText(
                            "The model has been deleted. Press OK to " +
                            "navigate to the models admin page."
                        ).then(
                            function () {
                                window.location = "/admin/?c=CBModelsAdmin";
                            }
                        ).catch(
                            function (error) {
                                CBErrorHandler.displayAndReport(error);
                            }
                        );
                    };
                }

                containerElement.appendChild(
                    CBUI.createHalfSpace()
                );


                /* CBImage view */

                if (model.className === "CBImage") {
                    containerElement.appendChild(
                        createCBImageViewElement(model)
                    );
                }


                /* associated image */

                if (
                    model.className !== "CBImage" ||
                    CBModelInspector_associatedImageModel !== null
                ){
                    containerElement.appendChild(
                        createAssociatedImageEditorElement(model)
                    );
                }


                /* associations */

                containerElement.appendChild(
                    IDDidChange_createAssociationsElement()
                );


                /* associated with */

                containerElement.appendChild(
                    IDDidChange_createAssociatedWithElement()
                );


                /* versions */

                if (modelData.modelVersions.length > 0) {
                    {
                        let titleElement = document.createElement("div");
                        titleElement.className = "CBUI_title1";
                        titleElement.textContent = "Versions";
                        containerElement.appendChild(titleElement);
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

                            element.appendChild(
                                CBUI.createHalfSpace()
                            );

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

                                element.appendChild(
                                    CBUI.createHalfSpace()
                                );
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

                                element.appendChild(
                                    CBUI.createHalfSpace()
                                );
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
                                        CBErrorHandler.displayAndReport(error);
                                    }
                                );
                            }
                        };

                        sectionItem.appendPart(stringsPart);

                        sectionItem.appendPart(
                            CBUINavigationArrowPart.create()
                        );

                        section.appendChild(sectionItem.element);
                    });

                    containerElement.appendChild(section);
                }

                containerElement.appendChild(
                    CBUI.createHalfSpace()
                );

                if (modelData.rowFromColbyPages) {
                    containerElement.appendChild(
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
                    containerElement.appendChild(
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

                    containerElement.appendChild(
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
                    containerElement.appendChild(
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
    /* window.CBModelInspector */



    /* -- closures -- -- -- -- -- */



    /**
     * @return Element
     */
    function createAssociatedImageEditorElement() {
        let editorElement = CBUI.createElement(
            "CBModelInspector_associatedImageEditor"
        );

        let titleElement = CBUI.createElement(
            "CBUI_title1"
        );

        editorElement.appendChild(titleElement);

        titleElement.textContent = "Associated Image";

        let sectionContainerElement = CBUI.createElement(
            "CBUI_sectionContainer"
        );

        editorElement.appendChild(sectionContainerElement);

        let sectionElement = CBUI.createElement(
            "CBUI_section"
        );

        sectionContainerElement.appendChild(sectionElement);

        let chooser = CBUIImageChooser.create();

        sectionElement.appendChild(
            chooser.element
        );

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
                    CBErrorHandler.displayAndReport(error);
                }
            );
        };

        return editorElement;
    }
    /* createAssociatedImageEditorElement() */



    /**
     * This function renders the image for a CBImage model.
     *
     * @param object model
     *
     * @return Element
     */
    function createCBImageViewElement(model) {
        let viewElement = CBUI.createElement(
            "CBModelInspector_CBImageView"
        );

        let titleElement = CBUI.createElement(
            "CBUI_title1"
        );

        viewElement.appendChild(titleElement);

        titleElement.textContent = "Image";

        let imageContainerElement = CBUI.createElement(
            "CBUI_container1 CBDarkTheme"
        );

        viewElement.appendChild(imageContainerElement);

        let artworkElement = CBArtworkElement.create(
            {
                URL: CBImage.toURL(model, "rw1600"),
                aspectRatioWidth: model.width,
                aspectRatioHeight: model.height,
                maxHeight: 400,
                maxWidth: 800,
            }
        );

        imageContainerElement.appendChild(artworkElement);

        return viewElement;
    }
    /* createCBImagePanelElement() */

})();



/**
 * @TODO 2019_11_05
 *
 *      Eventually, this can be moved inside the closure and the model
 *      instpector can have no publicly available API.
 */
Colby.afterDOMContentLoaded(
    function () {
        CBModelInspector.init();
    }
);
