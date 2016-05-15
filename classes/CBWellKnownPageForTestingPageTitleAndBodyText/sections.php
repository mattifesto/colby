<?php

$spec->sections = [];

$section = new stdClass();
$section->className = 'CBPageTitleAndDescriptionView';
$section->showPublicationDate = true;

$spec->sections[] = $section;

$section = new stdClass();
$section->className = 'CBThemedTextView';
$section->titleAsMarkaround = 'Introduction';
$section->contentAsMarkaround = <<<EOT

This page is automatically created by the installation and upgrade process. It
is a page used to test the styles applied to common elements. Feel free to modify or
even delete this page, however all changes will be reset  the next time the site
is upgraded.

For this reason, this is a great page to experiment with. However, it’s probably
best not to publish the page on a production site.

EOT;

$spec->sections[] = $section;

$section = new stdClass();
$section->className = 'CBThemedTextView';
$section->titleAsMarkaround = 'Themed Text View Title';
$section->contentAsMarkaround = <<<EOT

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Du_is eu sem a magna
hendrerit orna_re. Proin rutrum, lacus sit amet facilisis ornare, velit purus
porta nunc, ac vestibulum urna dui ac eros. I_nteger vitae venenatis lore_m. Nulla
a purus vel velit rutrum dapibus. Suspendisse rutrum vitae dui ut placerat.
Suspendisse potenti. Quisque rhoncus fermentum suscipit. Aenean vulputate libero
vel lectus venenatis varius. _Duis tincidunt odio in leo viverra_, nec ullamcorper
turpis consectetur.

# Content First Level Heading

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

} Mauris ligula risus
] lacinia luctus nulla a, dignissim mollis arcu.
} Quisque ut elementum ipsum
] vel euismod tortor.
} Aenean
] non rutrum lorem.

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

Test of *bold containing _italics_*.

Test of *bold containing {a citation}*.

Test of _italics containing *bold*_.

Test of _italics containing {a citation}_.

Test of {a citation containing *bold*}.

Test of {a citation containing _italics_}.

EOT;

$spec->sections[] = $section;

$section = new stdClass();
$section->className = 'CBThemedTextView';
$section->center = true;
$section->contentAsMarkaround = 'View the Mattifesto Home Page (inherited color) >';
$section->URL = 'https://mattifesto.com/';

$spec->sections[] = $section;

$section = new stdClass();
$section->className = 'CBThemedTextView';
$section->center = true;
$section->contentAsMarkaround = 'View the Mattifesto Home Page (specified color) >';
$section->contentColor = 'hsl(210, 100%, 50%)';
$section->URL = 'https://mattifesto.com/';

$spec->sections[] = $section;

$section = (object) [
    'className' => 'CBThemedTextView',
    'titleAsMarkaround' => 'The Mattifesto Website',
    'URL' => 'https://mattifesto.com/',
];

$section->contentAsMarkaround = <<<EOT

Curabitur in metus dictum, placerat nisi a, rhoncus purus. Duis mi odio,
scelerisque sit amet nunc ut, euismod dictum mi. Lorem ipsum dolor sit amet,
consectetur adipiscing elit. Phasellus consequat ornare lacus et auctor. Duis ut
laoreet turpis, eu porttitor elit. Cras congue turpis elit, at vehicula massa
tincidunt quis. Aenean vel vulputate ligula.

Interdum et malesuada fames ac ante ipsum primis in faucibus. Etiam et vulputate
diam. Maecenas facilisis magna ut bibendum lobortis. Donec pretium, sem sed
laoreet aliquet, ex urna dictum risus, et ornare nisi lorem a massa. In a sem
vitae nulla commodo rhoncus. Ut ac augue at ante mollis efficitur. Aliquam
auctor mi orci, sed semper purus auctor quis.

EOT;

$spec->sections[] = $section;
