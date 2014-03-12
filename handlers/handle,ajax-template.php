<?php

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';


if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

CBHTMLOutput::begin();
CBHTMLOutput::setTitleHTML('Ajax Template');
CBHTMLOutput::setDescriptionHTML('A simple Ajax template.');

include CBSystemDirectory . '/sections/equalize.php';

CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/Colby.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/handlers/handle,ajax-template.js');

?>

<main>
</main>

<?php

CBHTMLOutput::render();
