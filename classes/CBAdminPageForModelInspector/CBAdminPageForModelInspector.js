"use strict"; /* jshint strict: global */

/* global
    CBAdminPageForModelInspectorID,
    CBUI,
    CBUIStringEditor,
    Colby */

var CBAdminPageForModelInspector = {

    /**
     * @return undefined
     */
    DOMContentDidLoad: function () {
        var section, item;
        var spec = {
            ID: CBAdminPageForModelInspectorID,
        };
        var main = document.getElementsByTagName("main")[0];
        var container = document.createElement("div");
        var IDDidChangeCallback = CBAdminPageForModelInspector.IDDidChange.bind(undefined, {
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
            var data = new FormData();
            data.append("ID", args.spec.ID);

            Colby.fetchAjaxResponse("/api/?class=CBModels&function=fetchModelVersionsByID", data)
                 .then(resolved)
                 .catch(Colby.report)
                 .catch(Colby.displayError);
        }

        function resolved(response) {
            var section;
            var model = JSON.parse(response.versions[0].modelAsJSON);

            args.container.textContent = undefined;

            section = CBUI.createSection();

            section.appendChild(CBUI.createKeyValueSectionItem({
                key: "Class Name",
                value: model.className,
            }).element);
            args.container.appendChild(section);

            section.appendChild(CBUI.createKeyValueSectionItem({
                key: "Title",
                value: model.title,
            }).element);
            args.container.appendChild(section);

            section.appendChild(CBUI.createKeyValueSectionItem({
                key: "Description",
                value: model.description,
            }).element);
            args.container.appendChild(section);

            args.container.appendChild(CBUI.createHalfSpace());

            section = CBUI.createSection();

            response.versions.forEach(function (version) {
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

            Colby.updateTimes();
        }
    },
};

document.addEventListener("DOMContentLoaded", CBAdminPageForModelInspector.DOMContentDidLoad);
