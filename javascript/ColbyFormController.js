"use strict";

/**
 *
 */
function ColbyFormController()
{
    this.formElement = null;
    this.fieldElements = {};
    this.formHasChanged = false;
    this.formChangeTimeoutDuration = 3000;
    this.formChangeTimeoutId = null;
    this.isProcessingFormChanges = false;
}

/**
 * @return ColbyFormController
 */
ColbyFormController.formControllerForId = function(formElementId)
{
    var controller = new ColbyFormController();

    controller.formElement = document.getElementById(formElementId);

    if (!controller.formElement)
    {
        return null;
    }

    /**
     * Retrieve all off the form fields from the form.
     *
     * These are the variables required for this task.
     */

    var element;
    var elements;
    var i;

    /**
     * Retrieve the `input` elements.
     */

    elements = controller.formElement.getElementsByTagName("input");

    i = 0;

    while (element = elements.item(i))
    {
        if (element.name)
        {
            controller.fieldElements[element.name] = element;
        }

        i++;
    }

    /**
     * Retrieve the `select` elements.
     */

    elements = controller.formElement.getElementsByTagName("select");

    i = 0;

    while (element = elements.item(i))
    {
        if (element.name)
        {
            controller.fieldElements[element.name] = element;
        }

        i++;
    }

    /**
     * Retrieve the `textarea` elements.
     */

    elements = controller.formElement.getElementsByTagName("textarea");

    i = 0;

    while (element = elements.item(i))
    {
        if (element.name)
        {
            controller.fieldElements[element.name] = element;
        }

        i++;
    }

    /**
     * Attach a change handler to each of the field elements.
     */

    var fieldElementHasChanged = function()
    {
        /**
         * When the field element changes, `this` will refer to the field
         * element.
         */

        controller.fieldElementHasChanged(/* fieldElement: */ this);
    };

    for (var key in controller.fieldElements)
    {
        element = controller.fieldElements[key];

        /**
         * Sometimes we don't want to react to changes for specific form
         * elements.
         */

        if (element.classList.contains("ignore-changes"))
        {
            continue;
        }

        /**
         * The `tagName` property is always in all capitals.
         * The `type` property is always in all lowercase.
         */

        if (element.tagName == "INPUT")
        {
            if (element.type == "checkbox" ||
                element.type == "file")
            {
                element.addEventListener("change", fieldElementHasChanged, false);
            }
            else if (element.type == "text")
            {
                element.addEventListener("input", fieldElementHasChanged, false);
            }
        }
        else if (element.tagName == "SELECT")
        {
            element.addEventListener("change", fieldElementHasChanged, false);
        }
        else if (element.tagName == "TEXTAREA")
        {
            element.addEventListener("input", fieldElementHasChanged, false);
        }
    }

    /**
     * Make sure than any pending changes get processed before the page
     * unloads. Normally we would wait for any currently processing changes
     * to finish but in this case we don't have time for that and have to
     * process the new changes and hope for the best. It's unlikely, but
     * theoretically possible, for instance, that ajax methods could be sent
     * to the server out of order and changes could be lost.
     */

    var windowWillUnload = function()
    {
        if (controller.formHasChanged)
        {
            /**
             * Normally we would assume that the `processFormChanges` method
             * would eventually call `hasProcessedFormChanges`, but since we
             * can't wait in the case that it calls it asynchronously, we call
             * it here immediately. Duplicate calls to this function will not
             * result in an error, but missing calls to this may.
             */

            controller.willProcessFormChanges();
            controller.processFormChanges();
            controller.hasProcessedFormChanges();
        }
    };

    window.addEventListener('beforeunload', windowWillUnload, false);

    return controller;
};

/**
 * @return void
 */
ColbyFormController.prototype.fieldElementHasChanged = function(fieldElement)
{
    this.formHasChanged = true;

    /**
     * Add the "has-changed" class to the element and potentially to its
     * parent element.
     */

    fieldElement.classList.add("has-changed");

    if (fieldElement.parentElement.classList.contains("field-wrapper"))
    {
        fieldElement.parentElement.classList.add("has-changed");
    }

    /**
     * Restart form change timeout.
     */

    if (this.formChangeTimeoutId)
    {
        clearTimeout(this.formChangeTimeoutId);
    }

    var controller = this;

    var formChangeTimeoutHasEnded = function()
    {
        /**
         * If the previous set of form changes is still processing then delay
         * processing the next set of changes until after the previous set has
         * finished processing.
         */

        if (controller.isProcessingFormChanges)
        {
            controller.formChangeTimeoutId = setTimeout(formChangeTimeoutHasEnded,
                                                        controller.formChangeTimeoutDuration);

            return;
        }

        controller.formChangeTimeoutId = null;

        controller.willProcessFormChanges();

        controller.processFormChanges();

        /**
         * The `formHasChanged` instance variable is set to `false` now because
         * it makes sense that if code in the functions above asks if the form
         * has changed the value will be `true`. After the changes have been
         * processed, even if an Ajax call is made meaning the processing isn't
         * complete at this point, the `formHasChanged` instance variable will
         * be set back to `false` meaning that there have been no form changes
         * since processing of the last set of form changes began.
         */

        controller.formHasChanged = false;
    };

    this.formChangeTimeoutId = setTimeout(formChangeTimeoutHasEnded, this.formChangeTimeoutDuration);
};

/**
 * @return void
 */
ColbyFormController.prototype.hasProcessedFormChanges = function()
{
    var element;

    for (var key in this.fieldElements)
    {
        element = this.fieldElements[key];

        if (element.classList.contains("change-is-being-processed"))
        {
            element.classList.remove("change-is-being-processed");

            if (element.parentElement.classList.contains("field-wrapper"))
            {
                element.parentElement.classList.remove("change-is-being-processed");
            }
        }
    }

    this.isProcessingFormChanges = false;
};

/**
 * The default implementation of this function does nothing but immediately
 * call `hasProcessedFormChanges`. If this function is overridden the
 * implementation should do whatever it needs to do and then call
 * `hasProcessedFormChanges` once it is finished, even if it has to release
 * control of the thread to make an Ajax call first.
 *
 * @return void
 */
ColbyFormController.prototype.processFormChanges = function()
{
    this.hasProcessedFormChanges();
};

/**
 * @return void
 */
ColbyFormController.prototype.willProcessFormChanges = function()
{
    this.isProcessingFormChanges = true;

    var element;

    for (var key in this.fieldElements)
    {
        element = this.fieldElements[key];

        if (element.classList.contains("has-changed"))
        {
            element.classList.remove("has-changed");
            element.classList.add("change-is-being-processed");

            if (element.parentElement.classList.contains("field-wrapper"))
            {
                element.parentElement.classList.remove("has-changed");
                element.parentElement.classList.add("change-is-being-processed");
            }
        }
    }
};
