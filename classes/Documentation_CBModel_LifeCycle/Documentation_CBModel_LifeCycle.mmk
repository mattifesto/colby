--- h1
CBModel Interfaces
---

--- dl
    --- dt
    CBModel_build()
    ---
    ---
    This function takes a spec as a parameter and returns a model.
    ---

    --- dt
    CBModel_toID()
    ---
    --- dd
    Implement this interface if you want to enable model imports without the
    importer having to specify IDs explicitly for the models.

    This function takes a spec as a parameter and generates an ID for the spec
    base on the spec's properties.
    ---

    --- dt
    CBModel_upgrade()
    ---
    --- dd
    (Spec Upgrades (b))

    If properties on existing specs need to be changed, they should be changed
    in this function.

    (Model Upgrades (b))

    When the build process changes and existing models need to be rebuilt, use
    this interfaces to add a (buildProcessVersionNumber (code)) property with a
    value of 2 to the spec and increment that number in future version where the
    build process has changed again.

    It is not necessary to propagate this variable to the model unless you know
    you have a need for it.
    ---
---
