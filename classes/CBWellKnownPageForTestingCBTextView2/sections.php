<?php

$spec->sections = [];

$spec->sections[] = (object)[
    'className' => 'CBPageTitleAndDescriptionView',
    'showPublicationDate' => true,
];

$spec->sections[] = (object)[
    'className' => 'CBTextView2',
    'CSSClassNames' => 'CBTextView2StandardLayout',
    'contentAsCommonMark' => <<<EOT
# Introduction

This page is automatically created by the installation and upgrade process. It
is a page used to test the styles applied to common elements. Feel free to modify or
even delete this page, however all changes will be reset  the next time the site
is upgraded.

For this reason, this is a great page to experiment with. However, it’s probably
best not to publish the page on a production site.
EOT
];

$spec->sections[] = (object)[
    'className' => 'CBTextView2',
    'CSSClassNames' => 'CBTextView2StandardLayout',
    'contentAsCommonMark' => <<<EOT
# First Level Heading

Duis dapibus, nibh quis efficitur fringilla, nulla felis sodales enim, in
sodales nibh neque eu lacus. Nulla nec arcu eu urna tristique sagittis. Morbi
libero libero, imperdiet bibendum orci vel, convallis ultrices sapien.
Vestibulum est est, vehicula sit amet mauris at, suscipit euismod mi. Fusce
elementum pulvinar ligula eu sollicitudin.

## Unordered List

Maecenas blandit velit ac molestie porttitor. Praesent posuere, purus ac
tincidunt feugiat, sapien tellus pharetra odio, quis ultricies arcu quam vitae
odio. In mauris augue, scelerisque at lobortis in, laoreet eu lorem. Suspendisse
porttitor at risus nec viverra. Praesent consectetur mattis dolor, vitae cursus
tellus fermentum et. Fusce risus massa, tincidunt ac feugiat et, vulputate a
neque.

- Pellentesque tincidunt gravida tellus ut suscipit.
- Donec pharetra nisl sit amet ante gravida interdum.
- Praesent vel magna sed urna blandit euismod.
- Interdum et malesuada fames ac ante ipsum primis in faucibus.

Duis ut accumsan tellus. Aenean pulvinar mi eget dui suscipit eleifend. Aenean
vel erat nec mi varius faucibus. Nulla vitae lacus vitae leo convallis
condimentum. Nam egestas elit vitae cursus venenatis. Nunc lacinia est eget
dolor rhoncus, sit amet fringilla purus mollis.

## Ordered List

Mauris quis metus a felis cursus maximus. Fusce dignissim, ex et ultrices
tempor, magna velit aliquam purus, a aliquet eros augue ut neque. Quisque
laoreet velit nec augue pretium, a tempor mauris pellentesque. Morbi semper
eleifend tellus, non tincidunt elit aliquam quis.

1. Cras suscipit semper felis, non condimentum eros maximus posuere.
1. Pellentesque at ipsum vitae ligula placerat dictum at ac turpis.
45. Nam gravida sit amet ante iaculis sollicitudin.

Ut blandit molestie posuere. Nam sem nunc, tincidunt eu erat a, tincidunt
vestibulum ante. Morbi sit amet porta dui. Proin vel dapibus massa, id
sollicitudin ex. Suspendisse id consequat lectus, vitae ornare lectus. Praesent
ultricies scelerisque condimentum. Pellentesque dapibus lacinia luctus.
Pellentesque pretium posuere felis suscipit suscipit.

## Description List

Praesent nec consectetur orci, ultrices sagittis nisl. Morbi vitae risus nec est
tincidunt commodo sit amet in ipsum. Quisque quis massa eros. Phasellus at arcu
consectetur, accumsan leo vitae, maximus elit.

<dl>
    <dt>Mauris ligula risus</dt>
    <dd>lacinia luctus nulla a, dignissim mollis arcu.</dd>
    <dt>Quisque ut elementum ipsum</dt>
    <dd>vel euismod tortor.</dd>
    <dt>Aenean</dt>
    <dd>non rutrum lorem.</dd>
</dl>

Ut aliquam est et vulputate faucibus, magna quam dignissim erat, vel imperdiet
nisi felis at ex.

## Block Quote

Donec pharetra nisl sit amet ante gravida interdum. Praesent vel magna sed urna
blandit euismod. Interdum et malesuada fames ac ante ipsum primis in faucibus.
Duis ut accumsan tellus.

> Aenean pulvinar mi eget dui suscipit eleifend. Aenean vel erat nec mi varius
> faucibus. Nulla vitae lacus vitae leo convallis condimentum. Nam egestas elit
> vitae cursus venenatis. Nunc lacinia est eget dolor rhoncus, sit amet
> fringilla purus mollis.

## Third Level Headings

Vivamus cursus, magna et maximus pulvinar, mauris massa ullamcorper nibh, sed
mattis libero felis eu nisl. Nulla laoreet maximus lobortis.

### Quisque varius

Quisque varius dignissim diam, ut convallis ipsum lacinia sed. Maecenas volutpat
malesuada venenatis.

### Cras at rutrum

Cras at rutrum tellus. Mauris venenatis tristique ornare. Morbi eleifend, ante
dictum placerat volutpat, leo dui scelerisque nunc, id fringilla nisi metus sit
amet nibh.

### Donec bibendum

Donec bibendum, neque lacinia varius placerat, nibh quam eleifend nisi, id
eleifend eros augue at ipsum. Integer gravida imperdiet volutpat.

## Inline formatting

Test of **bold containing _italics_**.

Test of **bold containing <cite>a citation</cite>**.

Test of _italics containing **bold**_.

Test of _italics containing <cite>a citation</cite>_.

Test of <cite>a citation containing **bold**</cite>.

Test of <cite>a citation containing _italics_</cite>.

EOT
];

