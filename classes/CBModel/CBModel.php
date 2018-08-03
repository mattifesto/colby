<?php

/**
 * A model is an data storage object with its className property set to a
 * non-empty string. This class name indicates which class the model's data is
 * associated with.
 *
 * In object-oriented programming data lives inside class instances. Colby takes
 * a more functional approach and data lives inside models. Models don't
 * represent instances of classes, they are just data. The class has functions
 * that use or process this data in different ways the class creator has
 * determined are useful.
 *
 * Classes often build one model from another model by implementing the
 * CBModel_build() interface which is called by the CBModel::build()
 * function. In the context of buiding models, we refer to the source model as a
 * spec. The spec is still a model, but the word spec is used in this context to
 * indicate that this model is the source model.
 *
 * As an example, classes implementing the CBView_render() interface use model
 * data to render HTML content. Those classes usually also use model data to
 * generate search text and build models from specs.
 *
 * Array Function Safety
 *
 * The functions of CBModel are designed to be safe for array functions. They
 * take a mixed-type first parameter and return a nullable value. In most cases
 * the parameter is expected to be a model, if it is not a model, null is
 * returned from the function.
 *
 * This enables the functions to be used with functions like array_map() without
 * having to verify beforehand that every item in the array is a model. Adding a
 * call to array_filter() will remove the null values so these functions work to
 * remove non-model values from arrays.
 *
 * This self-cleaning behavior is better than a strongly typed behavior because
 * while a notification of a non-model value might be nice, that notification
 * would come in the form of an exception and the process of later removing a
 * non-model value would be difficult. Non-model values in this context will not
 * be a common situation.
 */
