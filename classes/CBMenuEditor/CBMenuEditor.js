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
        CBUISpecEditor_createEditorElement,
    };



    /**
     * @param Object args.spec
     * @param function args.specChangedCallback
     *
     * @return Element
     */
    function
    CBUISpecEditor_createEditorElement(
        args
    ) // -> Element
    {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;

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
            if (!args.spec.items) {
                args.spec.items = [];
            }

            let editor = CBUISpecArrayEditor.create(
                {
                    addableClassNames: ["CBMenuItem"],
                    specs: args.spec.items,
                    specsChangedCallback: args.specChangedCallback,
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
