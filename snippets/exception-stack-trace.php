<?php

// If a php block is at the end of a line it appears that a newline is not added to the HTML which is why there are manual newlines added below in that case. With the pre element, we care about newlines.

?>

<pre style="overflow-y: auto; margin: 20px;">
Exception: <?php echo get_class($e), "\n"; ?>
Message: <?php echo $e->getMessage(), "\n"; ?>

## <?php echo $e->getFile(), '(', $e->getLine(), ')', "\n"; ?>
<?php echo $e->getTraceAsString(); ?></pre>
