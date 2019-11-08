<?php

final class CBUser {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v500.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        $userNumericID = CBModel::valueAsInt($spec, 'userID');

        if ($userNumericID === null) {
            throw CBException::createModelIssueException(
                'The "userID" property must be an integer.',
                $spec,
                'd3cc11bb7ee84089207dc00fee71ecbb5f9679d4'
            );
        }

        return (object)[
            'description' => trim(
                CBModel::valueToString($spec, 'description')
            ),

            'facebook' => CBModel::clone(
                CBModel::valueToObject($spec, 'facebook')
            ),

            'lastLoggedIn' => CBModel::valueAsInt($spec, 'lastLoggedIn') ?? 0,

            'title' => trim(
                CBModel::valueToString($spec, 'title')
            ),

            'userID' => $userNumericID,
        ];
    }
    /* CBModel_build() */

}
