"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported
    CBUICaptcha,
    CBUICaptchaWasCompleted,
    CBUICaptchaDidExpire,
*/
/* globals
    CBUICaptchaReCAPTCHASiteKey,
    grecaptcha,
*/

/**
 * This control can currently only be used one time per page.
 */
var CBUICaptcha = {

    captchaDidExpireCallback: undefined,
    captchaWasCompletedCallback: undefined,

    /**
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     *
     * @return undefined
     */
    captchaDidExpire: function (args) {
        args.spec[args.propertyName] = undefined;

        grecaptcha.reset();

        args.specChangedCallback.call();
    },

    /**
     * @param string args.propertyName
     * @param object args.spec
     * @param function args.specChangedCallback
     * @param string responseKey
     *
     * @return undefined
     */
    captchaWasCompleted: function (args, responseKey) {
        args.spec[args.propertyName] = responseKey;

        args.specChangedCallback.call();
    },

    /**
     * @param string args.propertyName
     *  This is the name of the property that will hold the responseKey provided
     *  by Google in response to the captcha being completed.
     * @param object args.spec
     * @param object args.specChangedCallback
     *
     * @return {
     *  Element element,
     * }
     */
    create: function (args) {
        if (CBUICaptcha.captchaDidExpireCallback !== undefined) {
            throw "The CBUICaptcha control can only be used once per page.";
        }

        var element = document.createElement("div");
        element.className = "CBUICaptcha";
        var captcha = document.createElement("div");
        captcha.className = "g-recaptcha";

        captcha.setAttribute("data-callback", "CBUICaptchaWasCompleted");
        captcha.setAttribute("data-expired-callback", "CBUICaptchaDidExpire");
        captcha.setAttribute("data-sitekey", CBUICaptchaReCAPTCHASiteKey);

        element.appendChild(captcha);

        CBUICaptcha.captchaDidExpireCallback = CBUICaptcha.captchaDidExpire.bind(undefined, {
            propertyName: args.propertyName,
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        });

        CBUICaptcha.captchaWasCompletedCallback = CBUICaptcha.captchaWasCompleted.bind(undefined, {
            propertyName: args.propertyName,
            spec: args.spec,
            specChangedCallback: args.specChangedCallback,
        });

        return {
            element: element,
        };
    },
};

/**
 * @param string responseKey
 *
 * @return undefined
 */
function CBUICaptchaWasCompleted(responseKey) {
    CBUICaptcha.captchaWasCompletedCallback(responseKey);
}

/**
 * @return undefined
 */
function CBUICaptchaDidExpire() {
    CBUICaptcha.captchaDidExpireCallback();
}