$spec->sections[] = (object)[
    'className' => 'CBTextView2',
    'contentAsCommonMark' => '# CSSClassNames modifiers',
];

$spec->sections[] = (object)[
    'className' => 'CBTextView2',
    'CSSClassNames' => 'center',
    'contentAsCommonMark' => <<<EOT
## "center"

Adding the word "center" to the list of CSSClassNames will center the text of
your content.

Cras at rutrum tellus. Mauris venenatis tristique ornare. Morbi eleifend, ante
dictum placerat volutpat, leo dui scelerisque nunc, id fringilla nisi metus sit
amet nibh. Donec bibendum, neque lacinia varius placerat, nibh quam eleifend nisi, id
eleifend eros augue at ipsum. Integer gravida imperdiet volutpat.
EOT
];

$spec->sections[] = (object)[
    'className' => 'CBTextView2',
    'CSSClassNames' => 'justify',
    'contentAsCommonMark' => <<<EOT
## "justify"

Adding the word "justify" to the list of CSSClassNames will justify the text of
your content.

Cras at rutrum tellus. Mauris venenatis tristique ornare. Morbi eleifend, ante
dictum placerat volutpat, leo dui scelerisque nunc, id fringilla nisi metus sit
amet nibh. Donec bibendum, neque lacinia varius placerat, nibh quam eleifend nisi, id
eleifend eros augue at ipsum. Integer gravida imperdiet volutpat.
EOT
];

$spec->sections[] = (object)[
    'className' => 'CBTextView2',
    'CSSClassNames' => 'right',
    'contentAsCommonMark' => <<<EOT
## "right"

Adding the word "right" to the list of CSSClassNames will right align the text
of your content.

Cras at rutrum tellus. Mauris venenatis tristique ornare. Morbi eleifend, ante
dictum placerat volutpat, leo dui scelerisque nunc, id fringilla nisi metus sit
amet nibh. Donec bibendum, neque lacinia varius placerat, nibh quam eleifend nisi, id
eleifend eros augue at ipsum. Integer gravida imperdiet volutpat.
EOT
];

$spec->sections[] = (object)[
    'className' => 'CBTextView2',
    'CSSClassNames' => 'light',
    'localCSSTemplate' => 'view {background-color: hsl(30, 30%, 20%)}',
    'contentAsCommonMark' => <<<EOT
## "light"

The default text color is set by the site to be compatible with the default
background color. The the site may change the defaults at any time. This means
that if you set a custom background color you should specify whether the text
appropriate for that background color is light or dark. If the site defaults
change your view will still appear as you intended it to.

Adding the word "light" to the list of CSSClassNames makes the text light
colored for use on dark custom backgrounds.

EOT
];

$spec->sections[] = (object)[
    'className' => 'CBTextView2',
    'CSSClassNames' => 'dark',
    'localCSSTemplate' => 'view {background-color: hsl(30, 30%, 80%)}',
    'contentAsCommonMark' => <<<EOT
## "dark"

Adding the word "dark" to the list of CSSClassNames makes the text dark
colored for use on light custom backgrounds.

Cras at rutrum tellus. Mauris venenatis tristique ornare. Morbi eleifend, ante
dictum placerat volutpat, leo dui scelerisque nunc, id fringilla nisi metus sit
amet nibh. Donec bibendum, neque lacinia varius placerat, nibh quam eleifend nisi, id
eleifend eros augue at ipsum. Integer gravida imperdiet volutpat.
EOT
];

$spec->sections[] = (object)[
    'className' => 'CBTextView2',
    'contentAsCommonMark' => '# Hyperlinks',
];

$spec->sections[] = (object)[
    'className' => 'CBTextView2',
    'CSSClassNames' => 'center',
    'contentAsCommonMark' => <<<EOT
[View the Mattifesto Home Page (inherited color) >](https://mattifesto.com/)
EOT
];

$spec->sections[] = (object)[
    'className' => 'CBTextView2',
    'CSSClassNames' => 'center',
    'localCSSTemplate' => 'view a {color: orange}',
    'contentAsCommonMark' => <<<EOT
[View the Mattifesto Home Page (local CSS color, orange) >](https://mattifesto.com/)
EOT
];

$spec->sections[] = (object)[
    'className' => 'CBTextView2',
    'contentAsCommonMark' => '# Side by Side',
];

$container = (object)[
    'className' => 'CBContainerView',
    'CSSClassNames' => 'flow',
    'stylesTemplate' => <<<EOT
view {
    margin-bottom: 100px;
}

view > * {
    width: 480px;
    max-width: 50% !important;
}

@media (max-width 639px) {
    view > * {
        max-width: 100% !important;
    }
}
EOT
];

$container->subviews[] = (object)[
    'className' => 'CBTextView2',
    'CSSClassNames' => 'CBLightTheme',
    'localCSSTemplate' => <<<EOT
view {
    background-color: hsl(30, 30%, 80%);
}

view .special {
    color: blue;
}
EOT
    ,
    'contentAsCommonMark' => <<<EOT
Small text.

Side <span class="special">by</span> side!
EOT
];

$container->subviews[] = (object)[
    'className' => 'CBTextView2',
    'CSSClassNames' => 'CBDarkTheme',
    'localCSSTemplate' => <<<EOT
view {
    background-color: hsl(30, 30%, 20%);
}

view .special {
    color: red;
}
EOT
    ,
    'contentAsCommonMark' => <<<EOT
Large text.

Side <span class="special">by</span> side is accomplished by using
a CBContainerView to contain two text views and then setting up the styles
template with appropriate styles. Look at this page as an example and ask for
help because some of the technology is complex.
EOT
];

$spec->sections[] = $container;
