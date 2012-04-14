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

<style scoped="scoped">
    pre
    {
        overflow-y: auto;
        padding: 20px;
        margin: 20px;
        background-color: white;
        color: #333;
    }
</style>
<pre>
Exception: <?php

    echo htmlspecialchars(get_class($e), ENT_QUOTES);

?>

Message: <?php

    echo htmlspecialchars($e->getMessage(), ENT_QUOTES);

?>


## <?php

    echo htmlspecialchars($e->getFile(), ENT_QUOTES),
    '(',
    htmlspecialchars($e->getLine(), ENT_QUOTES),
    ')';
?>

<?php

    echo htmlspecialchars($e->getTraceAsString(), ENT_QUOTES);

?></pre>
