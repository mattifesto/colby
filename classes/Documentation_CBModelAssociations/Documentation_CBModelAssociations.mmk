Associations can be stored inside models, but sometimes its better to use the
CBModelAssociations table to store associations.

--- h1
History
---

The CBModelAssociations table came to exist rather late in the development of
Colby. Situations almost randomly started to occur in unrelated pieces of code
that required some sort of an association table. At first these situations were
handle by creating custom association tables. This quickly got out of hand and
the similarities between the custom tables became obvious.

Organically, the shape and nature of the CBModelAssociations table was developed
and more serious thought was given to the simplicity vs. complexity of such a
table and why the table was a necessary part of Colby.

--- h1
About
---

The table associates two models or potential models, because both models don't
have to technically exist, with the class name of a class that manages a
specific type of association. Having said this, both models eventually should
exist, however in the middle of a model import the models not yet imported
obviously won't exist.

The table has an ID column and an associatedID column, however the association
flows in both directions and neither column, at this point, is considered
always represent a more dominant or primary model.

--- h1
Reasons for this table
---


--- ul

    When you have a lot of associations, the CBModelAssociations table is better
    at storing, reading, and changing them than a model.

    Associations can more easily be made out of order in the associations table.
    To make associations inside models usually requires that both models already
    exist, which will not be the case when the first model is imported.

    It is easier to do transfers of associations using the CBModelAssociations
    table. If you have a product that was in product group 1 and it changes to
    product group 2, you can easily get the previous association before changing
    the association. Then start a task to update both of the pages associated
    with each product group that has changed.

    Imported models should not be edited. If an imported menu model is updated
    by imported items to contain a list of its menu items, when the menu is
    imported again it will lose that list.

    To expand upon: There is something that just feels right about using the
    CBModelAssociations table. I'm having a hard time coming up with the full
    technical reason for this. This document will be updated as the thoughts
    around this topic develop.

---
