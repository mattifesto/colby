/* global
    CBUINavigationView,

    CB_Admin_ModelCreate_modelTemplates,
*/

(function ()
{
    {
        let mainElement =
        document.getElementsByTagName(
            "main"
        )[0];

        let navigationView =
        CBUINavigationView.create();

        mainElement.append(
            navigationView.element
        );

        CB_Admin_ModelCreate_createUserInterface();
    }



    /**
     * @param array entry
     *
     *      [
     *          <class name>,
     *          [<template name>, ...]
     *      ]
     *
     * @return Element
     */
    function
    CB_Admin_ModelCreate_createClassElement(
        entry
    ) // -> Element
    {
        const classElement =
        document.createElement(
            "div"
        );

        const anchorElement =
        document.createElement(
            "a"
        );

        classElement.append(
            anchorElement
        );

        anchorElement.textContent =
        entry[0];

        anchorElement.href =
        "/admin/" +
        "?c=CBModelsAdminTemplateSelector" +
        "&modelClassName=" +
        entry[0];

        return classElement;
    }
    // CB_Admin_ModelCreate_createClassElement()



    /**
     * @return Element
     */
    function
    CB_Admin_ModelCreate_createContentElement(
    ) // -> Element
    {
        const contentElement =
        document.createElement(
            "div"
        );

        const entries =
        Object.entries(
            CB_Admin_ModelCreate_modelTemplates
        );

        entries.sort(
            function (
                a,
                b
            ) // -> int
            {
                const classNameA =
                a[0].toLowerCase();

                const classNameB =
                b[0].toLowerCase();

                if (
                    classNameA < classNameB
                ) {
                    return -1;
                }

                else if (
                    classNameA > classNameB
                ) {
                    return 1;
                }

                else
                {
                    return 0;
                }
            }
        );



        entries.forEach(
            function (
                entry
            ) // -> undefined
            {
                contentElement.append(
                    CB_Admin_ModelCreate_createClassElement(
                        entry
                    )
                );
            }
        );



        return (
            contentElement
        );
    }
    // CB_Admin_ModelCreate_createContentElement()



    /**
     * @return undefined
     */
    function
    CB_Admin_ModelCreate_createUserInterface(
    ) // -> undefined
    {
        let rootNavigationPanelElement =
        document.createElement(
            "div"
        );

        rootNavigationPanelElement.append(
            CB_Admin_ModelCreate_createContentElement()
        );

        CBUINavigationView.navigate(
            {
                element:
                rootNavigationPanelElement,

                title:
                "Create A Model",
            }
        );
    }
    // CB_Admin_ModelCreate_createUserInterface()

}
)();
