<?php

CBHandleAdminHelpSetup::render();

final class CBHandleAdminHelpSetup {

    /**
     * @return string
     */
    static function pageHelpersContentAsHTML() {
        $md = <<<EOT

# CBPageHelpers

The CBPageHelpers class is a class that provides information about a website
that is shared among all instances of a site, for instance the development,
test, and production instances. Additionally, this class was created to replace
properties in various preferences classes that shouldn't be easily changeable
between instances and shouldn't have to be manually set for an instance.

There was an awkwardness to site setup in which the site would be officially
"complete" but the developer would have to go the various preferences pages on
each instance and set values manually for the instances to work properly even
though the values don't change frequently and are shared between all instances.

A new site should run fine without CBPageHelpers being implemented at all, but
as cusomization occurs, CBPageHelpers functions will become useful.

EOT;

        return (new Parsedown())->text($md);
    }

    /**
     * @return null
     */
    static function render() {
        if (!ColbyUser::current()->isOneOfThe('Administrators')) {
            return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
        }

        $model = (object)[
            'classNameForSettings' => 'CBPageSettingsForAdminPages',
            'titleHTML' => 'API',
            'descriptionHTML' => 'Optional APIs',
            'layout' => (object)[
                'className' => 'CBPageLayout',
                'customLayoutClassName' => 'CBAdminPageLayout',
                'customLayoutProperties' => (object)[
                    'selectedMenuItemName' => 'help',
                    'selectedSubmenuItemName' => 'api',
                ],
            ],
            'sections' => [
                (object)[
                    'className' => 'CBPageTitleAndDescriptionView',
                ],
                (object)[
                    'className' => 'CBTextView2',
                    'contentAsHTML' => CBHandleAdminHelpSetup::pageHelpersContentAsHTML(),
                ],
                CBModel::specToOptionalModel((object)[
                    'className' => 'CBAPIDocumentationView',
                    'apiAsHTML' => 'static function <span class="name">classNameForPageSettings</span>() -&gt; string',
                    'descriptionAsMarkdown' => <<<EOT

This function should not be implememented.

It was a mistake to give this function such an innocuous sounding name when a
better description of it would be *the class name for page settings that should
be improperly  hardcoded onto the models of newly created pages*. That would
make a horribly long  function name.

Sometimes an older site has a default class name for page settings that is not
actually the correct value for new pages. The default class name for page
settings set for such a site represents an outdated set of  page settings but
those sites have a low maintenance budget and can't afford to update their older
pages.

Those sites don't wish to negatively impact new pages, so the
`CBStandardPageTemplate` class will use the return value of this function as a
hardcoded value for the `classNameForSettings` property on the model for new
pages.

Preferably, new pages should not set `classNameForSettings` property on page
models. An unset `classNameForSettings` property will cause the current default
class name for page settings to be used when the page is rendered.

EOT
                ]),
                CBModel::specToOptionalModel((object)[
                    'className' => 'CBAPIDocumentationView',
                    'apiAsHTML' => 'static function <span class="name">classNameForUnsetPageSettings</span>() -&gt; string',
                    'descriptionAsMarkdown' => <<<EOT

The class name returned by this function will be used as the class name for page
settings by `CBHTMLOutput` when rendering a page whose model has no value set
for the `classNameForSettings` property.

EOT
                ]),
                CBModel::specToOptionalModel((object)[
                    'className' => 'CBAPIDocumentationView',
                    'apiAsHTML' => 'static function <span class="name">classNamesForPageKinds</span>() -&gt; [string]',
                    'descriptionAsMarkdown' => <<<EOT

This function should return a list of class names for page kinds. Include the
class names returned by the `CBPagesPreferences` function
`classNamesForPageKindsDefault`() along with the custom class names for your
site.

If implemented, this function provides the return value for the
`CBPagesPreferences` function `classNamesForPageKinds`().

EOT
                ]),
                CBModel::specToOptionalModel((object)[
                    'className' => 'CBAPIDocumentationView',
                    'apiAsHTML' => 'static function <span class="name">classNamesForPageTemplates</span>() -&gt; [string]',
                    'descriptionAsMarkdown' => <<<EOT

This function should return a list of class names that generate new page
specs. Each class name will produce an option for the create page menu of the
admin area.

If this function is not implememented, the value of the
`classNamesForPageTemplatesDefault` function on the `CBPagesPreferences` class
will be used.

EOT
                ]),
                CBModel::specToOptionalModel((object)[
                    'className' => 'CBAPIDocumentationView',
                    'apiAsHTML' => 'static function <span class="name">renderDefaultPageFooter($properties)</span>',
                    'descriptionAsMarkdown' => <<<EOT
EOT
                ]),
                CBModel::specToOptionalModel((object)[
                    'className' => 'CBAPIDocumentationView',
                    'apiAsHTML' => 'static function <span class="name">renderDefaultPageHeader($properties)</span>',
                    'descriptionAsMarkdown' => <<<EOT
EOT
                ]),
            ],
        ];

        CBViewPage::renderModelAsHTML($model);
    }
}

/**
 *
 */
class CBAPIDocumentationView {

    /**
     * @param object $spec
     *
     * @return object
     */
    static function specToModel(stdClass $spec) {
        return (object)[
            'className' => 'CBAPIDocumentationView',
            'apiAsHTML' => CBModel::value($spec, 'apiAsHTML', ''),
            'descriptionAsHTML' => cbmdhtml(CBModel::value($spec, 'descriptionAsMarkdown', '')),
        ];
    }

    /**
     * @return null
     */
    static function renderModelAsHTML(stdClass $model) {
        ?>

        <div class="CBAPIDocumentationView">
            <div class="content">
                <div class="api">
                    <?= CBModel::value($model, 'apiAsHTML') ?>
                </div>
                <div class="description CBTextView2StandardLayout">
                    <?= CBModel::value($model, 'descriptionAsHTML') ?>
                </div>
            </div>
        </div>

        <?php
    }

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBTextView2StandardLayout'];
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [CBSystemURL . '/handlers/handle,admin,help,api.css'];
    }
}
