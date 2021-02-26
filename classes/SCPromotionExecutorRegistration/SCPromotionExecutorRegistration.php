<?php

final class SCPromotionExecutorRegistration {

    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $promotionSpec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $registrationSpec
    ): stdClass {
        $title = CBConvert::stringToCleanLine(
            CBModel::valueToString(
                $registrationSpec,
                'title'
            )
        );

        $descriptionCBMessage = CBModel::valueToString(
            $registrationSpec,
            'descriptionCBMessage'
        );

        $executorClassName = CBModel::valueAsName(
            $registrationSpec,
            'executorClassName'
        );

        if ($executorClassName === null) {
            throw new CBExceptionWithValue(
                'The "executorClassName" property on this spec is not valid.',
                $registrationSpec,
                'f0f7bee157ba6bcceda3631e15c5924d27d103d7'
            );
        }

        return (object)[
            'title' => $title,
            'descriptionCBMessage' => $descriptionCBMessage,
            'executorClassName' => $executorClassName,
        ];
    }
    /* CBModel_build() */



    /* -- accessors -- */



    /**
     * @param object $promotionExecutorRegistrationSpec
     * @param string $cbmessage
     *
     * @return void
     */
    static function
    setDescriptionCBMessage(
        stdClass $promotionExecutorRegistrationSpec,
        string $cbmessage
    ): void {
        $promotionExecutorRegistrationSpec->descriptionCBMessage = (
            $cbmessage
        );
    }
    /* setDescriptionCBMessage() */



    /**
     * @param object $promotionExecutorRegistrationSpec
     * @param string $executorClassName
     *
     * @return void
     */
    static function
    setExecutorClassName(
        stdClass $promotionExecutorRegistrationSpec,
        string $executorClassName
    ): void {
        $promotionExecutorRegistrationSpec->executorClassName = (
            $executorClassName
        );
    }
    /* setExecutorClassName() */



    /**
     * @param object $promotionExecutorRegistrationSpec
     * @param string $title
     *
     * @return void
     */
    static function
    setTitle(
        stdClass $promotionExecutorRegistrationSpec,
        string $title
    ): void {
        $promotionExecutorRegistrationSpec->title = (
            $title
        );
    }
    /* setTitle() */

}
