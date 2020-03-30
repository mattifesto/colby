<?php

final class CBUpgradesForVersion595 {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBAdmin_getIssueCBMessages(): array {
        $submoduleURLs = CBGit::getSubmoduleURLs();

        $shoppingCartSubmoduleURL = (
            'mdgit@mattifesto.com:~/libraries/SCShoppingCartLibrary.git'
        );

        $shoppingCartSubmoduleShouldBeRemoved = in_array(
            $shoppingCartSubmoduleURL,
            $submoduleURLs
        );

        if ($shoppingCartSubmoduleShouldBeRemoved) {
            return [
                <<<EOT

                    The shopping cart submodule needs to be removed from the
                    website.

                EOT,
            ];
        } else {
            return [];
        }
    }
    /* CBAdmin_getIssueCBMessages() */

}
