--- h1
CBPageHelpers
---

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
as customization occurs, CBPageHelpers functions will become useful.


--- div api

(static function (classNameForUnsetPageSettings (name)): string (function))

    --- description
    The class name returned by this function will be used as the class name for page
    settings by (CBHTMLOutput (code)) when rendering a page whose model has no value set
    for the (classNameForSettings (code)) property.
    ---
---

--- div api

(static function (classNamesForPageKinds (name)): string (function))

    --- description
    This function should return a list of class names for page kinds. Include the
    class names returned by the (CBPagesPreferences (code)) function
    (classNamesForPageKindsDefault\(\) (code)) along with the custom class names for your
    site.

    If implemented, this function provides the return value for the
    (CBPagesPreferences (code)) function (classNamesForPageKinds\(\) (code)).
    ---
---

--- div api

(static function (renderDefaultPageFooter (name)): string (function))

    --- description
    ---
---

--- div api

(static function (renderDefaultPageHeader (name)): string (function))

    --- description
    ---
---

--- div api deprecated

(static function (classNameForPageSettings (name)): string (function))

    --- description
    This return value of this function, if implemented, is used by the
    (CBStandardPageTemplate (code)) class as the value for the
    (classNameForSettings (code)) variable on the new page specs that it
    creates.

    It was a mistake to give this function such an innocuous sounding name when
    a better description of it would be:

    --- blockquote
    The class name for page settings that should be improperly hardcoded onto
    the models of newly created pages.
    ---

    That would make a horribly long function name.

    Sometimes an older site has a default class name for page settings that is
    not actually the correct value for new pages. The default class name for
    page settings set for such a site represents an outdated set of page
    settings but those sites have a low maintenance budget and can't afford to
    update their older pages.

    Those sites don't wish to negatively impact new pages, so the
    (CBStandardPageTemplate (code)) class will use the return value of this function as
    a hardcoded value for the (classNameForSettings (code)) property on the model for
    new pages.

    Preferably, new pages should not set (classNameForSettings (code)) property on page
    models. An unset (classNameForSettings (code)) property will cause the current
    default class name for page settings to be used when the page is rendered.
    ---
---
