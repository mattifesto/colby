"use strict";

var ColbySheet =
{
    'canAnimate' : false,
    'frame' : 0
};

ColbySheet.animateIn = function()
{
    var durationInFrames = 30;
    var progress = ColbySheet.frame / durationInFrames;

    var background = document.getElementById('sheet-background');
    var panel = document.getElementById('sheet-panel');

    if (ColbySheet.frame <= durationInFrames)
    {
        background.style.opacity = progress;

        var top = - panel.offsetHeight + (panel.offsetHeight * progress);
        panel.style.webkitTransform = 'translateY(' + top + 'px)';

        ColbySheet.requestAnimationFrame(ColbySheet.animateIn);
    }
    else
    {
        panel.style.webkitTransform = 'none';
    }

    ColbySheet.frame++;
}

ColbySheet.animateOut = function()
{
    var durationInFrames = 30;
    var progress = ColbySheet.frame / durationInFrames;

    var background = document.getElementById('sheet-background');
    var panel = document.getElementById('sheet-panel');

    if (ColbySheet.frame <= durationInFrames)
    {
        var opacity = Math.max(1 - progress, 0);
        background.style.opacity = opacity;

        var top = - panel.offsetHeight + (panel.offsetHeight * (1.0 - progress));
        panel.style.webkitTransform = 'translateY(' + top + 'px)';

        ColbySheet.requestAnimationFrame(ColbySheet.animateOut);
    }
    else
    {
        background.style.visibility = 'hidden';
        panel.style.visibility = 'hidden';
        panel.style.webkitTransform = 'none';
    }

    ColbySheet.frame++;
}

ColbySheet.beginSheet = function(contentHTML)
{
    ColbySheet.createElements();

    var background = document.getElementById('sheet-background');
    background.style.visibility = 'visible';

    var panel = document.getElementById('sheet-panel');
    panel.style.visibility = 'visible';
    panel.innerHTML = contentHTML;

    ColbySheet.frame = 0;

    if (ColbySheet.canAnimate)
    {
        ColbySheet.animateIn();
    }
}

ColbySheet.endSheet = function()
{
    ColbySheet.frame = 0;

    if (ColbySheet.canAnimate)
    {
        ColbySheet.animateOut();
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

ColbySheet.createElements = function()
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
