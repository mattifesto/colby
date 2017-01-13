"use strict"; /* jshint strict: global */
/* global
    CBUI,
    CBUIBooleanEditor,
    Colby */

var CBGroupUserSettings = {

    /**
     * @param object spec
     * @param bool newValue
     *
     * @return bool
     */
    valueShouldChange: function (spec, newValue) {
        if (newValue === false) {
            return confirm("Are you sure you want to remove this user from the " + spec.groupName + " group?");
        }

        return true;
    },

    /**
     * @param Element args.element
     * @param object args.spec
     *
     * @return null
     */
    display: function (args) {
        var section, item;

        section = CBUI.createSection();
        item = CBUI.createSectionItem();
        item.appendChild(CBUIBooleanEditor.create({
            labelText: args.spec.groupName,
            spec: args.spec,
            propertyName: "isMember",
            specChangedCallback: CBGroupUserSettings.save.bind(undefined, {
                spec: args.spec,
                // TODO: disable UI function
            }),
            valueShouldChangeCallback: CBGroupUserSettings.valueShouldChange.bind(undefined, args.spec),
        }).element);
        section.appendChild(item);

        args.element.appendChild(section);
        args.element.appendChild(CBUI.createHalfSpace());
    },

    /**
     * @param int userID
     * @param string groupName
     * @param Element element
     *
     * @return Promise
     */
    fetchSpec: function (userID, groupName, element) {
        var formData = new FormData();
        formData.append("userID", userID);
        formData.append("groupName", groupName);

        return Colby.fetchAjaxResponse("/api/?class=CBGroupUserSettings&function=fetchSpec", formData)
            .then(display, Colby.displayError);

        function display(ajaxResponse) {
            if (ajaxResponse.spec) {
                CBGroupUserSettings.display({
                    element: element,
                    spec: ajaxResponse.spec,
                });
            }
        }
    },

    /**
     * @param object args.spec
     *
     * @return Promise
     */
    save: function (args) {
        var formData = new FormData();
        formData.append("specAsJSON", JSON.stringify(args.spec));

        // TODO: disable UI while saving

        return Colby.fetchAjaxResponse("/api/?class=CBGroupUserSettings&function=updateGroupMembership", formData)
            .then(undefined, Colby.displayError);
    },

    /**
     * @return undefined
     */
    DOMContentDidLoad: function () {
        var userID, groupName, element;
        var elements = document.getElementsByClassName("CBGroupUserSettings");

        for (var i = 0; i < elements.length; i++) {
            element = elements.item(i);
            userID = element.dataset.userid;
            groupName = element.dataset.groupname;

            CBGroupUserSettings.fetchSpec(userID, groupName, element);
        }
    },
};

document.addEventListener("DOMContentLoaded", CBGroupUserSettings.DOMContentDidLoad);
