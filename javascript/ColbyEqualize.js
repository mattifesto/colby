"use strict";

/**
 * console.log
 *
 * This polyfill won't do anything but allow the method to be called without
 * error.
 */

if (!window.console)
{
    var console = {};

    console.log = function()
    {
    };

    window.console = console;
}

/**
 * HTMLDocument.addEventListener
 */

if (!HTMLDocument.prototype.addEventListener)
{
    HTMLDocument.prototype.addEventListener = function(type, listener, useCapture)
    {
        var newType = "on" + type;

        if ('DOMContentLoaded' == type)
        {
            newType = "onload";
        }

        window.attachEvent(newType, listener);
    };
}

/**
 * Element.addEventListener
 */

if (!Element.prototype.addEventListener)
{
    Element.prototype.addEventListener = function(type, listener, useCapture)
    {
        var newType = "on" + type;

        if ('DOMContentLoaded' == type)
        {
            newType = "onload";
        }

        this.attachEvent(newType, listener);
    };
}

/**
 * HTMLDocument.getElementsByClassName
 */

if (!HTMLDocument.prototype.getElementsByClassName)
{
    HTMLDocument.prototype.getElementsByClassName = function(className)
    {
        return this.querySelectorAll("." + className);
    }
}

/**
 * Element.getElementsByClassName
 */

if (!Element.prototype.getElementsByClassName)
{
    Element.prototype.getElementsByClassName = function(className)
    {
        return this.querySelectorAll("." + className);
    }
}

/**
 * Element.classList
 *
 * Source: https://github.com/eligrey/classList.js
 */

if(typeof document!=="undefined"&&!("classList" in document.createElement("a"))){(function(j){if(!("HTMLElement" in j)&&!("Element" in j)){return}var a="classList",f="prototype",m=(j.HTMLElement||j.Element)[f],b=Object,k=String[f].trim||function(){return this.replace(/^\s+|\s+$/g,"")},c=Array[f].indexOf||function(q){var p=0,o=this.length;for(;p<o;p++){if(p in this&&this[p]===q){return p}}return -1},n=function(o,p){this.name=o;this.code=DOMException[o];this.message=p},g=function(p,o){if(o===""){throw new n("SYNTAX_ERR","An invalid or illegal string was specified")}if(/\s/.test(o)){throw new n("INVALID_CHARACTER_ERR","String contains an invalid character")}return c.call(p,o)},d=function(s){var r=k.call(s.className),q=r?r.split(/\s+/):[],p=0,o=q.length;for(;p<o;p++){this.push(q[p])}this._updateClassName=function(){s.className=this.toString()}},e=d[f]=[],i=function(){return new d(this)};n[f]=Error[f];e.item=function(o){return this[o]||null};e.contains=function(o){o+="";return g(this,o)!==-1};e.add=function(){var s=arguments,r=0,p=s.length,q,o=false;do{q=s[r]+"";if(g(this,q)===-1){this.push(q);o=true}}while(++r<p);if(o){this._updateClassName()}};e.remove=function(){var t=arguments,s=0,p=t.length,r,o=false;do{r=t[s]+"";var q=g(this,r);if(q!==-1){this.splice(q,1);o=true}}while(++s<p);if(o){this._updateClassName()}};e.toggle=function(p,q){p+="";var o=this.contains(p),r=o?q!==true&&"remove":q!==false&&"add";if(r){this[r](p)}return !o};e.toString=function(){return this.join(" ")};if(b.defineProperty){var l={get:i,enumerable:true,configurable:true};try{b.defineProperty(m,a,l)}catch(h){if(h.number===-2146823252){l.enumerable=false;b.defineProperty(m,a,l)}}}else{if(b[f].__defineGetter__){m.__defineGetter__(a,i)}}}(self))};

/**
 * XMlHttpRequest2
 */

if (typeof FormData === "undefined")
{
    var FormData = function()
    {
        this.data = "";
    }

    FormData.prototype.append = function(name, value)
    {
        if (this.data)
        {
            this.data += "&";
        }

        this.data += encodeURIComponent(name) + "=" + encodeURIComponent(value);
    };

    FormData.prototype.toString = function()
    {
        return this.data;
    };

    var ColbyCreateXMLHttpRequest = function()
    {
        var faux = {};

        faux.xhr = new XMLHttpRequest();

        faux.open = function(method, url, async)
        {
            faux.xhr.open(method, url, async);
        };

        faux.send = function(data)
        {
            faux.xhr.onload = this.onload;
            faux.xhr.onreadystatechange = function()
            {
                if (this.readyState === 4)
                {
                    if (typeof this.onload === "function")
                    {
                        this.onload.call(this);
                    }
                }
            }

            if (data)
            {
                data = data.toString();
            }

            faux.xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            faux.xhr.send(data);
        };

        return faux;
    };
}
else
{
    var ColbyCreateXMLHttpRequest = function() { return new XMLHttpRequest(); };
}

/**
 * Element.textContent
 *
 * Source: http://eligrey.com/blog/post/textcontent-in-ie8
 */

if (Object.defineProperty &&
    Object.getOwnPropertyDescriptor &&
    Object.getOwnPropertyDescriptor(Element.prototype, "textContent") &&
    !Object.getOwnPropertyDescriptor(Element.prototype, "textContent").get)
{
    (function()
    {
        var innerTextDescriptor = Object.getOwnPropertyDescriptor(Element.prototype, "innerText");

        var descriptor =
        {
            // It won't work if you just drop in innerText.get
            // and innerText.set or the whole descriptor.
            get : function()
            {
                return innerTextDescriptor.get.call(this)
            },
            set : function(x)
            {
                return innerTextDescriptor.set.call(this, x)
            }
        }

        Object.defineProperty(Element.prototype, "textContent", descriptor);
    }
    )();
}
