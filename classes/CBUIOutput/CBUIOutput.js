"use strict";
/* jshint strict: global */
/* jshint esversion: 6 */
/* exported CBUIOutput */

var CBUIOutput = {

    /**
     * @return object
     *
     *      {
     *          append: function
     *          clear: function
     *          element: Element
     *      }
     */
    create: function () {
        var animationFrameID, motionDeltas, currentMotionFrameIndex;
        var currentContainerElementBottom;
        const targetContainerElementBottom = 0;
        var currentContainerElementHeight = 0;
        var itemElementHeight = 44;

        var element = document.createElement("div");
        element.className = "CBUIOutput CBDarkTheme";
        var containerElement = document.createElement("div");
        containerElement.className = "container";

        element.appendChild(containerElement);

        return {
            append: append,
            clear: clear,
            element: element,
        };

        /* closure */
        function append(text) {
            var firstLine = text.split(/\r?\n/, 1)[0];
            var itemElement = document.createElement("div");
            itemElement.className = "item";
            itemElement.style.height = itemElementHeight + "px";
            itemElement.textContent = firstLine;

            containerElement.appendChild(itemElement);

            currentContainerElementHeight += itemElementHeight;

            if (currentContainerElementHeight <= element.offsetHeight) {

                /**
                 * The container of items is shorter than the CBUIOutput
                 * element, so the container is attached to the top.
                 */

                currentContainerElementBottom = undefined;
                containerElement.style.bottom = "";
                containerElement.style.top = "0";
            } else {

                /**
                 * The container of items is taller than the CBUIOutput element.
                 */


                if (currentContainerElementBottom === undefined) {
                    currentContainerElementBottom = element.offsetHeight - currentContainerElementHeight;
                } else {
                    currentContainerElementBottom -= itemElementHeight;
                }

                containerElement.style.top = "";
                containerElement.style.bottom = currentContainerElementBottom + "px";

                /**
                 * targetContainerElementBottom is always 0
                 */

                var currentVelocity = 0;

                if (currentMotionFrameIndex !== undefined) {
                    currentVelocity = motionDeltas[currentMotionFrameIndex];
                }

                motionDeltas = CBUIOutput.deltas(targetContainerElementBottom - currentContainerElementBottom, currentVelocity, 60, 30);
                currentMotionFrameIndex = 0;

                if (animationFrameID === undefined) {
                    animationFrameID = window.requestAnimationFrame(renderFrame);
                }
            }

            /* closure */
            function renderFrame() {
                if (currentMotionFrameIndex < motionDeltas.length) {
                    currentContainerElementBottom = currentContainerElementBottom + motionDeltas[currentMotionFrameIndex];
                    currentMotionFrameIndex += 1;
                    animationFrameID = window.requestAnimationFrame(renderFrame);
                } else {
                    currentContainerElementBottom = targetContainerElementBottom;
                    currentMotionFrameIndex = undefined;
                    animationFrameID = undefined;
                }

                containerElement.style.bottom = currentContainerElementBottom + "px";
            }
        }

        /* closure */
        function clear() {
            currentContainerElementBottom = undefined;
            currentContainerElementHeight = 0;
            containerElement.textContent = undefined;
            containerElement.style.bottom = "";
            containerElement.style.top = "0";
        }
    },

    /**
     * @NOTE This function tries to simplify animation to something resembling
     * real-world motion. Instead of trying to make things happen in a fixed
     * number of frames, it approximates and accelerates like a real-world
     * object. In the real world, objects don't move to fit a total desired
     * duration. They react to physics and duration is an output, not an input.
     * When you drive, you accelerate and decelerate and adjust your velocity to
     * have an approximate duration which you never meet exactly.
     *
     * People often use complex math like beziers to do animation. I think this
     * is the wrong idea. Treat objects like real-world objects and let them
     * move how they will.
     *
     * @return [float]
     */
    deltas: function (distance, initialVelocity, approximateFrameCount, accelerationFrameCount) {
        var multiplier = (distance > 0) ? 1.0 : -1.0;
        distance = distance * multiplier;
        var currentVelocity = 0;
        var linearVelocityAsUnitsPerFrame = distance / approximateFrameCount;

        var accelerationPerFrame = linearVelocityAsUnitsPerFrame / accelerationFrameCount;

        // acceleration

        var accelerationDistance = 0;
        var accelerationDeltas = [];
        currentVelocity = initialVelocity;

        while (currentVelocity < linearVelocityAsUnitsPerFrame) {
            currentVelocity = currentVelocity + accelerationPerFrame;
            accelerationDistance += currentVelocity;

            accelerationDeltas.push(currentVelocity * multiplier);
        }

        // deceleration

        var decelerationDistance = 0;
        var decelerationDeltas = [];
        currentVelocity = 0;

        while (currentVelocity < linearVelocityAsUnitsPerFrame) {
            currentVelocity = currentVelocity + accelerationPerFrame;
            decelerationDistance += currentVelocity;

            decelerationDeltas.push(currentVelocity * multiplier);
        }

        decelerationDeltas.reverse();

        // constant velocity movement

        var linearDistance = distance - (accelerationDistance * 2);
        var linearFrameCount = Math.floor(linearDistance / linearVelocityAsUnitsPerFrame);
        var linearVelocity = linearDistance / linearFrameCount;
        var linearDeltas = [];

        for (var i = 0; i < linearFrameCount; i++) {
            linearDeltas.push(linearVelocity * multiplier);
        }

        var deltas = accelerationDeltas.concat(linearDeltas, decelerationDeltas);

        return deltas;
    },
};
