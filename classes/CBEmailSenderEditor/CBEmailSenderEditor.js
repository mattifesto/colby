"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBEmailSenderEditor */
/* global
    CBUI,
    CBUIStringEditor,
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
            CBUIStringEditor.createSpecPropertyEditorElement(
                "Title",
                spec,
                "title",
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
            CBUIStringEditor.createSpecPropertyEditorElement(
                "SMTP Server Hostname",
                spec,
                "SMTPServerHostname",
                specChangedCallback
            )
        );

        sectionElement.appendChild(
            CBUIStringEditor.createSpecPropertyEditorElement(
                "SMTP Server Port",
                spec,
                "SMTPServerPort",
                specChangedCallback
            )
        );

        sectionElement.appendChild(
            CBUIStringEditor.createSpecPropertyEditorElement(
                "SMTP Server Security",
                spec,
                "SMTPServerSecurity",
                specChangedCallback
            )
        );

        sectionElement.appendChild(
            CBUIStringEditor.createSpecPropertyEditorElement(
                "SMTP Server Username",
                spec,
                "SMTPServerUsername",
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
            CBUIStringEditor.createSpecPropertyEditorElement(
                "Sending Email Address",
                spec,
                "sendingEmailAddress",
                specChangedCallback
            )
        );

        sectionElement.appendChild(
            CBUIStringEditor.createSpecPropertyEditorElement(
                "Sending Email Full Name",
                spec,
                "sendingEmailFullName",
                specChangedCallback
            )
        );

        return editorElement;
    },
    /* CBUISpecEditor_createEditorElement() */

};
