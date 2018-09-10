<?php

final class CBArtworkElementDocumentation {

    /**
     * @return string
     */
    static function CBAdmin_group(): string {
        return 'Developers';
    }

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'help',
        ];
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'CBArtworkElement Documentation';

        $CSS = <<<EOT

            main.CBUIRoot {
                background-color: var(--CBBackgroundColor);
            }

            main .CBArtworkElement {
                background-color: red;
                margin-bottom: 50px;
            }

EOT;

        CBHTMLOutput::addCSS($CSS);

        CBView::renderSpec((object)[
            'className' => 'CBPageTitleAndDescriptionView',
        ]);

        $URL = CBTestAdmin::testImageURL();
        $message = <<<EOT

            A CBArtworkElement uses a set of 3 elements to display an image with
            the following features:

            --- ul
            It has fixed aspect ratio.

            It prevents distracting element resize after image loads.

            The image will scale down properly when space is limited in small
            parent elements, windows, or devices.

            It can be use to easily display images with different intrinsic
            aspect ratios in containers with a common aspect ratio.

            It cleanly supports the common situation of the intrinsic image size
            not matching the image rendering size exactly or even in multiples
            of two or three.
            ---

            CBArtworkElements, like (div (code)) elements, will expand to fill
            whatever width is available. If that behavior is not desired, the developer
            can specify maximum height and/or width.

            The CBArtworkElement does not know or care about the intrinsic size
            of the image it displays. The developer is encourage to provide an
            image of the "appropriate" size for the scenario. For instance, if
            a CBArtworkElement has a maximum display width of 800px, an image
            asset roughly two times that, 1600 pixels wide, would be
            appropriate.

            Even if the element ends up showng smaller than the maximum size,
            the developer's original intention is to provide that level of
            detail which is just as important when zooming the image on a phone.

            Colby image features make it extremely easy to generate images of
            different sizes than were originally uploaded to be used as the
            image asset for a CBArtworkElement.

            --- h2
            Outer Element
            ---

            A CBArtworkElement has no intrinsic width because it does not care
            about its image's intrinsic size. The outer element's styles provide
            the CBArtworkElement with an intrinsic width which is important if
            it is contained within a flexible box, a very common scenario.

            --- ul
                --- li
                (width: <calculated max width>px | 100vh (code))

                This property is always set to give the CBArtworkElement an
                intrinsic width. Setting the (width (code)) property, not the
                (max-width (code)), gives an element an intrinsic width.

                The maxHeight and/or maxWidth parameters  are used to calculate
                the value for this property in CSS pixel units. If neither
                parameter is provided, the property will be set to "100vh".
                ---

                --- li
                (max-width: 100%; (code))

                This property is set in (CBArtworkElement.css (code)) to 100%
                which allows the element to shrink to fit inside its containing
                element.
                ---
            ---

            --- h2
            Inner Element
            ---

            The inner element gives the CBArtworkElement its aspect ratio.

            --- ul
                --- li
                (padding-bottom: <calculated>% (code))

                This property is set to a percentage to give the inner element
                an aspect ratio. Without this property the CBArtworkElement
                would have no height.
                ---

                --- li
                (overflow: hidden; (code))

                This property is set so that browsers that don't support
                object-fit will not display any parts of the image outside the
                bounds of this element.

                ---

                --- li
                (position: relative; (code))

                Because the (padding-bottom (code)) property is set to give this
                element its shape, the (img (code)) child element is absolutely
                placed within it.
                ---
            ---

            --- h2
            Image Element
            ---

            This element is an (img (code)) element that displays the image.

            --- ul
                --- li
                (position: absolute; (code)) ((br))
                (top: 0; (code)) ((br))
                (left: 0; (code)) ((br))
                (width: 100%; (code)) ((br))
                (height: 100%; (code)) ((br))

                These properties are all set to properly position the (img
                (code)) element inside of the inner element. Absolute
                positioning must be used because the inner element is given its
                shape using (padding-bottom (code)).
                ---

                --- li
                (object-fit: contain; (code))

                This will render the image contained and centered inside the img
                element's bounds.
                ---
            ---
EOT;

        CBView::renderSpec((object)[
            'className' => 'CBMessageView',
            'markup' => $message,
        ]);

        $message = <<<EOT

            --- h1
            Examples
            ---

            The image used in the following examples has an intrinsic size to
            1600 pixels by 900 pixels. Here is a direct (link (a {$URL})) to the
            image.

            Below an artwork element rendered using the following parameters:

            --- ul
            aspectRatioWidth: 16

            aspectRatioHeight: 9
            ---

            No maximum width or maximum height is specified so the image will always
            fill the entire available width regardles of the intrinsic image size.

EOT;

        CBView::renderSpec((object)[
            'className' => 'CBMessageView',
            'markup' => $message,
        ]);

        CBArtworkElement::render([
            'height' => 9,
            'width' => 16,
            'URL' => $URL,
        ]);

        $message = <<<EOT

        Below an artwork element rendered using the following parameters:

        --- ul
        aspectRatioWidth: 32

        aspectRatioHeight: 9

        maxWidth: 640
        ---

        The intrinsic image aspect ratio is not 32 by 9 and since the image is
        contained within the CBArtworkElement aspect ratio the CBArtworkElement
        has whitespace areas to the right and left.

        Note: The CBArtworkElement whitespace areas are displayed with a red
        background in these examples.

        Note: CBArtworkElements are meant to be contained in views or other HTML
        that will display them in a layout context so they have no default
        layout behavior, such as centering.

EOT;

        CBView::renderSpec((object)[
            'className' => 'CBMessageView',
            'markup' => $message,
        ]);

        CBArtworkElement::render([
            'height' => 9,
            'width' => 32,
            'maxWidth' => 640,
            'URL' => $URL,
        ]);

        $message = <<<EOT

        Below an artwork element rendered using the following parameters:

        --- ul
        aspectRatioWidth: 1

        aspectRatioHeight: 1

        maxWidth: 640
        ---

        The intrinsic image aspect ratio is not 1 by 1 and since the image is
        contained within the CBArtworkElement aspect ratio the CBArtworkElement
        has whitespace areas on the top and bottom.

EOT;

        CBView::renderSpec((object)[
            'className' => 'CBMessageView',
            'markup' => $message,
        ]);

        CBArtworkElement::render([
            'height' => 1,
            'width' => 1,
            'maxWidth' => 640,
            'URL' => $URL,
        ]);
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(CBHelpAdminMenu::ID());

        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'CBArtworkElement',
            'text' => 'CBArtworkElement',
            'URL' => '/admin/?c=CBArtworkElementDocumentation',
        ];

        CBDB::transaction(function () use ($spec) {
            CBModels::save($spec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBHelpAdminMenu'];
    }
}
