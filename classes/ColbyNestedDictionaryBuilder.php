<?php

/**
 * This class is a wrapper class around a `stdClass` object. The reason for
 * this way of doing this is that the object should be used directly to get
 * values and this class should only be used to help create objects with the
 * correct schema and help to change the object while maintaining that schema.
 *
 * Also, this class should never be directly serialized or stored in any way,
 * instead the object should be retrieved and used directly. Serializing
 * anything but core PHP types is error prone if the custom type is no longer
 * present or has changed in any way since serialization.
 *
 * The nested dictionary data structure is useful in many scenarios where
 * there is essentially a list of dictionaries. Menus are one example where
 * there is a list of menu items where each item has a set or properties such
 * as the item title and the item href.
 *
 * Trying to create an maintain changes to a nested dictionary without this
 * helper class is difficult and error prone. However, reading data without
 * this helper class is simple and straightforward.
 */
class ColbyNestedDictionaryBuilder
{
    private $nestedDictionary = null;

    /**
     * This constructor is private to force use of the static initializers.
     */
    private function __construct()
    {
    }

    /**
     * @return ColbyNestedDictionaryBuilder
     */
    public static function builderWithNestedDictionary(stdClass $nestedDictionary)
    {
        $builder = new ColbyNestedDictionary();

        /**
         * It's up the the caller to make sure they pass in an object
         * with a nested dictionary schema.
         */

        $builder->nestedDictionary = $nestedDictionary;

        return $builder;
    }

    /**
     * @return ColbyNestedDictionaryBuilder
     */
    public static function builderWithTitle($title)
    {
        $nestedDictionary = new stdClass();

        $nestedDictionary->title = (string)$title;
        $nestedDictionary->objectSchema = 'ColbyNestedDictionary';
        $nestedDictionary->objectSchemaVersion = 1;
        $nestedDictionary->items = new stdClass();

        $builder = new ColbyNestedDictionaryBuilder();

        $builder->nestedDictionary = $nestedDictionary;

        return $builder;
    }

    /**
     *
     */
    public function addValue($outerKey, $innerKey, $value)
    {
        if (!isset($this->nestedDictionary->items->{$outerKey}))
        {
            $this->nestedDictionary->items->{$outerKey} = new stdClass();
        }

        $this->nestedDictionary->items->{$outerKey}->{$innerKey} = $value;
    }

    /**
     * @return stdClass with a nested dictionary schema.
     */
    public function nestedDictionary()
    {
        return $this->nestedDictionary;
    }
}
