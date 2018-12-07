<?php

/**
 * A tag should be associated with a model whenever you want to associate a
 * simple string with a model. The association key will be used to differentiate
 * between different contexts of tags.
 */
final class CBTag {

    /**
     * @param ID $ID
     * @param [string] $tags
     * @param string $associationKey
     *
     * @return void
     */
    static function add(string $ID, string $associationKey, array $tags): void {
        foreach ($tags as $tag) {
            CBModelAssociations::add(
                $ID,
                $associationKey,
                CBTag::tagToID($tag)
            );
        }
    }

    /**
     * @param object $spec
     *
     * @return ?object
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        $tag = CBModel::valueToString($spec, 'tag');
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

    /**
     * @param [string]
     *
     * @return void
     */
    static function create(array $tags): void {
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

    /**
     * Generate a tag ID with which an MCProductOption model will be associated.
     *
     * @param string $tag
     *
     * @return ID
     */
    static function tagToID(string $tag): string {
        if ($tag === '') {
            throw new InvalidArgumentException('the tag argument is empty');
        }

        return sha1("94ece6500fcaafedf25690262b45da0ec2d8c5b0 {$tag}");
    }
}
