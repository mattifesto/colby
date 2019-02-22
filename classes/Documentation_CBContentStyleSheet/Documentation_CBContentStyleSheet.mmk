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
            definition 2
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
