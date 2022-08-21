<?php

/**
 * @NOTE 2022_05_11
 *
 *      It feels odd to make a comment saying "this class is very important",
 *      but here we are. This class is important because if you are creating
 *      strings that will be converted to URLs this class will create a limit to
 *      those strings.
 *
 *      Tags are limited by this class. Titles are (sorta) limited by this
 *      class. But the class is always growing to eventually include every
 *      possible acceptable unique character.
 */
final class
CB_URL
{
    // -- functions



    /**
     * @NOTE 2022_08_21_1661043752
     *
     *      This function is being written in haste, but should become the
     *      function to validate URLs written in moments or in other casual
     *      URL entry scenarios.
     *
     *      Eventually this function should validate the top level domain and
     *      convert:
     *
     *          apple.com -> https://apple.com
     *
     * @param string $casualURL
     *
     * @return string
     *
     *      Returns "" if the casual URL is not valid.
     */
    static function
    convertCasualURLToActualURL(
        $casualURL
    ): string
    {
        $hasHTTP =
        preg_match(
            '/^(http:\/\/|https:\/\/)/',
            $casualURL
        );

        if (
            $hasHTTP
        ) {
            return $casualURL;
        }

        return '';
    }
    // convertCasualURLToActualURL()



    /**
     * A URL word is made up of characters that match this character regex. We
     * distinguish words from stubs because other concepts may also use the URL
     * word regex, such as tags. But often those concepts don't want to include
     * word separators like hyphens as URL stubs do.
     *
     * A match against this regex does not mean the string is a valid URL word.
     * URLs are converted to lowercase, however, some word characters don't have
     * lowercase versions so a URL can still have a mix of uppercase and
     * lowercase characters.
     *
     * A match against this regex and a conversion to lowercase will produce
     * a valid URL word.
     *
     * The return value of this function will change over time.
     *
     * @return string
     */
    static function
    getWordCharactersRegex(
    ): string
    {
        /**
         * @TODO 2022_05_10
         *
         *      Emoji should be acceptable characters which we can include when
         *      PHP supports:
         *
         *          \p{Emoji_Presentation}
         */

        return
        '\p{Ll}' .                  // lowercase letters
        '\p{Lu}' .                  // uppercase letters
        '\p{Nd}' .                  // decimal numbers
        '_';                        // underscore
    }
    // getWordCharactersRegex()



    /**
     * This function converts a raw string into a pretty word. A pretty word is
     * a sequence of word characters. The word pretty is used because it can
     * contain capital letters. This function might be used to convert a raw
     * string into a tag.
     *
     * A pretty word converted to lowercase is a valid URL.
     *
     *      "Hello World" ->
     *      "HelloWorld"
     *
     *      "Hello   World" ->
     *      "HelloWorld"
     *
     *      "  Hello  World   " ->
     *      "HelloWorld"
     *
     *      "A b C d Ť Ŧ Ʒ Ǌ Ѳ Ԭ ℳ Ⱓ Ꙫ 𐲖 𝓚 𝔅 𝕎 𝕽" ->
     *      "AbCdŤŦƷǊѲԬℳⰣꙪ𐲖𝓚𝔅𝕎𝕽"
     *
     *      " H e l l o   W-o-r-l-d " ->
     *      "HelloWorld"
     *
     *      "H😀e😀l😀l😀o World" ->
     *      "HelloWorld"
     *
     * @param string $rawString
     *
     * @return string
     */
    static function
    convertRawStringToPrettyWord(
        string $rawString
    ): string
    {
        $wordCharactersRegex =
        CB_URL::getWordCharactersRegex();

        $prettyWord =
        $rawString;

        // remove characters that aren't allowed

        $regex =
        '/[^' .
        $wordCharactersRegex .
        ']/u';

        $prettyWord =
        preg_replace(
            $regex,
            '',
            $prettyWord
        );

        return
        $prettyWord;
    }
    // convertRawStringToPrettyWord()



    /**
     * This function will convert a raw string to a URL path.
     *
     *      - One or more consecutive spaces will be converted to a single
     *      hyphen.
     *
     *          "hello world" ->
     *          "hello-world"
     *
     *          "hello    world" ->
     *          "hello-world"
     *
     *          "hello -world" ->
     *          "hello-world"
     *
     *          "hello   -world" ->
     *          "hello-world"
     *
     *          "hello- - -   -world" ->
     *          "hello-world"
     *
     *      - Uppercase characters will be converted to lowercase characters.
     *
     *          "A b C d Ť Ŧ Ʒ Ǌ Ѳ Ԭ ℳ Ⱓ Ꙫ 𐲖 𝓚 𝔅 𝕎 𝕽" ->
     *          "a-b-c-d-ť-ŧ-ʒ-ǌ-ѳ-ԭ-ℳ-ⱓ-ꙫ-𐳖-𝓚-𝔅-𝕎-𝕽"
     */
    static function
    convertRawStringToURLStub(
        string $rawString
    ): string
    {
        $wordCharactersRegex =
        CB_URL::getWordCharactersRegex();

        $stub =
        $rawString;

        // convert stub characters to lowercase

        $stub =
        mb_strtolower(
            $stub
        );

        // remove characters that aren't allowed or useful to the function

        $regex =
        '/[^' .
        $wordCharactersRegex .
        '\s-' .
        ']/u';

        $stub =
        preg_replace(
            $regex,
            '',
            $stub
        );

        // remove non-word characters from beginning of stub

        $stub =
        preg_replace(
            '/^[\s-]+/u',
            '',
            $stub
        );

        // remove non-word characters from end of stub

        $stub =
        preg_replace(
            '/[\s-]+$/u',
            '',
            $stub
        );

        // convert sequences of non-word characters to hyphens

        $stub =
        preg_replace(
            '/[\s-]+/',
            '-',
            $stub
        );

        return
        $stub;
    }
    // convertRawStringToURLStub()



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
     * @param string $potentialURLStub
     *
     * @return bool
     */
    static function
    potentialURLStubIsValid(
        string $potentialURLStub
    ): bool
    {
        $actualURLStub =
        CB_URL::convertRawStringToURLStub(
            $potentialURLStub
        );

        return
        $actualURLStub ===
        $potentialURLStub;
    }
    // potentialURLStubIsValid()



    /**
     * @param string $potentialPrettyWord
     *
     * @return bool
     */
    static function
    potentialPrettyWordIsValid(
        string $potentialPrettyWord
    ): bool
    {
        $stringLength =
        strlen(
            $potentialPrettyWord
        );

        if (
            $stringLength === 0
        ) {
            return
            false;
        }

        $actualPrettyWord =
        CB_URL::convertRawStringToPrettyWord(
            $potentialPrettyWord
        );

        return
        $actualPrettyWord ===
        $potentialPrettyWord;
    }
    // potentialPrettyWordIsValid()

}
