"use strict";


var CBViewEditor = {};

/**
 * @return CBViewEditor
 */
CBViewEditor.init = function()
{
    this.model =
    {
        className   : "CBView",
        ID          : Colby.random160(),
        version     : 1
    };

    return this;
};
