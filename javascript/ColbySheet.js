"use strict";

var ColbySheet =
{
    'canAnimate' : false
};

var frame = 0;

function animateIn()
{
    var durationInFrames = 30;
    var progress = frame / durationInFrames;

    var background = document.getElementById('sheet-background');
    var panel = document.getElementById('sheet-panel');

    if (frame <= durationInFrames)
    {
        background.style.opacity = progress;

        var top = - panel.offsetHeight + (panel.offsetHeight * progress);
        panel.style.webkitTransform = 'translateY(' + top + 'px)';

        ColbySheet.requestAnimationFrame(animateIn);
    }
    else
    {
        panel.style.webkitTransform = 'none';
    }

    frame++;
}

function animateOut()
{
    var durationInFrames = 30;
    var progress = frame / durationInFrames;

    var background = document.getElementById('sheet-background');
    var panel = document.getElementById('sheet-panel');

    if (frame <= durationInFrames)
    {
        var opacity = Math.max(1 - progress, 0);
        background.style.opacity = opacity;

        var top = - panel.offsetHeight + (panel.offsetHeight * (1.0 - progress));
        panel.style.webkitTransform = 'translateY(' + top + 'px)';

        ColbySheet.requestAnimationFrame(animateOut);
    }
    else
    {
        background.style.visibility = 'hidden';
        panel.style.visibility = 'hidden';
        panel.style.webkitTransform = 'none';
    }

    frame++;
}

function go()
{
    var html = ' \
<div class="my-panel"> \
<div style="height: 200px;">There once was a man from Nantucket</div> \
<div style="text-align: right;"><button onclick="endSheet();">Dismiss</button></div> \
</div>';

    beginSheet(html);
}

function beginSheet(contentHTML)
{
    createElements();

    var background = document.getElementById('sheet-background');
    background.style.visibility = 'visible';

    var panel = document.getElementById('sheet-panel');
    panel.style.visibility = 'visible';
    panel.innerHTML = contentHTML;

    frame = 0;

    if (ColbySheet.canAnimate)
    {
        animateIn();
    }
}

function endSheet()
{
    frame = 0;

    if (ColbySheet.canAnimate)
    {
        animateOut();
    }
    else
    {
        var background = document.getElementById('sheet-background');
        background.style.visibility = 'hidden';

        var panel = document.getElementById('sheet-panel');
        panel.style.visibility = 'hidden';
        panel.style.webkitTransform = 'none';
    }
}

function createElements()
{
    if (window.webkitRequestAnimationFrame)
    {
        ColbySheet.canAnimate = true;
        ColbySheet.backgroundColor = 'rgba(0, 0, 0, 0.5)';
    }
    else
    {
        ColbySheet.backgroundColor = 'black';
    }

    if (!document.getElementById('sheet-background'))
    {
        var root = document.createElement('div');

        root.id = 'sheet-background';
        root.style.visibility = 'hidden';
        root.style.position = 'fixed';
        root.style.top = '0px';
        root.style.left = '0px';
        root.style.width = '100%';
        root.style.height = '100%';
        root.style.backgroundColor = ColbySheet.backgroundColor;
        root.style.opacity = '0.5';

        document.body.appendChild(root);
    }

    if (!document.getElementById('sheet-panel'))
    {
        var panel = document.createElement('div');

        panel.id = 'sheet-panel';
        panel.style.visibility = 'hidden';
        panel.style.position = 'fixed';
        panel.style.left = '0px';
        panel.style.top = '0px';
        panel.style.width = '100%';

        document.body.appendChild(panel);
    }
}

ColbySheet.requestAnimationFrame = function(callback)
{
    if (window.requestAnimationFrame)
    {
        window.requestAnimationFrame(callback);
    }
    else if (window.webkitRequestAnimationFrame)
    {
        window.webkitRequestAnimationFrame(callback);
    }
    else if (window.mozRequestAnimationFrame)
    {
        window.mozRequestAnimationFrame(callback);
    }
};
