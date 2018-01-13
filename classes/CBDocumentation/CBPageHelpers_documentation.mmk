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
