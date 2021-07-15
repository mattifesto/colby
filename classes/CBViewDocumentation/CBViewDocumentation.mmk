--- h1
Designing and Developing Views
---

Use a tool like Chrome Device Mode or Safari Responsive Design Mode to test your
views in small viewport scenarios. Use a narrow viewport, such as 75px, to make
sure that your view works properly.

A view won't be more narrow that its left and right padding, so be aware that a
view with 40px left and right padding in a 75px wide viewport will be broken and
the styles probably need to be adjusted.


--- h1
Two Element Layout Model
---
Most views present some sort of content, and most of those views should use the
two element layout model to create a flexible layout which provides the best
layout behavior in any scenario, particularly the scenarios encountered in
responsive design.

The two elements are the (root element (b)), and its only child, the (content
element (b)). The (content element (b)) will contain the elements that present
the content of the view.

The (root element (b)) will consume the available width provided by its parent
element, and the (content element (b)) will have its width property set the
intrinsic width of the view. If there is more width available than the intrinsic
width, the (root element (b)) will center the (content element (b))
horizontally. If the (root element (b)) has less width available than the
intrinsic width, the (root element (b)) is a flexible box that will shrink the
(content element (b)) to fit into the available width.



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

    Although this element's child specifies a max-width of 100%, this element
    must also specify a max-width of 100%. There are a number of reasons the
    child element may try to and be allowed to have a width wider than what is
    available without it.

        --- ul
        because this element is a flexbox

        because the child might have padding

        add other reasons when discovered
        ---
    ---

    --- li
    (overflow-wrap: break-word; (code))

    Long words will overflow their containers and mess up your view layout. This
    declaration will make long words break.
    ---

    --- li
    (padding: 20px; (code))

    The root element the best place to apply padding to your view. (20px (code))
    is the standard padding for a view but it can obviously vary to fit a view's
    specific goals.
    ---

---



--- h3
Root element styles notes
---

--- ul
The class name (CBUI_view (code)) in CBUI.css specifies these styles except for
padding. This class should probably be moved into a CBView provided CSS class
name.

These styles do not the prevent overflow of aberrant view elements in some cases
(see CBContainerView documentation).
---



--- h2
Content element styles
---

--- ul
    --- li
    (max-width: 100%; (code))

    In a scenario where the available width for the content element is less
    than the intrinsic width of the content element, this declaration will make
    sure the content element doesn't overflow the available width.
    ---

    --- li
    (width: <intrinsic width>; (code))

    This is the place to declare the intrinsic width of your view. It sets the
    intrinsic width of the content and if your view is placed in a flexible box
    this width plus horizontal padding declared on the root element will be used
    as the intrinsic width of the entire view.
    ---
---
