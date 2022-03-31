<?php

final class
CB_URL
{
    // -- functions



    /**
     * This function allows but does not require the first or the last slash
     * on the URL path.
     *
     * @param string $originalPotentialURLPath
     *
     * @return bool
     */
    static function
    potentialURLPathIsValid(
        string $originalPotentialURLPath
    ): bool
    {
        // remove first and last slash if they exist

        $potentialURLPath =
        preg_replace(
            [
                '/^\//',
                '/\/$/',
            ],
            '',
            $originalPotentialURLPath
        );

        $potentialURLStubs =
        preg_split(
            '/\//',
            $potentialURLPath
        );

        foreach (
            $potentialURLStubs as $potentialURLStub
        ) {
            $isValid =
            CB_URL::potentialURLStubIsValid(
                $potentialURLStub
            );

            if (
                $isValid !== true
            ) {
                return false;
            }
        }

        return
        true;
    }
    // isPotentialURLPathValid()



    /**
     * This function is the official arbiter of what URL stubs are valid URL
     * stubs for Colby pages. The allowed characters are:
     *
     *      a-z
     *      Lowercase letters. Stubs are case insensitive and capital letters
     *      are not allowed in stubs. If a user requests a stub with capital
     *      letters they will be redirected to the same stub with lowercase
     *      letters if a page with that stub exists.
     *
     *      0-9
     *      Numbers.
     *
     *      -
     *      Hyphens. Hyphens are preferred to be singular between letters and
     *      numbers but that is not required.
     *
     *      _
     *      Underscores. Hyphens are preferred over underscores but pages for
     *      class nanes, function names, or usernames may require the use of
     *      underscores.
     *
     * @param string $potentialURLStub
     *
     * @return bool
     */
    static function
    potentialURLStubIsValid(
        string $potentialURLStub
    ): bool
    {
        $result =
        preg_match(
            '/^[a-z0-9_-]+$/',
            $potentialURLStub
        );

        if (
            $result === false
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    When the potential URL stub "${potentialURLStub}" was passed
                    as an argument to CB_URL::potentialURLStubIsValid(),
                    preg_match() produced in an error.

                EOT),
                $potentialURLStub,
                'd2fb28d1126ba4d4eba5e4866ac62a520d775216'
            );
        }

        return
        $result === 1;
    }
    // potentialURLStubIsValid()

}
