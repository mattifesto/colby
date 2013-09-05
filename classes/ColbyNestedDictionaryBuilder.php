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
class ColbyNestedDictionary
{
    private $data = null;

    /**
     * This constructor is private to force use of the static initializers.
     */
    private function __construct()
    {
    }

    /**
     * @return ColbyNestedDictionary
     */
    public static function nestedDictionaryWithDataObject(stdClass $dataObject)
    {
        $nestedDictionary = new ColbyNestedDictionary();

        /**
         * It's up the the developer to make sure they pass in a data object
         * with the schema required.
         */

        $nestedDictionary->data = $dataObject;

        return $nestedDictionary;
    }

    /**
     * @return ColbyNestedDictionary
     */
    public static function nestedDictionaryWithTitle($title)
    {
        $dataObject = new stdClass();

        $dataObject->title = (string)$title;
        $dataObject->objectSchema = 'ColbyNestedDictionary';
        $dataObject->objectSchemaVersion = 1;
        $dataObject->items = new stdClass();

        $nestedDictionary = new ColbyNestedDictionary();

        $nestedDictionary->data = $dataObject;

        return $nestedDictionary;
    }

    /**
     *
     */
    public function addValue($outerKey, $innerKey, $value)
    {
        if (!isset($this->data->items->{$outerKey}))
        {
            $this->data->items->{$outerKey} = new stdClass();
        }

        $this->data->items->{$outerKey}->{$innerKey} = $value;
    }

    /**
     * @return stdClass
     */
    public function dataObject()
    {
        return $this->data;
    }
}
