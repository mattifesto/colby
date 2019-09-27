"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBGroupUserSettings */
/* global
    CBErrorHandler,
    CBUI,
    CBUIBooleanEditor,
    Colby,
*/

var CBGroupUserSettings = {

    /**
     * @param object spec
     * @param bool newValue
     *
     * @return bool
     */
    valueShouldChange: function (spec, newValue) {
        if (newValue === false) {
            return window.confirm(
                "Are you sure you want to remove this user from the " +
                spec.groupName +
                " group?"
            );
        }

        return true;
    },
    /* valueShouldChange() */


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
            valueShouldChangeCallback: CBGroupUserSettings.valueShouldChange.bind(
                undefined,
                args.spec
            ),
        }).element);
        section.appendChild(item);

        args.element.appendChild(section);
        args.element.appendChild(CBUI.createHalfSpace());
    },


    /**
     * @param int userNumericID
     * @param string groupName
     * @param Element element
     *
     * @return Promise
     */
    fetchSpec: function (userNumericID, groupName, element) {
        let promise = Colby.callAjaxFunction(
            "CBGroupUserSettings",
            "fetchSpec",
            {
                userNumericID: userNumericID,
                groupName: groupName,
            }
        ).then(
            function (spec) {
                CBGroupUserSettings.display(
                    {
                        element: element,
                        spec: spec,
                    }
                );
            }
        ).catch(
            function (error) {
                CBErrorHandler.displayAndReport(error);
            }
        );

        return promise;
    },
    /* fetchSpec() */


    /**
     * @param object args.spec
     *
     * @return Promise
     */
    save: function (args) {
        // TODO: disable UI while saving

        return Colby.callAjaxFunction(
            "CBGroupUserSettings",
            "updateGroupMembership",
            args.spec
        ).catch(
            function (error) {
                CBErrorHandler.displayAndReport(error);
            }
        );
    },
    /* save() */


    /**
     * @return undefined
     */
    DOMContentDidLoad: function () {
        var userNumericID, groupName, element;
        var elements = document.getElementsByClassName("CBGroupUserSettings");

        for (var i = 0; i < elements.length; i++) {
            element = elements.item(i);
            userNumericID = element.dataset.userNumericId;
            groupName = element.dataset.groupName;

            CBGroupUserSettings.fetchSpec(userNumericID, groupName, element);
        }
    },
    /* DOMContentDidLoad() */
};
/* CBGroupUserSettings */


Colby.afterDOMContentLoaded(CBGroupUserSettings.DOMContentDidLoad);
