<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    include Colby::findHandler('handle-authorization-failed-ajax.php');

    exit;
}

include Colby::findFileForDocumentType('update.php', $_POST['document-group-id'], $_POST['document-type-id']);
