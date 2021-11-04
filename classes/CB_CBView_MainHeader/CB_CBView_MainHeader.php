<?php

final class
CB_CBView_MainHeader {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ) {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.38.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CB_UI',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBModel interfaces -- */



    /**
     * @param object $viewSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $viewSpec
    ): stdClass {
        return (object)[];
    }
    /* CBModel_build() */



    /* -- CBView interfaces -- */



    /**
     * @param object $viewModel
     *
     * @return void
     */
    static function
    CBView_render(
        stdClass $viewModel
    ): void {
        $currentPrettyUsername = CB_Username::fetchCurrentUserPrettyUsername();

        if (
            $currentPrettyUsername === null
        ) {
            $userEmoji = '👤';
            $userURL = '/colby/user/';
        } else {
            $userEmoji = '👨';
            $userURL = "/user/{$currentPrettyUsername}/";
        }

        echo <<<EOT

            <header class="CB_CBView_MainHeader CB_UI">
                <div class="CB_CBView_MainHeader_group">

        EOT;

        CB_CBView_MainHeader::renderItem(
            '🏠',
            '/'
        );

        CB_CBView_MainHeader::renderItem(
            '🔍',
            '/search/'
        );

        CB_CBView_MainHeader::renderItem(
            $userEmoji,
            $userURL,
        );

        echo <<<EOT

            </div>
            <div class="CB_CBView_MainHeader_group">

        EOT;

        $isAdministator = CBUserGroup::currentUserIsMemberOfUserGroup(
            'CBAdministratorsUserGroup'
        );

        if ($isAdministator) {
            CB_CBView_MainHeader::renderItem(
                '🔨',
                '/admin/'
            );
        }

        $stripePreferencesModel = CBModelCache::fetchModelByID(
            SCStripePreferences::ID()
        );

        $livePublishableKey = SCStripePreferences::getLivePublishableKey(
            $stripePreferencesModel
        );

        if ($livePublishableKey !== null) {
            CB_CBView_MainHeader::renderItem(
                '🛍',
                '/view-cart/'
            );
        }

        echo <<<EOT

                </div>
            </header>

        EOT;
    }
    /* CBView_render() */



    /* -- functions -- */



    /**
     * @param string $emoji
     * @param string $url
     * @param string $text
     *
     * @return void
     */
    private static function
    renderItem(
        string $emoji,
        string $url
    ): void {
        $emojiAsHTML = cbhtml(
            $emoji
        );

        echo <<<EOT

            <a class="CB_CBView_MainHeader_item" href="{$url}">
                <div class="CB_CBView_MainHeader_icon">$emojiAsHTML</div>
            </a>

        EOT;
    }
    /* renderItem() */

}
