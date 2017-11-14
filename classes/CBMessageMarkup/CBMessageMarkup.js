"use strict";
/* jshint strict: global */
/* exported CBMessageMarkup */
/* global
    Colby */

var CBMessageMarkup = {

    /**
     * @deprecated use CBMessageMarkup.markupToHTML()
     */
    convert: function (markup) {
        return CBMessageMarkup.markupToHTML(markup);
    },

    /**
     * @param [object] stack
     *
     * @return object
     *
     *      {
     *          children: [mixed],
     *          classNamesAsHTML: string
     *          defaultChildTagName: string
     *          isPreformatted: bool
     *          tagName: string
     *      }
     */
    createElement: function (stack) {
        var element = {
            children: [],
            classNamesAsHTML: '',
            defaultChildTagName: '',
            isPreformatted: false,
            tagName: '',
        };

        if (stack.length > 0) {
            var parentElement = stack[stack.length - 1];
            parentElement.children.push(element);
        }

        stack.push(element);

        return element;
    },


    /**
     * Once a paragraph has been converted this will decode encoded markup
     * characters to their unescaped form.
     *
     * @param string $markup
     *
     * @return string
     */
    decodeEncodedCharacters: function (markup) {
        return markup.replace(new RegExp(CBMessageMarkup.encodedOpenBrace, "g"), "{")
                     .replace(new RegExp(CBMessageMarkup.encodedCloseBrace, "g"), "}")
                     .replace(new RegExp(CBMessageMarkup.encodedCommand, "g"), "---");
    },

    /**
     * @param [object] stack
     *
     * @return object|undefined
     */
    elementFinish: function(stack) {
        var element = stack.pop();
        var html = "";

        element.children.forEach(function (child) {
            if (child.html) {
                html += child.html;
            } else {
                if (typeof child !== "string") {
                    throw new Error("child should be a string");
                }

                var childHTML = CBMessageMarkup.paragraphToHTML(child);

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

        if (stack.length === 0) {
            /* root element */
            element.html = html;

            return undefined;
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

        return stack[stack.length - 1];
    },

    /**
     * Encodes escaped markup characters in preparation for the paragraph to be
     * converted.
     *
     * @param string $markup
     *
     * @return string
     */
    encodeEscapedCharacters: function (markup) {
        return markup.replace(/\\\{/g, CBMessageMarkup.encodedOpenBrace)
                     .replace(/\\\}/g, CBMessageMarkup.encodedCloseBrace)
                     .replace(/\\---/g, CBMessageMarkup.encodedCommand);
    },

    get encodedOpenBrace() {
        return "f5a6328bda8575b25bbc5f0ece0181df57e54ed1";
    },

    get encodedCloseBrace() {
        return "edc679d4ac06a45884a23160030c4cb2d4b2ebf1";
    },

    get encodedCommand() {
        return "4605702366f1f3d132e1a76a25165e2c0b6b352c";
    },

    /**
     * @param string original
     *
     *      "{stong: Extra!}"
     *      "{a Wikipedia href: https://www.wikipedia.org}"
     *      "{br:}"
     *
     * @param string inlineTagName
     *
     *      "strong"
     *      "a"
     *      "br"
     *
     * @param string? inlineContent
     *
     *      "Extra!"
     *      "Wikipedia href: https://www.wikipedia.org"
     *      unset
     *
     * @return string
     */
    inlineElementToHTML: function (original, inlineTagName, inlineContent) {
        var matches, text;

        CBMessageMarkup.replacementCount += 1;

        switch (inlineTagName) {
            case "br":
            case "wbr":
                return "<" + inlineTagName + ">";
            case "a":
                matches = inlineContent.match(/([\s\S]*)\shref:\s([\s\S]*)/);

                if (matches) {
                    text = matches[1].trim();
                    var href = matches[2].trim();

                    return '<a href="' + href + '">' + text + '</a>';
                }

                break;
            case "abbr":
                matches = inlineContent.match(/([\s\S]*)\stitle:\s([\s\S]*)/);

                if (matches) {
                    text = matches[1].trim();
                    var title = matches[2].trim();

                    return '<abbr title="' + title + '">' + text + '</abbr>';
                }

                break;
            case "b":
            case "cite":
            case "code":
            case "em":
            case "i":
            case "kbd":
            case "mark":
            case "q":
            case "rp":
            case "rt":
            case "rtc":
            case "ruby":
            case "s":
            case "samp":
            case "small":
            case "span":
            case "strong":
            case "sub":
            case "sup":
            case "time":
            case "u":
            case "var":
                return "<" + inlineTagName + ">" + inlineContent + "</" + inlineTagName + ">";
        }

        return "<span class=\"" + inlineTagName + "\">" + inlineContent + "</span>";
    },

    /**
     * @param string original
     *
     *      "{stong: Extra!}"
     *      "{a Wikipedia href: https://www.wikipedia.org}"
     *      "{br:}"
     *
     * @param string inlineTagName
     *
     *      "strong"
     *      "a"
     *      "br"
     *
     * @param string? inlineContent
     *
     *      "Extra!"
     *      "Wikipedia href: https://www.wikipedia.org"
     *      unset
     *
     * @return string
     */
    inlineElementToText: function (original, inlineTagName, inlineContent) {
        var matches, text;

        CBMessageMarkup.replacementCount += 1;

        switch (inlineTagName) {
            case "br":
            case "wbr":
                return "";
            case "a":
                matches = inlineContent.match(/([\s\S]*)\shref:\s([\s\S]*)/);

                if (matches) {
                    text = matches[1].trim();

                    return text;
                }

                break;
            case "abbr":
                matches = inlineContent.match(/([\s\S]*)\stitle:\s([\s\S]*)/);

                if (matches) {
                    text = matches[1].trim();

                    return text;
                }

                break;
        }

        return inlineContent;
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

    /**
     * @param string markup
     *
     * @return string
     */
    markupToHTML: function (markup) {
        markup = CBMessageMarkup.encodeEscapedCharacters(markup);

        var content, line;
        var lines = markup.split(/\r?\n/);
        var stack = [];
        var root = CBMessageMarkup.createElement(stack);
        root.tagName = "root";
        var current = root;

        for (var index = 0; index < lines.length; index++) {
            line = lines[index];
            var command = CBMessageMarkup.lineToCommand(line);

            if (command) {
                var parentAllows = CBMessageMarkup.tagNameAllowsBlockChildren(current.tagName);
                var childAllowed = CBMessageMarkup.tagNameIsAllowedBlockElement(command.tagName);

                if (parentAllows && childAllowed) {
                    if (content !== undefined) {
                        current.children.push(content);
                        content = undefined;
                    }

                    current = CBMessageMarkup.createElement(stack);
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
                    if (content !== undefined) {
                        current.children.push(content);
                        content = undefined;
                    }

                    current = CBMessageMarkup.elementFinish(stack);
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
                    current.children.push(content);
                    content = undefined;
                }
            }
        }

        if (content) {
            current.children.push(content);
            content = undefined;
        }

        while (current !== undefined) {
            current = CBMessageMarkup.elementFinish(stack);
        }

        return CBMessageMarkup.decodeEncodedCharacters(root.html);
    },

    /**
     * @param string markup
     *
     * @return string
     */
    markupToText: function (markup) {
        markup = CBMessageMarkup.encodeEscapedCharacters(markup);
        var command, line, paragraph;
        var paragraphs = [];
        var lines = markup.split(/\r?\n/);

        for (var index = 0; index < lines.length; index++) {
            line = lines[index];
            command = CBMessageMarkup.lineToCommand(line);

            /**
             * TODO: Add support for recognizing preformatted elements.
             */

            if (command !== false || line.trim() === "") {
                if (paragraph !== undefined) {
                    paragraph = CBMessageMarkup.paragraphToText(paragraph);
                    paragraph = paragraph.trim();
                    paragraph = paragraph.replace(/\s+/g, " ");

                    paragraphs.push(paragraph);

                    paragraph = undefined;
                }
            } else {
                if (paragraph === undefined) {
                    paragraph = "";
                }

                paragraph += line + "\n";
            }
        }

        var text = paragraphs.join("\n\n");
        text = CBMessageMarkup.decodeEncodedCharacters(text);

        return text;
    },

    /**
     * @param string markup
     *
     * @return string
     */
    paragraphToHTML: function (markup) {
        var content = Colby.textToHTML(markup);

        do {
            CBMessageMarkup.replacementCount = 0;
            content = content.replace(/\{([a-z]+):(?:\s+([^\{\}]+))?\}/g, CBMessageMarkup.inlineElementToHTML);
        } while (CBMessageMarkup.replacementCount);

        return content;
    },

    /**
     * @param string markup
     *
     * @return string
     */
    paragraphToText: function (markup) {
        var content = markup;

        do {
            CBMessageMarkup.replacementCount = 0;
            content = content.replace(/\{([a-z]+):(?:\s+([^\{\}]+))?\}/g, CBMessageMarkup.inlineElementToText);
        } while (CBMessageMarkup.replacementCount);

        return content;
    },

    /**
     * @param string $tagName
     *
     * @return bool
     */
    tagNameAllowsBlockChildren: function (tagName) {
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
    },

    /**
     * @param string $tagName
     *
     * @return bool
     */
    tagNameIsAllowedBlockElement: function (tagName) {
        return [
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
        ].indexOf(tagName) !== -1;
    },
};
