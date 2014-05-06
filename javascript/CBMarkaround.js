"use strict";


var CBMarkaround = {};

CBMarkaround.parse = function(markaround)
{
    var HTML    = '';
    var lines   = markaround.split(/\r?\n/);
    var state   = null;

    for (var i = 0; i < lines.length; i++)
    {
        var line = lines[i].trim();

        if ('' === line)
        {
            if ("paragraph" === state)
            {
                HTML += "\n\n";
            }

            state = null;
        }
        else
        {
            /**
             * Transition to a new state, if necessary.
             */

            if (null === state)
            {
                if (/^\s*---+\s*$/.test(line))
                {
                    HTML += "<hr>";
                }
                else
                {
                    state = "paragraph";

                    HTML += "<p>";
                }
            }

            /**
             * Handle the line content for the current state.
             */

            switch (state)
            {
                case "paragraph":

                    HTML += " " + Colby.textToHTML(line);

                    break;

                default:

                    break;
            }
        }
    }

    return HTML;
};
