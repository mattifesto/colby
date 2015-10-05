<?php

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Front Page');
CBHTMLOutput::setDescriptionHTML('This is the default front page.');

?>

<main>
    <style scoped>

        main > p {
            line-height:    1.5;
            margin:         100px auto;
            text-align:     center;
            width:          540px;
        }

    </style>

    <p>This is the default front page. To replace it create the file<br><br><code>handlers/handle-front-page.php</code><br><br>in the main website directory. Alternatively, create a page in the admin area and set it as the front page. A front page created in the admin area has higher priority than a handler.

</main>

<?php

CBHTMLOutput::render();
