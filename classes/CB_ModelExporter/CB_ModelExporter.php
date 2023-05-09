<?php

final class
CB_ModelExporter
{
    // -- functions



    /**
     * @param object $modelArgument
     *
     * @return array
     */
    static function
    getDirectlyRequiredModelCBIDs(
        stdClass $modelArgument
    ): array
    {
        $callable =
        CBModel::getClassFunction(
            $modelArgument,
            'CB_ModelExporter_getDirectlyRequiredModelCBIDs'
        );

        if (
            $callable === null
        ) {
            $message =
            CBConvert::stringToCleanLine(
                <<<EOT

                    This model does not support export.

                EOT
            );

            $exception =
            new CBExceptionWithValue(
                $message,
                $modelArgument,
                'dee38e0031d9e90ba5717629abc447cab7d220d1'
            );

            throw $exception;
        }

        $message =
        CBConvert::stringToCleanLine(
            <<<EOT

                This function has not been implemented yet.

            EOT
        );

        $exception =
        new CBExceptionWithValue(
            $message,
            $modelArgument,
            'bddbc729dde42e496a7c3e36e1b28d0bb723485a'
        );

        throw $exception;
    }
    // getDirectlyRequiredModelCBIDs()

}
