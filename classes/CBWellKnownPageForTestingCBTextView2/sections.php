<?php

$spec->sections = [];

$spec->sections[] = (object)[
    'className' => 'CBPageTitleAndDescriptionView',
    'showPublicationDate' => true,
];

$spec->sections[] = (object)[
    'className' => 'CBTextView2',
    'contentAsCommonMark' => <<<EOT
# Introduction

This page is automatically created by the installation and upgrade process. It
is a page used to test the styles applied to common elements. Feel free to modify or
even delete this page, however all changes will be reset  the next time the site
is upgraded.

For this reason, this is a great page to experiment with. However, it’s probably
best not to publish the page on a production site.

# Code

Inline `code` elements will be formatted like `this.`

```
Preformatted code blocks
will be formatted like this.

10 FOR X=1 TO 5
20 PRINT "Hello, world!"
30 NEXT X
```

EOT
];

$spec->sections[] = (object)[
    'className' => 'CBTextView2',
    'CSSClassNames' => 'CBDarkTheme',
    'contentAsCommonMark' => <<<EOT
# Formatting
## Unordered List

Maecenas blandit velit ac molestie porttitor.

- Pellentesque tincidunt gravida tellus ut suscipit.
- Donec pharetra nisl sit amet ante gravida interdum.
- Praesent vel magna sed urna blandit euismod.
- Interdum et malesuada fames ac ante ipsum primis in faucibus.

Duis ut accumsan tellus.

## Ordered List

1. Cras suscipit semper felis, non condimentum eros maximus posuere.
1. Pellentesque at ipsum vitae ligula placerat dictum at ac turpis.
45. Nam gravida sit amet ante iaculis sollicitudin.

Ut blandit molestie posuere.

## Description List

Praesent nec consectetur orci, ultrices sagittis nisl.

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

Donec pharetra nisl sit amet ante gravida interdum.

> Aenean pulvinar mi eget dui suscipit eleifend. Aenean vel erat nec mi varius
> faucibus. Nulla vitae lacus vitae leo convallis condimentum. Nam egestas elit
> vitae cursus venenatis. Nunc lacinia est eget dolor rhoncus, sit amet
> fringilla purus mollis.

## Third Level Headings
### Quisque varius

Quisque varius dignissim diam, ut convallis ipsum lacinia sed. Maecenas volutpat
malesuada venenatis.

### Cras at rutrum

Cras at rutrum tellus. Mauris venenatis tristique ornare. Morbi eleifend, ante
dictum placerat volutpat, leo dui scelerisque nunc, id fringilla nisi metus sit
amet nibh.

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
    'CSSClassNames' => 'hero1 center CBDarkTheme',
    'localCSSTemplate' => <<<EOT
view {
    align-items: center;
    background-color: hsl(0, 70%, 25%);
    min-height: 100vh;
}
EOT
    ,
    'contentAsCommonMark' => <<<EOT
# Hero
## Superman
#### Clark Griswold Kent
Faster than a speeding bullet!
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
    'CSSClassNames' => 'CBDarkTheme',
    'contentAsCommonMark' => '# Hyperlinks',
];

$spec->sections[] = (object)[
    'className' => 'CBTextView2',
    'CSSClassNames' => 'center CBDarkTheme',
    'contentAsCommonMark' => <<<EOT
[View the Mattifesto Home Page (inherited color) >](https://mattifesto.com/)
EOT
];

$spec->sections[] = (object)[
    'className' => 'CBTextView2',
    'CSSClassNames' => 'center CBDarkTheme',
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
view > * {
    width: 50%;
    max-width: 480px;
}

@media (max-width: 639px) {
    view > * {
        width: 100%;
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
Left side.

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
Right side.

Side <span class="special">by</span> side is accomplished by using
a CBContainerView to contain two text views and then setting up the styles
template with appropriate styles. Look at this page as an example and ask for
help because some of the technology is complex.
EOT
];

$spec->sections[] = $container;
