<?php

/**
 * This file is a snippet for use with the `ColbyOutputManager` class. This
 * snippet will be included by an instance of that class and therefore `$this`
 * refers to the current instance of a `ColbyOutputManager`.
 */

?>

        <?php

        /**
         * Javascript files to include.
         */

        foreach ($this->javaScriptURLs as $javaScriptURL)
        {
            ?>

            <script src="<?php echo $javaScriptURL; ?>"></script>

            <?php
        }

        /**
         * Javascript snippets to add to the page.
         */

        foreach ($this->javaScriptSnippetFilenames as $javaScriptSnippetFilename)
        {
            include $javaScriptSnippetFilename;
        }

        ?>

    </body>
</html>
