"using strict";

function CBContinuousAjaxRequest(URL)
{
    this.allRequestsDidCompleteCallback = null;
    this.delay                          = 0;
    this.onload                         = null;
    this.queuedFormData                 = null;
    this.requestIsActive                = false;
    this.timerID                        = null;
    this.URL                            = URL;
    // TODO: need to keep track of whether a request is active in case it takes longer to return than the delay
}

/**
 * @return void
 */
CBContinuousAjaxRequest.prototype.makeRequestWithFormData = function(formData)
{
    this.queuedFormData = formData;

    if (this.timerID)
    {
        clearTimeout(this.timerID);
    }

    self = this;

    var sendRequest = function()
    {
        self.timerID = null;

        self.sendRequestIfReady();
    };

    this.timerID = setTimeout(sendRequest, this.delay);
};

/**
 * @return void
 */
CBContinuousAjaxRequest.prototype.sendRequest = function()
{
    var xhr                     = new XMLHttpRequest();
    xhr.continuousAjaxRequest   = this;

    xhr.open("POST", this.URL, true);
    xhr.onload = CBContinuousAjaxRequest.didLoad;
    xhr.send(this.queuedFormData);

    this.queuedFormData = null;
    this.requestIsActive = true;
};

/**
 * This function looks at the current state of the object to determine if now
 * is a good time to send a request. If it is, a request is sent, if not one
 * isn't.
 */
CBContinuousAjaxRequest.prototype.sendRequestIfReady = function()
{
    if (!this.queuedFormData || this.requestIsActive || this.timerID)
    {
        return;
    }

    this.sendRequest();
};

/**
 * @return void
 */
CBContinuousAjaxRequest.didLoad = function()
{
    var self     = this.continuousAjaxRequest;

    self.requestIsActive = false;

    if (self.onload)
    {
        self.onload.call(this);
    }

    if (!self.queuedFormData && self.allRequestsDidCompleteCallback)
    {
        self.allRequestsDidCompleteCallback.call(this);
    }

    self.sendRequestIfReady();
};
