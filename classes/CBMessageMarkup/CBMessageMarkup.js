"use strict";
/* jshint strict: global */
/* exported CBMessageMarkup */
/* global
    Colby */

var CBMessageMarkup = {

    /**
     * @param string message
     *
     * @return string (HTML)
     */
    convert: function (message) {
        var content;
        var line;
        var lines = message.split(/\r?\n/);
        var context = CBMessageMarkup.createContext();
        var root = context.createElement();
        root.tagName = "root";
        var current = root;

        for (var index = 0; index < lines.length; index++) {
            line = lines[index];
            var command = CBMessageMarkup.lineToCommand(line);

            if (command) {
                if (tagNameIsAllowedBlockElement(command.tagName) && tagNameAllowsBlockChildren(current.tagName)) {
                    if (content) {
                        current.addChild(content);
                        content = undefined;
                    }

                    current = context.createElement();
                    current.classNamesAsHTML = Colby.textToHTML(command.classNames.join(" "));
                    current.tagName = command.tagName;

                    if (command.tagName === "pre") {
                        current.isPreformatted = true;
                    }

                    if (command.tagName === "ol" || command.tagName === "ul") {
                        current.defaultChildTagName = "li";
                    }

                    if (command.tagName === "dl") {
                        current.defaultChildTagName = "dd";
                    }
                }

                if (command.tagName === "" && current !== root) {
                    if (content) {
                        current.addChild(content);
                        content = undefined;
                    }

                    current = current.finish();
                }

                continue;
            }

            /* blocks that accept preformatted content */

            if (current.isPreformatted) {

                /**
                 * The current block wants preformatted content.
                 */

                if (content === undefined) {
                    content = "";
                }

                content += line + "\n";
            } else if (line.trim() !== "") {

                /**
                 * The current line is a line in a content paragraph.
                 */

                if (content === undefined) {
                    content = "";
                }

                content += line + "\n";
            } else {

                /**
                 * The current line ends a content paragraph.
                 */

                if (content) {
                    current.addChild(content);
                    content = undefined;
                }
            }
        }

        if (content) {
            current.addChild(content);
            content = undefined;
        }

        while (current !== undefined) {
            current = current.finish();
        }

        return root.html;

        /* @return bool */
        function tagNameIsAllowedBlockElement(string) {
            var tagNames = [
                "blockquote",
                "dd",
                "div",
                "dl",
                "dt",
                "h1",
                "h2",
                "h3",
                "h4",
                "h5",
                "h6",
                "li",
                "pre",
                "ol",
                "ul",
            ];

            return tagNames.indexOf(string) !== -1;
        }

        /* @return bool */
        function tagNameAllowsBlockChildren(tagName) {
            return [
                "blockquote",
                "dd",
                "div",
                "dl",
                "dt",
                "li",
                "ol",
                "root", // custom
                "ul",
            ].indexOf(tagName) !== -1;
        }
    },

    convertInline: function (content) {
        var atLeastOneReplacementWasMade = false;
        var escapedOpenBraceReplacement = "f5a6328bda8575b25bbc5f0ece0181df57e54ed1";
        var escapedCloseBraceReplacement = "edc679d4ac06a45884a23160030c4cb2d4b2ebf1";


        content = Colby.textToHTML(content)
                  .replace(/\\\{/g, escapedOpenBraceReplacement)
                  .replace(/\\\}/g, escapedCloseBraceReplacement);

        do {
            atLeastOneReplacementWasMade = false;

            content = content.replace(/\{([a-z]+):(?:\s+([^\{\}]+))?\}/g, replace);
        } while (atLeastOneReplacementWasMade);

        content = content.replace(new RegExp(escapedOpenBraceReplacement, "g"), '{')
                         .replace(new RegExp(escapedCloseBraceReplacement, "g"), '}');

        return content;

        function replace(original, tagName, content) {
            var matches, text;

            if (content === undefined) {
                if (["br", "wbr"].indexOf(tagName) > -1) {
                    return "<" + tagName + ">";
                }
            } else if (tagName === "a") {
                matches = content.match(/([\s\S]*)\shref:\s([\s\S]*)/);

                if (matches) {
                    text = matches[1].trim();
                    var href = matches[2].trim();

                    atLeastOneReplacementWasMade = true;
                    return '<a href="' + href + '">' + text + '</a>';
                }
            } else if (tagName === "abbr") {
                matches = content.match(/([\s\S]*)\stitle:\s([\s\S]*)/);

                if (matches) {
                    text = matches[1].trim();
                    var title = matches[2].trim();

                    atLeastOneReplacementWasMade = true;
                    return '<abbr title="' + title + '">' + text + '</abbr>';
                }
            } else if (["b", "cite", "code", "em", "i", "kbd", "mark", "q", "rp", "rt", "rtc", "ruby", "s", "samp", "small", "span", "strong", "sub", "sup", "time", "u", "var"].indexOf(tagName) > -1) {
                atLeastOneReplacementWasMade = true;

                return "<" + tagName + ">" + content + "</" + tagName + ">";
            }

            return original;
        }
    },

    /**
     * @return object
     *
     *      {
     *          createElement: function
     *      }
     */
    createContext: function() {
        var current;
        var root;
        var stack = [];

        return {
            createElement: createElement,
        };

        /**
         * @param object? parent
         *
         * @return object
         *
         *      {
         *          addChild: function,
         *          finish: function,
         *          parent: object (may not be necessary)
         *      }
         */
        function createElement() {
            var children = [];
            var element = {
                addChild: function (child) {
                    children.push(child);
                },
                finish: finish,
                parent: current,
            };

            if (current) {
                current.addChild(element);
            } else {
                root = element;
            }

            stack.push(element);

            current = element;

            return element;

            /**
             *
             */
            function finish() {
                var html = "";

                children.forEach(function (child) {
                    if (child.html) {
                        html += child.html;
                    } else {
                        if (typeof child !== "string") {
                            throw new Error("child should be a string");
                        }

                        var childHTML = CBMessageMarkup.convertInline(child);

                        if (element.isPreformatted) {
                            html += childHTML;
                        } else if (element.defaultChildTagName) {
                            html += "<" + element.defaultChildTagName + ">\n" +
                                    "<p>" + childHTML +
                                    "</" + element.defaultChildTagName + ">\n";
                        } else {
                            html += "<p>" + childHTML;
                        }
                    }
                });

                if (element === root) {
                    element.html = html;
                } else {
                    var classAttribute;

                    if (element.classNamesAsHTML) {
                        classAttribute = ' class="' + element.classNamesAsHTML + '"';
                    } else {
                        classAttribute = "";
                    }


                    element.html = "<" + element.tagName + classAttribute + ">\n" +
                                 html +
                                 "</" + element.tagName + ">\n";

                }

                stack.pop();

                return element.parent;
            }
        }
    },

    /**
     * @param string line
     *
     * @return object|false
     */
    lineToCommand: function (line) {
        var tagName, classNames;
        var r = /^\s*---(\s.*)?$/;
        var matches = line.match(r, line);

        if (matches === null) {
            return false;
        }

        classNames = matches[1] ? matches[1].trim() : "";

        if (classNames === "") {
            classNames = [];
            tagName = "";
        } else {
            classNames = classNames.split(/\s+/);
            tagName = classNames.shift();
        }

        return {
            classNames: classNames,
            tagName: tagName,
        };
    },
};
