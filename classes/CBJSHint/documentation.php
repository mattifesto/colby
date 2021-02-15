<?php

$jshintrcFileContents = file_get_contents(
    __DIR__ . '/jshintrc'
);

$cbmessage = <<<EOT

    This class currently has no functionality but to provide developers with an
    example of the appropriate .jshintrc file to place in their user home
    directory so that they get the correct response when running jshint.

    Copy this to your .jshintrc file:

    --- pre\n{$jshintrcFileContents}
    ---

EOT;

$messageViewSpec = (object)[];

CBModel::setClassName(
    $messageViewSpec,
    'CBMessageView'
);

CBMessageView::setCBMessage(
    $messageViewSpec,
    $cbmessage
);

CBView::renderSpec(
    $messageViewSpec
);
