<?php

try {
    $stateAsJSON = cb_query_string_value('state');
    $state = json_decode($stateAsJSON);
} catch (Throwable $throwable) {
    $state = (object)[];
}

$destinationURL = trim(
    CBModel::valueToString(
        $state,
        'destinationURL'
    )
);

/**
 * If the destination URL is empty, make the home page the destination.
 */
if ($destinationURL === '') {
    $destinationURL = '/';
}

$cbmessage = '';

$emailAddress = trim(
    cb_post_value(
        'emailAddress',
        ''
    )
);

$password = cb_post_value(
    'password',
    ''
);



/**
 * Try to sign the user in if they are not already signed in and there is an
 * email address available.
 */

if (
    ColbyUser::getCurrentUserCBID() === null &&
    $emailAddress !== ''
) {
    $result = CBUser::signIn(
        $emailAddress,
        $password
    );

    /**
     * If the sign in was successful, redirect the user to the destination URL.
     */

    if (ColbyUser::getCurrentUserCBID() !== null) {
        header(
            "Location: {$destinationURL}",
            true,
            302
        );

        exit();
    }

    /* error message */

    $cbmessage = CBModel::valueToString(
        $result,
        'cbmessage'
    );
}



if (ColbyUser::getCurrentUserCBID() !== null) {

    /**
     * If the user is signed in and came directly to this page show the sign out
     * view.
     */

    $viewSpecs = [
        (object)[
            'className' => 'CBMessageView',
            'markup' => <<<EOT

                --- center
                You are already signed in.
                ---

            EOT,
        ],
        (object)[
            'className' => 'CBSignOutView',
        ],
    ];
} else {

    /**
     * If the user is not logged in display sign in views.
     */

    $viewSpecs = [
        (object)[
            'className' => 'CBMessageView',
            'markup' => $cbmessage
        ],
        (object)[
            'className' => 'SignInView',
            'destinationURL' => $destinationURL,
            'emailAddress' => $emailAddress,
        ],
        (object)[
            'className' => 'CBFacebookSignInView',
            'destinationURL' => $destinationURL,
        ],
    ];
}



$pageSpec = CBModelTemplateCatalog::fetchLivePageTemplate(
    (object)[
        'title' => 'User',
        'sections' => $viewSpecs,
    ]
);

CBPage::render(
    CBModel::build(
        $pageSpec
    )
);



final class SignInView {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
            'CBUIStringEditor',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $spec
    ): stdClass {
        return (object)[
            'destinationURL' => CBModel::valueToString(
                $spec,
                'destinationURL'
            ),
            'emailAddress' => CBModel::valueToString(
                $spec,
                'emailAddress'
            ),
        ];
    }
    /* CBModel_build() */



    /* -- CBView interfaces -- -- -- -- -- */



    /**
     * @param object $viewModel
     *
     * @return void
     */
    static function CBView_render(
        stdClass $viewModel
    ): void {
        $destinationURL = CBModel::valueToString(
            $viewModel,
            'destinationURL'
        );

        $emailAddress = CBModel::valueToString(
            $viewModel,
            'emailAddress'
        );

        $formActionURL = CBUser::getSignInPageURL(
            $destinationURL
        );

        $createAccountURL = CBUser::getCreateAccountPageURL(
            $destinationURL
        );

        ?>

        <form
            method="post"
            action="<?= $formActionURL ?>"
            style="padding-bottom: 44px;"
        >
            <div class="CBUI_sectionContainer">
                <div class="CBUI_section">

                    <div class="CBUIStringEditor">
                        <div class="CBUIStringEditor_container">
                            <label
                                class="CBUIStringEditor_label"
                                for="emailAddress"
                            >
                                Email Address
                            </label>
                            <input
                                class="CBUIStringEditor_input"
                                autocomplete="username"
                                type="email"
                                name="emailAddress"
                                id="emailAddress"
                                value="<?= cbhtml($emailAddress) ?>"
                            />
                        </div>
                    </div>

                    <div class="CBUIStringEditor">
                        <div class="CBUIStringEditor_container">
                            <label
                                class="CBUIStringEditor_label"
                                for="password"
                            >
                                Password
                            </label>
                            <input
                                class="CBUIStringEditor_input"
                                autocomplete="current-password"
                                type="password"
                                name="password"
                                id="password"
                            />
                        </div>
                    </div>

                </div>
            </div>

            <div class="CBUI_container1">
                <input
                    class="CBUI_button1"
                    type="submit"
                    value="Sign In"
                />
            </div>
        </form>

        <div class="CBUI_sectionContainer">
            <div class="CBUI_section">
                <a
                    class="CBUI_action"
                    href="<?= cbhtml($createAccountURL) ?>"
                >Create New Account &gt;</a>

                <a
                    class="CBUI_action"
                    href="/colby/user/reset-password/"
                >Reset Password &gt;</a>
            </div>
        </div>

        <?php
    }
    /* CBView_render() */

}
