<?php

/**
 * @deprecated 2022_05_09
 *
 *      Use CB_Tag.
 *
 * A tag should be associated with a model whenever you want to associate a
 * simple string with a model. The association key will be used to differentiate
 * between different contexts of tags.
 *
 * A tag can literally be any string but you may want to limit the strings
 * allowed for various contexts.
 */
final class
CBTag
{
    // -- CB_CBAdmin_Code interfaces



    /**
     * @return [object]
     */
    static function
    CBCodeAdmin_searches(
    ): array
    {
        $searchForClassName =
        (object)
        [
            'args' =>
            '--ignore-file=match:/^CBTag\./',

            'cbmessage' =>
            <<<EOT

                Use CB_Tag.

            EOT,

            'noticeStartDate' =>
            '2022/05/09',

            'regex' =>
            '\\bCBTag\\b',

            'severity' =>
            5,

            'title' =>
            'CBTag',
        ];

        return
        [
            $searchForClassName,
        ];
    }
    // CBCodeAdmin_searches()



    // -- functions



    /**
     * @param ID $ID
     * @param string $associationKey
     * @param [string] $tags
     *
     * @return void
     */
    static function
    add(
        string $ID,
        string $associationKey,
        array $tags
    ): void {
        foreach ($tags as $tag) {
            CBModelAssociations::add(
                $ID,
                $associationKey,
                CBTag::tagToID($tag)
            );
        }
    }
    /* add() */



    /**
     * @param object $spec
     *
     * @return ?object
     */
    static function
    CBModel_build(
        stdClass $spec
    ): ?stdClass {
        $tag = CBModel::valueToString($spec, 'title');
        $ID = CBModel::valueAsID($spec, 'ID');

        if (
            $ID !== CBTag::tagToID($tag)
        ) {
            $specAsJSON = json_encode($spec);
            $message = <<<EOT

                The ID in this spec is not the correct ID for the tag.

                --- pre\n{$specAsJSON}
                ---

            EOT;

            CBLog::log((object)[
                'message' => $message,
                'modelID' => CBTag::tagToID($tag),
                'severity' => 3,
                'sourceClassName' => __CLASS__,
                'sourceID' => '30b3720f7dd7e06413b949c1ade1fd28d652f7f0',
            ]);

            return null;
        }

        return (object)[
            'title' => $tag,
        ];
    }
    /* CBModel_build() */



    /**
     * @param [string]
     *
     * @return void
     */
    static function
    create(
        array $tags
    ): void {
        foreach ($tags as $tag) {
            CBModelUpdater::update(
                (object)[
                    'className' => __CLASS__,
                    'ID' => CBTag::tagToID($tag),
                    'title' => $tag,
                ]
            );
        }
    }
    /* create() */



    /**
     * @param ID $ID
     * @param string $associationKey
     * @param [string] $tags
     *
     * @return void
     */
    static function
    delete(
        string $ID,
        string $associationKey,
        array $tags
    ): void {
        foreach ($tags as $tag) {
            CBModelAssociations::delete(
                $ID,
                $associationKey,
                CBTag::tagToID($tag)
            );
        }
    }
    /* delete() */



    /**
     * @param string $associationKey
     * @param string $tag
     *
     * @return [ID]
     */
    static function
    fetchModelIDs(
        string $associationKey,
        $tag
    ): array {
        return array_map(
            function ($association) {
                return $association->ID;
            },
            CBModelAssociations::fetch(
                null,
                $associationKey,
                CBTag::tagToID($tag)
            )
        );
    }
    /* fetchModelIDs() */



    /**
     * @see documenation
     *
     * @param string $string
     *
     * @return string
     */
    static function
    stringToNormalizedTag(
        string $originalString
    ): string {
        $normalizedTag = mb_strtolower(
            $originalString
        );

        $normalizedTag = preg_replace(
            '/[^\p{Ll}0-9_]/u',
            '',
            $normalizedTag
        );

        return $normalizedTag;
    }
    /* stringToNormalizedTag() */



    /**
     * @see documenation
     *
     * @param string $tag
     *
     * @return ID
     */
    static function
    tagToID(
        string $tag
    ): string {
        return sha1(
            "94ece6500fcaafedf25690262b45da0ec2d8c5b0 {$tag}"
        );
    }
    /* tagToID() */

}
