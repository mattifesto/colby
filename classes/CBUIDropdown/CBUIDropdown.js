"use strict"; /* jshint strict: global */

/**
 * This object creates dropdown elements with as little dogma as possible that
 * can be used for any situation requiring dropdown or similar functionality.
 *
 * Showing and hiding a dropdown is complex in the following ways which is why
 * this object exists:
 *
 *      - Dropdowns do not allow their text to be selected.
 *
 *      - Dropdowns use the default (arrow) pointer.
 *
 *      - A dropdown element is relatively positioned so that the dropdown menu
 *        can be positioned absolutely as to not change the page layout.
 *
 *      - Only a single dropdown menu can be expanded at any moment.
 *
 *      - Most importantly, when a dropdown is clicked, the dropdown menu is
 *        shown. This object will make sure the menu is at a high z-index and
 *        shade the rest of the page content and prevent other elements from
 *        being clicked at this time.
 *
 *        The dropdown will react to clicks outside the menu by hiding the menu.
 *
 *        There are some traditional ways of doing this discussed on the
 *        internet that are incorrect and/or don't work on some mobile browsers
 *        due to various inconsistencies. This class solves all of those issues.
 */
var CBUIDropdown = {

    expandedDropdownElement : undefined,
    shieldElement : undefined,

    /**
     * Creates a dropdown that contains a button and a menu. The menu will be
     * hidden until the button is clicked and will be hidden again after the
     * next click.
     *
     * To complete a dropdown you will need to add content to the dropdown
     * button and menu elements. You may also want to add custom classes to
     * these elements and the dropdown element itself so you can add custom
     * styles.
     *
     * @return {Element buttonElement, Element dropdownElement, Element menuElement}
     */
    create : function () {
        var dropdownElement = document.createElement("div");
        dropdownElement.className = "CBUIDropdown";
        var buttonElement = document.createElement("div");
        buttonElement.className = "button";
        var menuElement = document.createElement("div");
        menuElement.className = "menu";

        dropdownElement.appendChild(buttonElement);
        dropdownElement.appendChild(menuElement);

        var callback = CBUIDropdown.showDropdown.bind(undefined, {
            dropdownElement : dropdownElement,
        });

        buttonElement.addEventListener("click", callback);

        return {
            buttonElement : buttonElement,
            dropdownElement : dropdownElement,
            menuElement : menuElement,
        };
    },

    /**
     * @return undefined
     */
    hideDropdown : function () {
        var dropdownElement = CBUIDropdown.expandedDropdownElement;
        var shieldElement = CBUIDropdown.shieldElement;

        if (dropdownElement) {
            dropdownElement.classList.remove("expanded");
            dropdownElement.removeEventListener("click", CBUIDropdown.hideDropdown);

            shieldElement.classList.remove("raised");
            shieldElement.removeEventListener("click", CBUIDropdown.hideDropdown);

            CBUIDropdown.expandedDropdownElement = undefined;
        }
    },

    /**
     * @return Element
     */
    makeShieldElement : function () {
        var shieldElement = CBUIDropdown.shieldElement;

        if (shieldElement === undefined) {
            shieldElement = document.createElement("div");
            shieldElement.className = "CBUIDropdownShield";

            document.body.appendChild(shieldElement);

            CBUIDropdown.shieldElement = shieldElement;
        }

        return shieldElement;
    },

    /**
     * @param Element dropdownElement
     *
     * @return undefined
     */
    showDropdown : function (args) {
        var dropdownElement = args.dropdownElement;
        var shieldElement = CBUIDropdown.makeShieldElement();

        if (CBUIDropdown.expandedDropdownElement === undefined) {

            /* setTimeout is used because this function is usually called in
               response to a click event we need to ensure that the event
               listeners specified below happen in response to the next click,
               not a click currently being processed. */

            setTimeout(function () {
                dropdownElement.classList.add("expanded");
                dropdownElement.addEventListener("click", CBUIDropdown.hideDropdown);

                shieldElement.classList.add("raised");
                shieldElement.addEventListener("click", CBUIDropdown.hideDropdown);
            }, 0);

            CBUIDropdown.expandedDropdownElement = dropdownElement;
        }
    },
};
