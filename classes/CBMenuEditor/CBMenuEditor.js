/* global
    CB_UI_StringEditor,
    CBModel,
    CBUI,
    CBUISpecArrayEditor,
    CBUIStringEditor2,
*/


(function ()
{
    "use strict";

    window.CBMenuEditor =
    {
        CBUISpecEditor_createEditorElement2,
    };



    /**
     * @param Object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function
    CBUISpecEditor_createEditorElement2(
        spec,
        specChangedCallback
    ) // -> Element
    {
        let rootElement =
        document.createElement(
            "div"
        );

        rootElement.className =
        "CBMenuEditor_root_element";



        rootElement.append(
            CBMenuEditor_createAdministrativeTitleEditorElement(
                spec,
                specChangedCallback
            )
        );



        let sectionElement;

        {
            let elements =
            CBUI.createElementTree(
                "CBUI_sectionContainer",
                "CBUI_section"
            );

            let sectionContainerElement =
            elements[0];

            rootElement.append(
                sectionContainerElement
            );

            sectionElement =
            elements[1];
        }



        /* title */

        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "title",
                "Title",
                specChangedCallback
            )
        );

        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "titleURI",
                "Title URI",
                specChangedCallback
            )
        );

        /* menu items */
        {
            if (
                !spec.items
            ) {
                spec.items =
                [];
            }

            let editor = CBUISpecArrayEditor.create(
                {
                    addableClassNames: ["CBMenuItem"],
                    specs: spec.items,
                    specsChangedCallback: specChangedCallback,
                }
            );

            editor.title = "Menu Items";

            rootElement.appendChild(
                editor.element
            );

            rootElement.appendChild(
                CBUI.createHalfSpace()
            );
        }

        return (
            rootElement
        );
    }
    /* CBUISpecEditor_createEditorElement() */



    // -- functions



    /**
     * @param object spec
     * @param function specChangedCallback
     *
     * @return Element
     */
    function
    CBMenuEditor_createAdministrativeTitleEditorElement(
        spec,
        specChangedCallback
    ) // -> Element
    {
        let stringEditor =
        CB_UI_StringEditor.create();

        stringEditor.CB_UI_StringEditor_setTitle(
            "Administrative Title"
        );

        stringEditor.CB_UI_StringEditor_setValue(
            CBModel.valueToString(
                spec,
                'CBMenu_administrativeTitle_property'
            )
        );

        stringEditor.CB_UI_StringEditor_setChangedEventListener(
            function (
            ) // -> undefined
            {
                let newAdministrativeTitle =
                stringEditor.CB_UI_StringEditor_getValue();

                spec.CBMenu_administrativeTitle_property =
                newAdministrativeTitle;

                specChangedCallback();
            }
        );

        return (
            stringEditor.CB_UI_StringEditor_getElement()
        );
    }
    // CBMenuEditor_createAdministrativeTitleEditorElement()
}
)();
