"use strict";
/* jshint strict: global */
/* global
    CBMessageMarkup,
    CBModelInspector_modelID,
    CBUI,
    CBUIExpander,
    CBUIStringEditor,
    Colby */

var CBModelInspector = {

    /**
     * @return undefined
     */
    init: function () {
        var section, item;
        var spec = {
            ID: CBModelInspector_modelID,
        };
        var main = document.getElementsByTagName("main")[0];
        var container = document.createElement("div");
        var IDDidChangeCallback = CBModelInspector.IDDidChange.bind(undefined, {
            spec: spec,
            container: container,
        });

        main.appendChild(CBUI.createHalfSpace());

        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        item.appendChild(CBUIStringEditor.createEditor({
                labelText: "Model ID",
                propertyName: "ID",
                spec: spec,
                specChangedCallback: IDDidChangeCallback,
        }).element);
        section.appendChild(item);

        main.appendChild(section);

        main.appendChild(CBUI.createHalfSpace());

        main.appendChild(container);

        main.appendChild(CBUI.createHalfSpace());

        IDDidChangeCallback();
    },

    /**
     * @param hex160? args.spec.ID
     * @param Element args.container
     *
     * @return undefined
     */
    IDDidChange: function (args) {
        if (/^[0-9a-f]{40}$/.test(args.spec.ID)) {
            Colby.callAjaxFunction("CBModelInspector", "fetchModelData", {ID: args.spec.ID})
                 .then(resolved)
                 .catch(Colby.displayAndReportError);
        } else {
            document.title = "Inspector: Invalid ID";
        }

        function resolved(response) {
            var section;

            args.container.textContent = undefined;

            section = CBUI.createSection();

            if (response.modelVersions.length === 0) {
                document.title = "Inspector: " + args.spec.ID;
                section.appendChild(CBUI.createKeyValueSectionItem({
                    key: "Notice",
                    value: "This ID has no model."
                }).element);
            } else {
                var model = JSON.parse(response.modelVersions[0].modelAsJSON);

                document.title = "Inspector: " + (model.title ? model.title.trim() : model.className);

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
            }

            args.container.appendChild(section);

            args.container.appendChild(CBUI.createHalfSpace());

            section = CBUI.createSection();

            response.modelVersions.forEach(function (version) {
                var item = CBUI.createSectionItem2();

                var time = document.createElement("time");
                time.className = "time";
                time.dataset.timestamp = version.timestamp * 1000;
                item.titleElement.appendChild(time);

                var specCommand = document.createElement("div");
                specCommand.className = "command";
                specCommand.textContent = "Spec";
                specCommand.addEventListener("click", showSpec);

                item.commandsElement.appendChild(specCommand);

                var modelCommand = document.createElement("div");
                modelCommand.className = "command";
                modelCommand.textContent = "Model";
                modelCommand.addEventListener("click", showModel);

                item.commandsElement.appendChild(modelCommand);

                var revertCommand = document.createElement("div");
                revertCommand.className = "command";
                revertCommand.textContent = "Revert";
                revertCommand.addEventListener("click", revert);

                item.commandsElement.appendChild(revertCommand);

                section.appendChild(item.element);

                function showModel() {
                    var pre = document.createElement("div");
                    pre.textContent = JSON.stringify(JSON.parse(version.modelAsJSON), undefined, 2);
                    pre.style.whiteSpace = "pre-wrap";

                    Colby.setPanelElement(pre);
                    Colby.showPanel();
                }

                function showSpec() {
                    var pre = document.createElement("div");
                    pre.textContent = JSON.stringify(JSON.parse(version.specAsJSON), undefined, 2);
                    pre.style.whiteSpace = "pre-wrap";

                    Colby.setPanelElement(pre);
                    Colby.showPanel();
                }

                function revert() {
                    var data = new FormData();
                    data.append("ID", args.spec.ID);
                    data.append("version", version.version);

                    Colby.fetchAjaxResponse("/api/?class=CBModels&function=revert", data)
                         .then(resolved)
                         .catch(Colby.report);

                    function resolved(response) {
                        location.reload(true);
                    }
                }
            });

            args.container.appendChild(section);

            if (response.rowFromColbyPages) {
                args.container.appendChild(CBUI.createHalfSpace());
                args.container.appendChild(CBUIExpander.create({
                    message: "ColbyPages Row\n\n--- pre\n" +
                             CBMessageMarkup.stringToMarkup(JSON.stringify(response.rowFromColbyPages, undefined, 2)) +
                             "\n---",
                }).element);
            }

            if (response.rowFromCBImages) {
                args.container.appendChild(CBUI.createHalfSpace());
                args.container.appendChild(CBUIExpander.create({
                    message: "CBImages Row\n\n--- pre\n" +
                             CBMessageMarkup.stringToMarkup(JSON.stringify(response.rowFromCBImages, undefined, 2)) +
                             "\n---",
                }).element);
            }

            Colby.updateTimes();
        }
    },
};

Colby.afterDOMContentLoaded(CBModelInspector.init);
