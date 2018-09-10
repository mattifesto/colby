
--- h1
Two Element Layout Model
---
Most views present some sort of content, and most of those views should use the
two element layout model to create a flexible layout which provides the best
layout behavior in any scenario, particularly the scenarios encountered in
responsive design.

The two elements are the (root element (b)), and its only child, the (content
element (b)). The (content element (b)) will contain the elements that present the content
of the view.

The (root element (b)) will consume the available width provided by its parent
element, and the (content element (b)) will have its width property set the intrinsic
width of the view. If there is more width available than the intrinsic width,
the (root element (b)) will center the (content element (b)) horizontally. If the (root element (b)) has less width
available than the intrinsic width, the (root element (b)) is a flexible box that will
shrink the (content element (b)) to fit into the available width.

--- h2
Root element styles
---

--- ul
    --- li
    (display: flex; (code)) ((br))
    (justify-content: center; (code))

    The root view element is a flexible box that centers the content element
    horizontally if there is width available.
    ---

    --- li
    (box-sizing: border-box; (code))

    This prevents padding from expanding the width of the root element to
    greater than 100% of its parent element.
    ---

    --- li
    (max-width: 100%; (code))

    This prevents children with padding from pushing the width of the root
    element wider than 100%.

    (TODO 2018/09/10 This text was copied from the previous documentation
    location and it needs a more detailed scenario description. (small))
    ---

    --- li
    (padding: 20px; (code))

    The root element the best place to apply padding to your view. (20px (code))
    is the standard padding for a view but it can obviously vary to fit a view's
    specific goals.
    ---

---

--- h2
Content element styles
---

--- ul
    --- li
    (width: <intrinsic width>; (code)) ((br))

    This is the place to declare the intrinsic width of your view. It sets the
    intrinsic width of the content and if your view is placed in a flexible box
    this width plus horizontal padding declared on the root element will be used
    as the intrinsic width of the entire view.
    ---
---
