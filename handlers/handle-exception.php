<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Exception</title>
        <style>
            pre
            {
                overflow-y: auto;
                padding: 20px;
                margin: 20px;
                background-color: white;
                color: #333;
            }
        </style>
    </head>
    <body>

        <?php

        /**
         * Stack traces can be a security risk so they should only be
         * displayed if the site is being debugged.
         */

        if (Colby::siteIsBeingDebugged()) {

            echo '<pre>';

            $stackTrace = Colby::exceptionStackTrace($exception);

            /**
             * 2013.09.07
             *  The code below used to call the method
             *  `ColbyConvert::textToHTML`. The problem was that exceptions
             *  can be triggered before the `ColbyConvert` class is included
             *  and if that were to happen execution would stop and an error
             *  wouldn't even be logged.
             *
             *  Because of the potential for unlogged errors, exception
             *  handling code should avoid calling external functions. When
             *  an external function is necessary, it should be verified as
             *  being "exception safe."
             *
             *  The code below is and should remain an exact duplicate of what
             *  `ColbyConvert::textToHTML` does.
             */

            echo htmlspecialchars($stackTrace, ENT_QUOTES);

            echo '</pre>';
        }

        ?>

    </body>
</html>
