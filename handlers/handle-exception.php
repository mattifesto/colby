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
        <pre><?php echo ColbyConvert::textToHTML(Colby::exceptionStackTrace($exception)); ?></pre>
    </body>
</html>
