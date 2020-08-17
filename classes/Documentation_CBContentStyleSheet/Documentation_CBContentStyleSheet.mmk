--- h1
Heading Elements
---

--- Documentation_CBContentStyleSheet_example
    --- div
        --- h1
        Heading 1
        ---
        --- h2
        Heading 2
        ---
        --- h3
        Heading 3
        ---
        --- h4
        Heading 4
        ---
        --- h5
        Heading 5
        ---
        --- h6
        Heading 6
        ---

        This is a paragraph.

        --- h1
        This a very long heading 1 that should wrap and form multiple lines. It
        is not normal to have a heading quite this long and yet it happens
        fairly frequently and should be considered a valid scenario.
        ---

        This is a paragraph.

        --- h2
        Heading 2
        ---

        This is a paragraph.

        --- h3
        Heading 3
        ---

        This is a paragraph.

        --- section
            --- h1
            Heading 1 In Section
            ---
            --- section
                --- h1
                Heading 1 In Section In Section
                ---
            ---
        ---
    ---
---

--- h1
Dictionary Lists
---

A <dl> should have large top and bottom margins. Paragraphs in a <dt> should
have no margins. Paragraphs in a <dd> should have the default paragraph margins.

--- Documentation_CBContentStyleSheet_example
    --- div
        --- dl
            --- dt
            term 1
            ---
            --- dd
            definition 1
            ---
            --- dt
            term 2
            ---
            --- dd
            definition 1
            ---
            --- dd
            definition 2
            ---
            --- dd
            definition 3 paragraph 1

            definition 3 paragraph 2
            ---
        ---
    ---
---

Two paragraphs in a single <dt> should display with no margins. Two paragraphs
in a single <dd> should have the default paragraph margins.

--- Documentation_CBContentStyleSheet_example
    --- div
        --- dl
            --- dt
            term 1

            term 2
            ---
            --- dd
            definition 1

            definition 2
            ---
        ---
    ---
---



--- h1
Unordered and Ordered Lists
---

--- Documentation_CBContentStyleSheet_example
    --- div
        --- ul
            one

            two

            --- li
            three one

            three two

            three three
            ---
        ---
        --- ol
            one

            two

            --- li
            three one

            three two

            three three
            ---
        ---
    ---
---
