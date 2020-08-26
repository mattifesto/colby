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
        let sectionElement;


        /* Website section */

        {
            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            sectionElement = elements[1];

            viewElement.appendChild(
                elements[0]
            );
        }


        /* Website Domain */

        let websiteHostnameEditor = CBUIStringEditor.create();
        websiteHostnameEditor.title = "Website Hostname";
        websiteHostnameEditor.value = CBSetupView_suggestedWebsiteHostname;

        sectionElement.appendChild(
          websiteHostnameEditor.element
        );


        /* MySQL section */

        {
            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            sectionElement = elements[1];

            viewElement.appendChild(
                elements[0]
            );
        }


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


        /* Developer username and password section */

        {
            let elements = CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            sectionElement = elements[1];

            viewElement.appendChild(
                elements[0]
            );
        }


        /* Developer email address */

        let developerEmailAddressEditor = CBUIStringEditor.create();
        developerEmailAddressEditor.title = "Developer Email Address";

        sectionElement.appendChild(
            developerEmailAddressEditor.element
        );


        /* Developer password 1 */

        let developerPassword1Editor = CBUIStringEditor.create(
            {
                inputType: "password",
            }
        );

        developerPassword1Editor.title = "Developer Password";

        sectionElement.appendChild(
            developerPassword1Editor.element
        );


        /* Developer password 2 */

        let developerPassword2Editor = CBUIStringEditor.create(
            {
                inputType: "password",
            }
        );

        developerPassword2Editor.title = "Confirm Developer Password";

        sectionElement.appendChild(
            developerPassword2Editor.element
        );


        /* Verify Button */

        let buttonElement;

        {
            let elements = CBUI.createElementTree(
                "CBUI_container1",
                "CBUI_button1"
            );

            viewElement.appendChild(
                elements[0]
            );

            buttonElement = elements[1];
        }

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
                        developerEmailAddressEditor.value,
                        developerPassword1Editor.value,
                        developerPassword2Editor.value,
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
     * @param string developerEmailAddress
     * @param string developerPassword1
     * @param string developerPassword2
     *
     * @return Promise -> bool
     */
    async function setupDatabaseUser_verifyDatabaseUserViaAjax(
        developerEmailAddress,
        developerPassword1,
        developerPassword2,
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
                    developerEmailAddress,
                    developerPassword1,
                    developerPassword2,
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
