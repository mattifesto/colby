"use strict";


var CBViewEditor = {};

/**
 * This is a designated initializer.
 *
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

/**
 * This is a designated initializer.
 *
 * @return CBViewEditor
 */
CBViewEditor.initWithModel = function(model)
{
    this.model = model;

    return this;
};