final class CBModel {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [Colby::flexpath(__CLASS__, 'v439.js', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return ['CBConvert'];
    }

    /**
     * This function returns a copy of the $spec parameter.
     *
     * Copies of model specs are further processed by model classes that
     * implement the CBModel_prepareCopy() interface. The interface will be
     * passed a copy of the spec. The interface can return that object with or
     * without modifications, or it can return an entirely different object. The
     * interface must return a valid model.
     *
     * The copy preparation usually involves resetting properties that need to
     * be reset for copied specs. For instance, a copied page spec will be
     * unpublished.
     *
     * @param model $spec
     *
     *      The spec to copy.
     *
     * @param ID $ID
     *
     *      The ID for the copy.
     *
     * @return ?model
     *
     *      If the $spec parameter is not a model, this function will return
     *      null. Otherwise, this function will always return another model.
     *
     *      The returned model will always be a different object than $spec
     *      argument.
     */
    static function copy(stdClass $spec, string $ID): ?stdClass {
        $spec = CBConvert::valueAsModel($spec);

        if ($spec === null) {
            return null;
        }

        $className = CBModel::valueToString($spec, 'className');
        $title = trim(CBModel::valueToString($spec, 'title'));
        $copy = CBModel::clone($spec);
        $copy->ID = $ID;
        $copy->title = empty($title) ? 'Copy' : "{$title} Copy";

        unset($copy->version);

        if (is_callable($function = "{$className}::CBModel_prepareCopy")) {
            $copy = call_user_func($function, $copy);
        }

        return $copy;
    }

    /**
     * This function performs a deep clone of a model by serializing it to JSON
     * and then unserializing it. Since models are always serialized to JSON
     * this method of cloning is will always produce a valid clone.
     *
     * @param mixed $model
     *
     *      This parameter does not technically have to be a model. It does need
     *      to be serializable to JSON.
     *
     * @return mixed
     */
    static function clone($model) {
        return json_decode(json_encode($model));
    }

    /**
     * Returns the index of the first model found with the specified property
     * value.
     *
     * @param [model] $models
     * @param string $propertyName
     * @param mixed $propertyValue
     *
     *      The property value is compared to the model property value with the
     *      == operator.
     *
     * @return mixed
     *
     *      Returns an int for numeric arrays and a string for associative
     *      arrays. Returns null if no match is found.
     */
    static function indexOf(array $models, string $propertyName, $propertyValue) {
        foreach ($models as $index => $model) {
            $value = CBModel::value($model, $propertyName);

            if ($value == $propertyValue) {
                return $index;
            }
        }

        return null;
    }

    /**
     * This function can be used to generate IDs for specs, such as specs
     * imported from CSV files.
     *
     * @param mixed $spec
     *
     *      This function takes a mixed parameter to make it "array function
     *      safe".
     *
     * @return ?hex160
     */
    static function toID($spec): ?string {
        $className = CBModel::valueToString($spec, 'className');

        if (is_callable($function = "{$className}::CBModel_toID")) {
            return call_user_func($function, $spec);
        } else {
            return null;
        }
    }

    /**
     * This function builds a new model from a source model. The source model,
     * in this context, is called a spec. Models are built to provide an
     * opportunity to process a user edited spec into a more stable or
     * performant model. For instance, the building function may precompute the
     * HTML escaped versions of strings.
     *
     * Reading this function will help developers understand the exact
     * interactions and requirements of specs and models.
     *
     *      @NOTE
     *      A model will always have the same class name as its spec.
     *
     *      In the past a spec was allowed to generate a model with a different
     *      class name, but this was never used. Allowing this to occur presents
     *      the dilemma of which class name should be used in the CBModels
     *      table. Model transitions may need to take place, but the transition
     *      should not occur during CBModel::build().
     *
     *      @NOTE
     *      If a model needs an ID, for example because it's going to be saved,
     *      that ID must be set on the spec. If a spec ID property is set it
     *      must be a valid hex160 and will always be copied to the model.
     *
     *      Generated IDs:
     *
     *      Sometimes specs are imported from CSV files created from
     *      spreadsheets edited by website administrators. Those users may
     *      specify human readable unique identifiers like product codes instead
     *      of IDs. The import process will notice that a spec doesn't have an
     *      ID and will call CBModel::toID() to generate an ID which it will
     *      then set on the spec.
     *
     *      Any ID generation must be done for a spec before it is used with
     *      this function.
     *
     *      @NOTE
     *      Since title is such a commonly used property, this function will
     *      perform a basic transfer of title from the spec to the model if the
     *      model produced doesn't have a title set.
     *
     * @param mixed $spec
     *
     *      This function takes a mixed parameter to make it "array function
     *      safe".
     *
     *      For this function to successfully build a model, the spec's class
     *      must exist and have the CBModel_build() interface implemented.
     *
     *      The $spec parameter is not typed so that you can do somthing like:
     *
     *          $model = CBModel::build(CBModel::value($spec, 'image'));
     *
     *      If the value returned by CBModel::value() is not an object
     *      CBModel::build() will return null.
     *
     * @return ?model
     *
     *      This function will return null if the CBModel_build() interface is
     *      not implemented. That function may return null for specs that don't
     *      meet its basic requirements. The CBImage class, for example, has a
     *      number of specific spec requirements. However, most classes can
     *      build a model from a completely empty spec.
     */
    static function build($spec): ?stdClass {
        $className = CBModel::valueToString($spec, 'className');

        if (empty($className)) {
            return null;
        }

        $model = null;

        if (is_callable($function = "{$className}::CBModel_build")) {
            $model = call_user_func($function, $spec);
        } else if (is_callable($function = "{$className}::CBModel_toModel")) {
            $model = call_user_func($function, $spec);
        }

        if (!is_object($model)) {
            return null;
        }

        $model->className = $className;

        if (!empty($spec->ID)) {
            if (CBHex160::is($spec->ID)) {
                $model->ID = $spec->ID;
            } else {
                return null;
            }
        }

        if (!isset($model->title) && isset($spec->title)) {
            $model->title = trim(CBModel::valueToString($spec, 'title'));
        }

        return $model;
    }

    /**
     * Transfer the first level property values of object 2 onto object 1.
     * Object 1 may be altered, object 2 will not be altered.
     *
     * This function is meant to be used with models, but there is no reason it
     * can't be used with objects that aren't technically models.
     *
     * @param object $object1
     * @param object $object2
     *
     * @return void
     */
    static function merge(stdClass $object1, stdClass $object2): void {
        foreach ($object2 as $key => $value) {
            $object1->{$key} = $value;
        }
    }

    /**
     * @deprecated use CBModel::build()
     */
    static function toModel(stdClass $spec) {
        return CBModel::build($spec);
    }

    /**
     * @param mixed $model
     *
     *      This function takes a mixed parameter to make it "array function
     *      safe".
     *
     * @return string
     */
    static function toSearchText($model): string {
        $className = CBModel::valueToString($model, 'className');

        if (empty($className)) {
            return '';
        }

        $ID = CBModel::valueAsID($model, 'ID');
        $text = '';

        if (is_callable($function = "{$className}::CBModel_toSearchText")) {
            $text = call_user_func($function, $model);
        } else if (is_callable($function = "{$className}::modelToSearchText")) { // deprecated
            $text = call_user_func($function, $model);
        }

        return implode(' ', array_filter([$text, $className, $ID]));
    }

    /**
     * This function returns an upgraded version of the $spec parameter.
     *
     * Model specs are upgraded by model classes that implement the
     * CBModel_upgrade() interface. The interface will be passed a clone of the
     * spec to be upgraded. The interface can return that object with or without
     * modifications, or it can return an entirely different object. The
     * interface must return a valid model.
     *
     * @param mixed $spec
     *
     *      This function takes a mixed parameter to make it "array function
     *      safe".
     *
     * @return ?model
     *
     *      If the $spec parameter is not a model, this function will return
     *      null. Otherwise, this function will always return another model.
     *
     *      The returned model will always be a different object than $spec
     *      argument. However the returned model may be equal to the $spec
     *      argument. You can compare the $spec argument to the returned model
     *      using == to determine if any changes were made during the upgrade.
     */
    static function upgrade($spec): ?stdClass {
        $spec = CBConvert::valueAsModel($spec);

        if ($spec === null) {
            return null;
        }

        $upgradedSpec = CBModel::clone($spec);

        if (is_callable($function = "{$spec->className}::CBModel_upgrade")) {
            $upgradedSpec = CBConvert::valueAsModel(call_user_func($function, $upgradedSpec));

            if ($upgradedSpec === null) {
                throw new Exception("{$function}() returned an invalid model");
            }

            $ID = CBModel::valueAsID($spec, 'ID');
            $upgradedID = CBModel::valueAsID($upgradedSpec, 'ID');

            if ($upgradedID != $ID) {
                $message = <<<EOT

                    When the {$className} model with the ID "{$ID}"
                    was upgraded using CBModel::upgrade(), the ID was
                    altered which is not allowed.

                    The upgrade was cancelled.

EOT;

                CBLog::log((object)[
                    'className' => __CLASS__,
                    'message' => $message,
                    'severity' => 3,
                ]);

                return CBModel::clone($spec);
            }
        }

        return $upgradedSpec;
    }

    /**
     * @param mixed $model
     *
     *      This function takes a mixed parameter to make it "array function
     *      safe".
     *
     *      The $model parameter is generally expected to be a stdClass instance
     *      or `null`, but it can be any value such as `42`. If it is not
     *      stdClass this function will treat is as `null` and return the
     *      default value.
     *
     *      This behavior reduces the amount of validation code required in many
     *      cases. For instance, it allows code to fetch a model and not
     *      validate that the model exists (the model value may be `false` in
     *      this case) before checking to see if a value is set.
     *
     * @param string $keyPath
     *
     *      Examples: "height", "width", "image.height",
     *                "image.alternativeText.text"
     *
     * @param mixed $default (deprecated)
     * @param function $transform (deprecated)
     *
     * @return mixed
     */
    static function value($model, $keyPath, $default = null, callable $transform = null) {
        $keys = explode('.', $keyPath);
        $propertyName = array_pop($keys);

        foreach($keys as $key) {
            if (isset($model->{$key}) && is_object($model->{$key})) {
                $model = $model->{$key};
            } else {
                return $default;
            }
        }

        if (isset($model->{$propertyName})) {
            if ($transform !== null) {
                return call_user_func($transform, $model->{$propertyName});
            } else {
                return $model->{$propertyName};
            }
        } else {
            return $default;
        }
    }

    /**
     * This is a convenience function for:
     *
     *      CBConvert::valueAsHex160(CBModel::value(...))
     *
     * @param mixed $model
     * @param string $keyPath
     *
     * @return hex160|null
     */
    static function valueAsID($model, string $keyPath): ?string {
        return CBConvert::valueAsHex160(CBModel::value($model, $keyPath));
    }

    /**
     * This is a convenience function for:
     *
     *      CBConvert::valueAsInt(CBModel::value(...))
     *
     * @param mixed $model
     * @param string $keyPath
     *
     * @return ?int
     */
    static function valueAsInt($model, string $keyPath): ?int {
        return CBConvert::valueAsInt(CBModel::value($model, $keyPath));
    }

    /**
     * This is a convenience function for:
     *
     *      CBConvert::valueAsModel(CBModel::value(...))
     *
     * @param mixed $model
     * @param string $keyPath
     *
     * @return ?model
     */
    static function valueAsModel($model, string $keyPath, array $classNames = []): ?stdClass {
        return CBConvert::valueAsModel(CBModel::value($model, $keyPath), $classNames);
    }

    /**
    * This is a convenience function for:
    *
    *      CBConvert::valueAsMoniker(CBModel::value(...))
    *
    * @param mixed $model
    * @param string $keyPath
    *
    * @return ?string
     */
    static function valueAsMoniker($model, string $keyPath): ?string {
        return CBConvert::valueAsMoniker(CBModel::value($model, $keyPath));
    }

    /**
     * This is a convenience function for:
     *
     *      CBConvert::valueAsObject(CBModel::value(...));
     *
     * @param mixed $model
     * @param string $keyPath
     *
     * @return ?object
     */
    static function valueAsObject($model, string $keyPath): ?stdClass {
        return CBConvert::valueAsObject(CBModel::value($model, $keyPath));
    }

    /**
     * This is a convenience function for:
     *
     *      CBConvert::valueToArray(CBModel::value(...))
     *
     * @param mixed $model
     * @param string $keyPath
     *
     * @return [mixed]
     */
    static function valueToArray($model, string $keyPath): array {
        return CBConvert::valueToArray(CBModel::value($model, $keyPath));
    }

    /**
     * This is a convenience function for:
     *
     *      CBConvert::valueToBool(CBModel::value(...))
     *
     * CBConvert::valueToBool() behaves differently than boolval() when the
     * value is a string.
     *
     * @param mixed $model
     * @param string $keyPath
     *
     * @return bool
     */
    static function valueToBool($model, string $keyPath): bool {
        return CBConvert::valueToBool(CBModel::value($model, $keyPath));
    }

    /**
     * This is a convenience function for:
     *
     *      CBConvert::valueToObject(CBModel::value(...));
     *
     * @param mixed $model
     * @param string $keyPath
     *
     * @return object
     */
    static function valueToObject($model, string $keyPath): stdClass {
        return CBConvert::valueToObject(CBModel::value($model, $keyPath));
    }

    /**
     * This is a convenience function for:
     *
     *      CBConvert::valueToNames(CBModel::value(...));
     *
     * @param mixed $model
     * @param string $keyPath
     *
     * @return [string]
     */
    static function valueToNames($model, string $keyPath): array {
        return CBConvert::valueToNames(CBModel::value($model, $keyPath));
    }

    /**
     * This is a convenience function for:
     *
     *      CBConvert::valueToString(CBModel::value(...));
     *
     * @param mixed $model
     * @param string $keyPath
     *
     * @return string
     */
    static function valueToString($model, string $keyPath): string {
        return CBConvert::valueToString(CBModel::value($model, $keyPath));
    }
}
