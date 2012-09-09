<?php

// PHP removes one newline after a PHP closing tag from the output
// which is especially noticable inside of a <pre> element
// this is why there appear to be more newlines than necessary
//
// the following:
//
//    echo 'the'; endphp>-newline-
//   -newline-
//   content
//
// will produce the output
//
//   the
//   content
//
// http://brian.moonspot.net/php-history-newline-closing-tag
//
// updated: 2012.04.11

?>
Exception Type: <?php

    echo get_class($exception);

?>

Message: <?php

    echo $exception->getMessage();

?>


## <?php

    echo $exception->getFile(),
    '(',
    $exception->getLine(),
    ')';
?>

<?php

    echo $exception->getTraceAsString();
