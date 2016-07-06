<?php

$currentUserIsLoggedIn  = ColbyUser::current()->isLoggedIn();
$titleHTML              = $currentUserIsLoggedIn ? 'Authorization Failed' : 'Please Log In';

CBHTMLOutput::begin();
CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
CBHTMLOutput::setTitleHTML($titleHTML);
CBHTMLOutput::setDescriptionHTML('You are not authorized to view this page.');

?>

<main>
    <style>

        main {
            padding-top:  100px;
            text-align: center;
        }

        a.CBLogInLink {
            background-color:   hsl(210, 100%, 70%);
            box-shadow:
                0 0 0 1px white,
                0 0 0 5px hsl(210, 100%, 90%);
            color:              hsla(0, 0%, 20%, 0.5);
            display:            inline-block;
            font-size:          20px;
            font-weight:        bold;
            letter-spacing:     1px;
            margin-top:         50px;
            padding:            15px 50px;
            text-decoration:    none;
            transition:         box-shadow 0.15s;
        }

        a.CBLogInLink:hover {
            box-shadow:
                0 0 0 2px white,
                0 0 0 6px hsl(210, 100%, 70%);
        }

    </style>

    <?php

    if ($currentUserIsLoggedIn) {
        ?>

        <p>You are not authorized to view this page.

        <?php
    } else {
        ?>

        <p>You must be logged in to view this page.

        <div>
            <a class="CBLogInLink" href="<?php echo ColbyUser::loginURL(); ?>">log in</a>
        </div>

        <?php
    }

    ?>

</main>

<?php

include CBSystemDirectory . '/sections/admin-page-footer-2.php';

CBHTMLOutput::render();
