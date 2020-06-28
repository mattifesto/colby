<?php

/**
 * The SCShoppingCart model is used on the client to hold the items the user has
 * placed in their cart. It is not used on the server side which is why this
 * class does not implement the CBModel interfaces. It is a model on the client
 * side because it is saved using CBModel.save(). The model has the following
 * properties:
 *
 *      {
 *          className: "SCShoppingCart"
 *          cartItems: [object]
 *      }
 */
final class SCShoppingCart {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v625.js', scliburl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        $preferencesModel = CBModelCache::fetchModelByID(
            SCPreferences::ID()
        );

        $installedCartItemClassNames = CBModel::valueToArray(
            $preferencesModel,
            'cartItemClassNames'
        );

        return array_merge(
            [
                'CBModel',
                'CBModels',
                'CBUIPanel',
                'Colby',
                'SCCartItem',
                'SCCartItemCollection',
            ],
            $installedCartItemClassNames
        );
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- functions -- -- -- -- -- */



    /**
     * @param object $shoppingCartModel
     *
     * @return [object]
     */
    static function getItems(stdClass $shoppingCartModel): array {
        return CBModel::valueToArray(
            $shoppingCartModel,
            'cartItems'
        );
    }

}
