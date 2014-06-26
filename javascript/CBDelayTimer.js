"use strict";


/**
 *
 */
function CBDelayTimer()
{
    this.delayInMilliseconds    = 5000;
    this.pauseRequestCount      = 0;
    this.status                 = "inactive";

    window.addEventListener("beforeunload", this.windowWillUnload.bind(this), false);
}

/**
 * This method will increment the pause request count. While the pause request
 * count is greater than zero, callback execution will be postponed.
 */
CBDelayTimer.prototype.pause = function()
{
    this.pauseRequestCount++;
};

/**
 * This method restarts the timer so that, after this method is called, the
 * callback won't be called until after the full delay has elapsed.
 */
CBDelayTimer.prototype.restart = function()
{
    if (this.timeoutID)
    {
        clearTimeout(this.timeoutID);
    }

    this.status     = "active";
    this.timeoutID  = setTimeout(this.timeoutDidFinish.bind(this), this.delayInMilliseconds);
};

/**
 * This method will decrement the pause request count. If the pause request
 * count goes to zero and the timer is in "pending" status, then the callback
 * will be executed.
 */
CBDelayTimer.prototype.resume = function()
{
    this.pauseRequestCount--;

    if (0 == this.pauseRequestCount &&
        "pending" == this.status)
    {
        this.status = "inactive";

        if (this.callback)
        {
            this.callback.call();
        }
    }
};

/**
 * If the pause request count is zero, the callback will be executed. Otherwise,
 * the status will be set to "pending" so that the callback will be executed
 * when the pause request count becomes zero.
 *
 * This is a private method and shouldn't be called by clients of this class.
 */
CBDelayTimer.prototype.timeoutDidFinish = function()
{
    this.timeoutID = undefined;

    if (this.pauseRequestCount > 0)
    {
        this.status = "pending";
    }
    else
    {
        this.status = "inactive";

        if (this.callback)
        {
            this.callback.call();
        }
    }
};

/**
 * This is a private method which exists to make sure the callback is called
 * if necessary before the page unloads.
 *
 * If the pause request count is greater than zero, the user will asked by
 * the browser if they really want to leave the page. If they leave, the pending
 * callbacks will not be executed. If they stay, things will continue to run as
 * if they had never attempted to leave the page.
 */
CBDelayTimer.prototype.windowWillUnload = function()
{
    if (this.pauseRequestCount > 0)
    {
        return "This page contains unsaved data.";
    }
    else if ("active" == this.status)
    {
        this.windowIsUnloading = true;

        clearTimeout(this.timeoutID);

        this.timeoutDidFinish();

        this.windowIsUnloading = undefined;
    }
};
