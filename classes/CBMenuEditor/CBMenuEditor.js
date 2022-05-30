/* global
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
        let elements = CBUI.createElementTree(
            "CBMenuEditor",
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let element = elements[0];
        let sectionElement = elements[2];

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

            element.appendChild(
                editor.element
            );

            element.appendChild(
                CBUI.createHalfSpace()
            );
        }

        return element;
    }
    /* CBUISpecEditor_createEditorElement() */

}
)();
