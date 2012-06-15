<?php

Colby::useUser();
ColbyUser::logoutCurrentUser();

$state = json_decode(urldecode($_GET['state']));

header('Location: ' . $state->colby_redirect_uri);
