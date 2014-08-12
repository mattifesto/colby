"use strict";

/**
 *
 */
function CBMenuEditor()
{
    this.initialize();
}

/**
 *
 */
CBMenuEditor.prototype.initialize = function()
{
    var main = document.getElementsByTagName("main")[0];

    this.editingTextArea                = document.createElement("textarea");
    this.editingTextArea.style.display  = "block";
    this.editingTextArea.style.height   = "700px";
    this.editingTextArea.style.margin   = "30px auto";
    this.editingTextArea.style.width    = "720px";
    this.editingTextArea.addEventListener("input", this.editingTextAreaInputHandler());

    this.displayTextArea                = document.createElement("textarea");
    this.displayTextArea.readOnly       = true;
    this.displayTextArea.style.display  = "block";
    this.displayTextArea.style.height   = "200px";
    this.displayTextArea.style.margin   = "30px auto";
    this.displayTextArea.style.width    = "720px";

    main.appendChild(this.editingTextArea);
    main.appendChild(this.displayTextArea);

    this.editingTextArea.value = "{\"home\":{\"titleHTML\":\"Home\",\"URI\":\"/\",\"submenu\":null},\"blog\":{\"titleHTML\":\"Blog\",\"URI\":\"/blog/\",\"submenu\":null},\"books\":{\"titleHTML\":\"Books\",\"URI\":\"/books/\",\"submenu\":{\"best sellers\":{\"titleHTML\":\"Best Sellers\",\"URI\":\"/best-sellers/\",\"submenu\":null},\"childrens books\":{\"titleHTML\":\"Childrens&rsquo; Books\",\"URI\":\"/childrens-books/\",\"submenu\":null}}}}";
    this.editingTextAreaHasChanged();
};

/**
 *
 */
CBMenuEditor.prototype.editingTextAreaInputHandler = function()
{
    var self    = this;
    var handler = function()
    {
        self.editingTextAreaHasChanged();
    };

    return handler;
};

/**
 *
 */
CBMenuEditor.prototype.editingTextAreaHasChanged = function()
{
    try
    {
        var theJSON = JSON.parse(this.editingTextArea.value);

        this.editingTextArea.value = JSON.stringify(theJSON, null, 8);
        this.displayTextArea.value = JSON.stringify(theJSON);

        this.editingTextArea.style.backgroundColor = "#ffffff";
    }
    catch(exception)
    {
        this.editingTextArea.style.backgroundColor = "#ffbfbf";
    }
};

/**
 *
 */

document.addEventListener("DOMContentLoaded", function() { new CBMenuEditor(); });
