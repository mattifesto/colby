"use strict";
/* jshint strict: global */
/* jshint esversion: 8 */
/* global
    CBUI,
    CBUIPanel,
    CBUIStringEditor,
    Colby,
*/



(function () {

    Colby.afterDOMContentLoaded(afterDOMContentLoaded);



    /**
     * @return undefined
     */
    async function afterDOMContentLoaded() {
        let viewElements = document.getElementsByClassName("CBSetupView");
        let viewElement = viewElements.item(0);

        await setupDatabaseUser(
            viewElement
        );

        window.alert("done");
    }
    /* afterDOMContentLoaded() */



    /**
     * @return Promise -> ???
     */
    function setupDatabaseUser(
        viewElement
    ) {
        return new Promise(
            function (resolve) {
                setupDatabaseUser_createUserInterface(
                    viewElement,
                    resolve
                );
            }
        );
    }
    /* setupDatabaseUser() */



    /**
     *
     */
    function setupDatabaseUser_createUserInterface(
        viewElement,
        resolve
    ) {
        let elements = CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        viewElement.appendChild(elements[0]);

        let sectionElement = elements[1];

        let hostnameEditor = CBUIStringEditor.create();
        hostnameEditor.title = "MySQL Host";

        sectionElement.appendChild(
            hostnameEditor.element
        );

        let usernameEditor = CBUIStringEditor.create();
        usernameEditor.title = "MySQL Username";

        sectionElement.appendChild(
            usernameEditor.element
        );

        let passwordEditor = CBUIStringEditor.create(
            {
                inputType: "password",
            }
        );

        passwordEditor.title = "MySQL Password";

        sectionElement.appendChild(
            passwordEditor.element
        );

        let databaseEditor = CBUIStringEditor.create();
        databaseEditor.title = "MySQL Database";

        sectionElement.appendChild(
            databaseEditor.element
        );

        elements = CBUI.createElementTree(
            "CBUI_container1",
            "CBUI_button1"
        );

        viewElement.appendChild(
            elements[0]
        );

        let buttonElement = elements[1];
        buttonElement.textContent = "Verify Database User";

        buttonElement.addEventListener(
            "click",
            closure_setupDatabaseUser_verifyDatabaseUserViaAjax
        );



        /**
         * return undefined
         */
        function closure_setupDatabaseUser_verifyDatabaseUserViaAjax() {
            setupDatabaseUser_verifyDatabaseUserViaAjax(
                hostnameEditor.value,
                usernameEditor.value,
                passwordEditor.value,
                resolve
            );
        }
        /* closure_setupDatabaseUser_verifyDatabaseUserViaAjax() */

    }
    /* setupDatabaseUser_createUserInterface() */



    async function setupDatabaseUser_verifyDatabaseUserViaAjax(
        hostname,
        username,
        password,
        resolve
    ) {
        try {
            let response = await Colby.callAjaxFunction(
                "CBSetup",
                "verifyDatabaseUser",
                {
                    hostname,
                    username,
                    password,
                }
            );

            if (response.succeeded === true) {
                resolve();
            } else {
                CBUIPanel.displayCBMessage(
                    response.cbmessage
                );
            }
        } catch (error) {
            CBUIPanel.displayAndReportError(error);
        }
    }
    /* setupDatabaseUser_verifyDatabaseUserViaAjax() */

})();
