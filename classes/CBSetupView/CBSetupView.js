"use strict";
/* jshint strict: global */
/* jshint esversion: 8 */
/* global
    CBAjax,
    CBUI,
    CBUIPanel,
    CBUIStringEditor,
    Colby,

    CBSetupView_suggestedWebsiteHostname,
*/


(function () {

    Colby.afterDOMContentLoaded(
      afterDOMContentLoaded
    );



    /**
     * @return undefined
     */
    async function afterDOMContentLoaded() {
        let viewElements = document.getElementsByClassName("CBSetupView");
        let viewElement = viewElements.item(0);

        await setupDatabaseUser(
            viewElement
        );

        location.href = "/admin/";
    }
    /* afterDOMContentLoaded() */



    /**
     * @return Promise -> undefined
     */
    async function setupDatabaseUser(
        viewElement
    ) {
        await setupDatabaseUser_createUserInterface(
            viewElement
        );
    }
    /* setupDatabaseUser() */



    /**
     * @return Promise -> undefined
     */
    async function setupDatabaseUser_createUserInterface(
        viewElement,
    ) {
        let elements = CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        viewElement.appendChild(elements[0]);

        let sectionElement = elements[1];


        /* Website Domain */

        let websiteHostnameEditor = CBUIStringEditor.create();
        websiteHostnameEditor.title = "Website Hostname";
        websiteHostnameEditor.value = CBSetupView_suggestedWebsiteHostname;

        sectionElement.appendChild(
          websiteHostnameEditor.element
        );


        /* MySQL Binary Directory */

        let mysqlBinaryDirectoryEditor = CBUIStringEditor.create();
        mysqlBinaryDirectoryEditor.title = "MySQL Binary Directory (Optional)";

        sectionElement.appendChild(
          mysqlBinaryDirectoryEditor.element
        );


        /* MySQL Hostname */

        let mysqlHostnameEditor = CBUIStringEditor.create();
        mysqlHostnameEditor.title = "MySQL Host";

        sectionElement.appendChild(
            mysqlHostnameEditor.element
        );


        /* MySQL Username */

        let mysqlUsernameEditor = CBUIStringEditor.create();
        mysqlUsernameEditor.title = "MySQL Username";

        sectionElement.appendChild(
            mysqlUsernameEditor.element
        );


        /* MySQL Password */

        let mysqlPasswordEditor = CBUIStringEditor.create(
            {
                inputType: "password",
            }
        );

        mysqlPasswordEditor.title = "MySQL Password";

        sectionElement.appendChild(
            mysqlPasswordEditor.element
        );


        /* MySQL Database Name */

        let mysqlDatabaseNameEditor = CBUIStringEditor.create();
        mysqlDatabaseNameEditor.title = "MySQL Database";

        sectionElement.appendChild(
            mysqlDatabaseNameEditor.element
        );


        /* Verify Button */

        elements = CBUI.createElementTree(
            "CBUI_container1",
            "CBUI_button1"
        );

        viewElement.appendChild(
            elements[0]
        );

        let buttonElement = elements[1];
        buttonElement.textContent = "Verify Database User";

        let resolve;

        let promise = new Promise(
            function (resolveCallback) {
                resolve = resolveCallback;
            }
        );

        buttonElement.addEventListener(
            "click",
            async function () {
                let succeeded = (
                    await setupDatabaseUser_verifyDatabaseUserViaAjax(
                        websiteHostnameEditor.value,
                        mysqlBinaryDirectoryEditor.value,
                        mysqlHostnameEditor.value,
                        mysqlUsernameEditor.value,
                        mysqlPasswordEditor.value,
                        mysqlDatabaseNameEditor.value
                    )
                );

                if (succeeded) {
                    resolve();
                }
            }
        );

        return promise;
    }
    /* setupDatabaseUser_createUserInterface() */



    /**
     * @param string hostname
     * @param string username
     * @param string password
     *
     * @return Promise -> bool
     */
    async function setupDatabaseUser_verifyDatabaseUserViaAjax(
        websiteHostname,
        mysqlBinaryDirectory,
        mysqlHostname,
        mysqlUsername,
        mysqlPassword,
        mysqlDatabaseName
    ) {
        try {
            let response = await CBAjax.call(
                "CBSetup",
                "verifyDatabaseUser",
                {
                    websiteHostname,
                    mysqlBinaryDirectory,
                    mysqlHostname,
                    mysqlUsername,
                    mysqlPassword,
                    mysqlDatabaseName,
                }
            );

            if (response.succeeded === true) {
                await CBUIPanel.displayCBMessage(
                    'thing approved'
                );

                return true;
            } else {
                await CBUIPanel.displayCBMessage(
                    response.cbmessage
                );

                return false;
            }
        } catch (error) {
            await CBUIPanel.displayAndReportError(
                error
            );

            return false;
        }
    }
    /* setupDatabaseUser_verifyDatabaseUserViaAjax() */

})();
