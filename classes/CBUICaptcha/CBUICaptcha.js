"use strict"; /* jshint strict: global */
/* globals CBUICaptchaReCAPTCHASiteKey, Colby, grecaptcha */

var CBUICaptcha = {

    /**
     * @return {
     *  Element element,
     * }
     */
    create : function (args) {
        var element = document.createElement("div");
        element.className = "CBUICaptcha";
        var captcha = document.createElement("div");
        captcha.className = "g-recaptcha";

        captcha.setAttribute("data-callback", "CBUICaptchaUserWasVerified");
        captcha.setAttribute("data-expired-callback", "CBUICaptchaResponseDidExpire");
        captcha.setAttribute("data-sitekey", CBUICaptchaReCAPTCHASiteKey);

        element.appendChild(captcha);

        return {
            element : element,
        };
    },

    /**
     * @param string args.responseKey
     *
     * @return undefined
     */
    verify : function (args) {
        var data = new FormData();
        data.append("responseKey", args.responseKey);

        var xhr = new XMLHttpRequest();
        xhr.onerror = Colby.displayXHRError.bind(undefined, {xhr : xhr});
        xhr.onload = CBUICaptcha.verifyDidLoad.bind(undefined, {xhr : xhr});

        xhr.open("POST", "/api/?class=CBUICaptcha&function=verify");
        xhr.send(data);
    },

    /**
     * @param XMLHttpRequest args.xhr
     *
     * @return undefined
     */
    verifyDidLoad : function (args) {
        var response = Colby.responseFromXMLHttpRequest(args.xhr);

        if (response.wasSuccessful) {
            alert('yay');
        } else {
            Colby.displayResponse(response);
        }
    },
};

/**
 * @param string responseKey
 *
 * @return undefined
 */
function CBUICaptchaUserWasVerified(responseKey) {
    CBUICaptcha.verify({
        responseKey : responseKey,
    });
}

function CBUICaptchaResponseDidExpire() {
    grecaptcha.reset();
}
