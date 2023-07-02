<?php

final class
CB_ErrorTextGenerator
{
    /**
     * @param Throwable $throwable
     *
     * @return object
     */
    static function
    generateErrorText(
        Throwable $throwableArgument
    ): string
    {
        $chronologicalThrowableArray =
        [];

        $throwable =
        $throwableArgument;

        while (
            $throwable !==
            null
        ) {
            array_unshift(
                $chronologicalThrowableArray,
                $throwable
            );

            $throwable =
            $throwable->getPrevious();
        }

        $text =
        '';

        $throwableNumber =
        1;

        foreach(
            $chronologicalThrowableArray as
            $throwable
        ) {
            $text .=
            "\n\n\n" .
            " - - - - - Throwable #{$throwableNumber} - - - - - " .
            "\n\n\n";

            $chronologicalTraceArray =
            array_reverse(
                $throwable->getTrace()
            );

            $text .=
            json_encode(
                $chronologicalTraceArray,
                JSON_PRETTY_PRINT
            );

            $text .=
            "\n\n" .
            $throwable->getMessage() .
            "\n" .
            $throwable->getFile() .
            "\nLine: " .
            $throwable->getLine();

            $throwableNumber +=
            1;
        }

        return $text;
    }
    // generateErrorText()

}
