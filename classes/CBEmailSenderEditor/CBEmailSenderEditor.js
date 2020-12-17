"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBEmailSenderEditor */
/* global
    CBUI,
    CBUIStringEditor,
    CBUIStringEditor2,
*/


var CBEmailSenderEditor = {

    /* -- CBUISpecEditor interfaces -- -- -- -- -- */



    /**
     * @param object args
     *
     *      {
     *          spec: object
     *          specChangedCallback: function
     *      }
     *
     * @return Element
     */
    CBUISpecEditor_createEditorElement(
        args
    ) {
        let spec = args.spec;
        let specChangedCallback = args.specChangedCallback;

        let elements = CBUI.createElementTree(
            "CBEmailSenderEditor",
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        let editorElement = elements[0];
        let sectionElement = elements[2];

        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "title",
                "Title",
                specChangedCallback
            )
        );

        elements = CBUI.createElementTree(
            "CBUI_sectionContainer",
            "CBUI_section"
        );

        sectionElement = elements[1];

        editorElement.appendChild(
            elements[0]
        );

        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "SMTPServerHostname",
                "SMTP Server Hostname",
                specChangedCallback
            )
        );

        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "SMTPServerPort",
                "SMTP Server Port",
                specChangedCallback
            )
        );

        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "SMTPServerSecurity",
                "SMTP Server Security",
                specChangedCallback
            )
        );

        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "SMTPServerUsername",
                "SMTP Server Username",
                specChangedCallback
            )
        );

        sectionElement.appendChild(
            CBUIStringEditor.createSpecPropertyEditorElement(
                "SMTP Server Password",
                spec,
                "SMTPServerPassword",
                specChangedCallback,
                {
                    inputType: "password",
                }
            )
        );

        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "sendingEmailAddress",
                "Sending Email Address",
                specChangedCallback
            )
        );

        sectionElement.appendChild(
            CBUIStringEditor2.createObjectPropertyEditorElement(
                spec,
                "sendingEmailFullName",
                "Sending Email Full Name",
                specChangedCallback
            )
        );

        return editorElement;
    },
    /* CBUISpecEditor_createEditorElement() */

};
