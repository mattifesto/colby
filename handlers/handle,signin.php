<?php

try {
    $stateAsJSON = cb_query_string_value(
        'state'
    );

    $state = json_decode(
        $stateAsJSON
    );
} catch (
    Throwable $throwable
) {
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

    $userSignInViewSpec = CBModel::createSpec(
        'CB_CBView_UserSignIn'
    );

    CB_CBView_UserSignIn::setDestinationURL(
        $userSignInViewSpec,
        $destinationURL
    );

    $viewSpecs = [
        $userSignInViewSpec,
        (object)[
            'className' => 'SignInView',
            'destinationURL' => $destinationURL,
        ],
        (object)[
            'className' => 'CBFacebookSignInView',
            'destinationURL' => $destinationURL,
        ],
    ];
}



$pageSpec = CBViewPage::standardPageTemplate();

CBViewPage::setTitle(
    $pageSpec,
    'Sign In'
);

CBViewPage::setViews(
    $pageSpec,
    $viewSpecs
);

CBPage::render(
    CBModel::build(
        $pageSpec
    )
);



final class
SignInView {

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

        $createAccountURL = CBUser::getCreateAccountPageURL(
            $destinationURL
        );

        ?>

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
