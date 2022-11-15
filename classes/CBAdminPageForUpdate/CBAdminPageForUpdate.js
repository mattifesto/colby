/* globals
    CBAjax,
    CBDevelopersUserGroup,
    CBMaintenance,
    CBMessageMarkup,
    CBUI,
    CBUIExpander,
    CBUINavigationView,
    CBUIPanel,
    Colby,

    CBAdminPageForUpdate_arrayOfSubmodulePaths_jsvariable,
    CBAdminPageForUpdate_isDevelopmentWebsite,
*/


(function()
{
    "use strict";



    let taskIsRunning =
    false;

    let shared_outputElement;

    Colby.afterDOMContentLoaded(
        function ()
        {
            afterDOMContentLoaded();
        }
    );



    /**
     * @return undefined
     */
    function
    afterDOMContentLoaded(
    ) // -> undefined
    {
        let mainElement =
        document.getElementsByTagName(
            "main"
        )[0];

        mainElement.appendChild(
            CBUINavigationView.create().element
        );

        let navigationPaneElement =
        document.createElement(
            "div"
        );

        CBUINavigationView.navigate(
            {
                element:
                navigationPaneElement,

                title:
                "Developer Tools",
            }
        );

        if (
            CBDevelopersUserGroup.currentUserIsMember()
        ) {
            navigationPaneElement.appendChild(
                createDeveloperUpdateButtonElement()
            );
        }

        navigationPaneElement.appendChild(
            createFullUpdateButtonElement()
        );

        navigationPaneElement.appendChild(
            createIndividualActionsSectionElement()
        );

        navigationPaneElement.append(
            CBAdminPageForUpdate_createSubmodulesElement()
        );



        /* output element */

        shared_outputElement =
        document.createElement(
            "div"
        );

        shared_outputElement.className =
        "output";

        navigationPaneElement.appendChild(
            shared_outputElement
        );

        return;
    }
    /* afterDOMContentLoaded() */



    /**
     * @param string submodulePath
     *
     * @return undefined
     */
    function
    CBAdminPageForUpdate_createSubmoduleAnchorElement(
        parentElementArgument,
        submodulePathArgument
    ) // -> Element
    {
        let anchorElement =
        document.createElement(
            "a"
        );

        anchorElement.textContent =
        submodulePathArgument;

        parentElementArgument.append(
            anchorElement
        );

        anchorElement.addEventListener(
            "click",
            function ()
            {
                closure_handleClick();
            }
        );



        /**
         * @return undefined
         */
        function
        closure_handleClick(
        ) // -> undefined
        {
            CBAdminPageForUpdate_runTask(
                submodulePathArgument,
                function ()
                {
                    CBAdminPageForUpdate_performSubmoduleTask(
                        submodulePathArgument
                    );
                }
            );
        }
        // closure_handleClick()

    }
    // CBAdminPageForUpdate_createSubmoduleAnchorElement()



    /**
     * @return Element
     */
    function
    CBAdminPageForUpdate_createSubmodulesElement(
    ) // --> Element
    {
        let rootElement =
        document.createElement(
            "div"
        );

        rootElement.className =
        "CBAdminPageForUpdate_submodules_root_element";



        CBAdminPageForUpdate_arrayOfSubmodulePaths_jsvariable.forEach(
            function (submodulePath)
            {
                CBAdminPageForUpdate_createSubmoduleAnchorElement(
                    rootElement,
                    submodulePath
                );
            }
        );



        return rootElement;
    }
    // CBAdminPageForUpdate_createSubmodulesElement()



    /**
     * This function creates a single button that is used to fully update
     * non-development websites. It will not be shown on development websites.
     *
     * @return Element
     */
    function
    createFullUpdateButtonElement(
    ) // -> Element
    {
        let elements =
        CBUI.createElementTree(
            "CBUI_container1",
            "CBUI_button1"
        );

        let buttonElement =
        elements[1];

        buttonElement.textContent =
        "Backup, Pull Website, and Update";

        buttonElement.addEventListener(
            "click",
            function ()
            {
                CBAdminPageForUpdate_runTask(
                    "Backup, Pull Website, and Update",
                    function ()
                    {
                        return Promise.resolve().then(
                            function () {
                                return promiseToBackupDatabase();
                            }
                        ).then (
                            function () {
                                return CBAdminPageForUpdate_pull(
                                    "website"
                                );
                            }
                        ).then(
                            function () {
                                return promiseToUpdateSite();
                            }
                        );
                    }
                );
            }
        );

        return elements[0];
    }
    /* createFullUpdateButtonElement() */



    /**
     * This function creates a section containing individual actions for backup,
     * pull colby, and update website.
     */
    function
    createIndividualActionsSectionElement(
    ) {
        let elements =
        CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let sectionElement =
        elements[1];


        /* backup only */
        {
            let actionElement =
            CBUI.createElement(
                "CBUI_action"
            );

            sectionElement.appendChild(
                actionElement
            );

            actionElement.textContent =
            "Backup Database";

            actionElement.addEventListener(
                "click",
                function ()
                {
                    CBAdminPageForUpdate_runTask(
                        "Backup Database",
                        function ()
                        {
                            return promiseToBackupDatabase();
                        }
                    );
                }
            );
        }
        /* backup only */


        /* pull colby or pull website */
        if (
            CBAdminPageForUpdate_isDevelopmentWebsite
        ) {
            let actionElement =
            CBUI.createElement(
                "CBUI_action"
            );

            sectionElement.appendChild(
                actionElement
            );

            actionElement.textContent =
            "Pull Colby";

            actionElement.addEventListener(
                "click",
                function ()
                {
                    CBAdminPageForUpdate_runTask(
                        "Pull Colby",
                        function ()
                        {
                            return CBAdminPageForUpdate_pull(
                                "colby"
                            );
                        }
                    );
                }
            );
        }

        else
        {
            let actionElement =
            CBUI.createElement(
                "CBUI_action"
            );

            sectionElement.appendChild(
                actionElement
            );

            actionElement.textContent =
            "Pull Website";

            actionElement.addEventListener(
                "click",
                function ()
                {
                    CBAdminPageForUpdate_runTask(
                        "Pull Website",
                        function ()
                        {
                            return CBAdminPageForUpdate_pull(
                                "website"
                            );
                        }
                    );
                }
            );
        }
        /* pull colby or pull website */


        /* update only */
        {
            let actionElement =
            CBUI.createElement(
                "CBUI_action"
            );

            sectionElement.appendChild(
                actionElement
            );

            actionElement.textContent =
            "Update Site";

            actionElement.addEventListener(
                "click",
                function ()
                {
                    CBAdminPageForUpdate_runTask(
                        "Update Site",
                        function ()
                        {
                            return promiseToUpdateSite();
                        }
                    );
                }
            );
        }
        /* update only */


        return elements[0];
    }
    /* createIndividualActionsSectionElement() */



    /**
     * @return Element
     */
    function
    createDeveloperUpdateButtonElement(
    ) // -> Element
    {
        let sectionContainerElement =
        CBUI.createElement(
            "CBUI_sectionContainer"
        );

        let sectionElement =
        CBUI.createElement(
            "CBUI_section"
        );

        sectionContainerElement.appendChild(
            sectionElement
        );

        let actionElement =
        CBUI.createElement(
            "CBUI_action"
        );

        sectionElement.appendChild(
            actionElement
        );

        actionElement.textContent =
        "Backup, Pull Colby, and Update";

        actionElement.addEventListener(
            "click",
            function ()
            {
                CBAdminPageForUpdate_runTask(
                    "Backup and Update Colby",
                    function ()
                    {
                        return Promise.resolve().then(
                            function () {
                                return promiseToBackupDatabase();
                            }
                        ).then(
                            function () {
                                return CBAdminPageForUpdate_pull(
                                    "colby"
                                );
                            }
                        ).then(
                            function () {
                                return promiseToUpdateSite();
                            }
                        );
                    }
                );
            }
        );

        return sectionContainerElement;
    }
    /* createDeveloperUpdateButtonElement() */



    /**
     * @param string submodulePathArgument
     *
     * @return undefined
     */
    async function
    CBAdminPageForUpdate_performSubmoduleTask(
        submodulePathArgument
    ) // -> Promise -> undefined
    {
        shared_outputElement.textContent =
        "";

        let expander =
        CBUIExpander.create();

        shared_outputElement.append(
            expander.element
        );

        expander.expanded =
        true;

        expander.message =
        "foo";

        expander.title =
        submodulePathArgument;
    }
    // CBAdminPageForUpdate_performSubmoduleTask()



    /**
     * This function fully handles an attempt to run a task.
     *
     * @param string title
     * @param function callback
     *
     * @return Promise -> undefined
     */
    async function
    CBAdminPageForUpdate_runTask(
        title,
        callback
    ) // -> Promise -> undefined
    {
        try
        {
            if (
                taskIsRunning
            ) {
                CBUIPanel.displayText2(
                    "A task is already running."
                );

                return;
            }

            taskIsRunning =
            true;

            shared_outputElement.textContent =
            "";

            await
            CBMaintenance.transaction(
                title,
                callback
            );

            taskIsRunning =
            false;
        }

        catch (
            error
        ) {
            CBUIPanel.displayAndReportError(
                error
            );

            taskIsRunning =
            false;
        }
    }
    /* CBAdminPageForUpdate_runTask() */



    /**
     * @return Promise
     */
    function
    promiseToBackupDatabase(
    ) // -> Promise -> undefined
    {
        let expander =
        CBUIExpander.create();

        expander.title =
        "database backup in progress";

        expander.timestamp =
        Date.now() / 1000;

        shared_outputElement.appendChild(
            expander.element
        );

        let promise =
        CBAjax.call(
            "CBAdminPageForUpdate",
            "backupDatabase"
        ).then(
            function ()
            {
                expander.title =
                "database backup completed";

                expander.timestamp =
                Date.now() / 1000;
            }
        );

        return promise;
    }
    /* promiseToBackupDatabase() */



    /**
     * @param string targetArgument
     *
     * @return Promise -> undefined
     */
    async function
    CBAdminPageForUpdate_pull(
        targetArgument
    ) // -> Promise -> undefined
    {
        try
        {
            let ajaxFunctionName;

            switch (
                targetArgument
            ) {
                case "colby":

                ajaxFunctionName =
                "pullColby";

                break;



                case "website":

                ajaxFunctionName =
                "pull";

                break;


                default:

                throw Error(
                    "Unrecognized targetArgument"
                );
            }

            let expander =
            CBUIExpander.create();

            expander.title =
            `pull ${targetArgument} in progress`;

            expander.timestamp =
            Date.now() /
            1000;

            shared_outputElement.append(
                expander.element
            );

            let response =
            await
            CBAjax.call(
                "CBAdminPageForUpdate",
                ajaxFunctionName
            );

            let cbmessage =
            [
                "--- pre green",

                CBMessageMarkup.stringToMessage(
                    response.output
                ),

                "---",
            ].join(
                "\n"
            );

            expander.message =
            cbmessage;

            expander.timestamp =
            Date.now() /
            1000;

            if (
                response.succeeded
            ) {
                expander.title =
                `pull ${targetArgument} completed`;
            }

            else
            {
                expander.title =
                `pull ${targetArgument} failed`;

                expander.severity =
                3;
            }

            expander.expanded =
            true;
        }

        catch (
            error
        ) {
            CBUIPanel.displayAndReportError(
                error
            );
        }
    }
    // CBAdminPageForUpdate_pull()



    /**
     * @return Promise
     */
    function
    promiseToUpdateSite(
    ) // -> Promise -> undefined
    {
        let expander =
        CBUIExpander.create();

        expander.title =
        "website update in progress";

        expander.timestamp =
        Date.now() / 1000;

        shared_outputElement.appendChild(
            expander.element
        );

        let promise =
        CBAjax.call(
            "CBAdminPageForUpdate",
            "update"
        ).then(
            function ()
            {
                expander.title =
                "website update completed";

                expander.timestamp =
                Date.now() / 1000;
            }
        );

        return promise;
    }
    /* promiseToUpdateSite() */



    /**
     * @param int numberOfSecondsToWaitArgument
     *
     * @return Promise -> undefined
     */
    async function
    CBAdminPageForUpdate_wait(
        numberOfSecondsToWaitArgument
    ) // -> Promise -> undefined
    {
        let numberOfSecondsRemaining =
        numberOfSecondsToWaitArgument;

        let expander =
        CBUIExpander.create();

        shared_outputElement.append(
            expander.element
        );

        expander.title =
        `waiting ${numberOfSecondsRemaining} seconds`;

        expander.timestamp =
        Date.now() / 1000;

        while (
            numberOfSecondsRemaining > 0
        ) {
            await closure_waitOneSecond();

            numberOfSecondsRemaining -= 1;

            expander.title =
            `waiting ${numberOfSecondsRemaining} seconds`;
        }




        /**
         * @return Promise -> undefined
         */
        async function
        closure_waitOneSecond(
        ) // -> Promise -> undefined
        {
            return new Promise(
                function (
                    resolve
                ) // -> undefined
                {
                    setTimeout(
                        function ()
                        {
                            resolve();
                        },
                        1000
                    );
                }
            );
        }
        // closure_waitOneSecond()

    }
    // CBAdminPageForUpdate_wait()

}
)();
