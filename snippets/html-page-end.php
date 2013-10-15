<?php

/**
 * This file is a snippet for use with the `ColbyOutputManager` class. This
 * snippet will be included by an instance of that class and therefore `$this`
 * refers to the current instance of a `ColbyOutputManager`.
 */

foreach ($this->javaScriptFilenames as $javaScriptFilename)
{
    ?>

    <script src="<?php echo $javaScriptFilename; ?>"></script>

    <?php
}

?>

    </body>
</html>
