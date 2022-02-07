(function () {

    window.CB_AjaxRequest = {
        setExecutorArguments: CB_AjaxRequest_setExecutorArguments,
        setExecutorClassName: CB_AjaxRequest_setExecutorClassName,
        setExecutorFunctionClassName: (
            CB_AjaxRequest_setExecutorFunctionClassName
        ),
        setExecutorFunctionName: CB_AjaxRequest_setExecutorFunctionName,
    };



    /**
     * @param object ajaxRequestModel
     * @param object executorArguments
     *
     * @return undefined
     */
    function
    CB_AjaxRequest_setExecutorArguments(
        ajaxRequestModel,
        executorArguments
    ) {
        ajaxRequestModel.CB_AjaxRequest_executorArguments_property = (
            executorArguments
        );
    }
    /* CB_AjaxRequest_setExecutorArguments() */



    /**
     * @param object ajaxRequestModel
     * @param string executorClassName
     *
     * @return undefined
     */
    function
    CB_AjaxRequest_setExecutorClassName(
        ajaxRequestModel,
        executorClassName
    ) {
        ajaxRequestModel.CB_AjaxRequest_executorClassName_property = (
            executorClassName
        );
    }
    /* CB_AjaxRequest_setExecutorClassName() */



    /**
     * @param object ajaxRequestModel
     * @param string executorFunctionClassName
     *
     * @return undefined
     */
    function
    CB_AjaxRequest_setExecutorFunctionClassName(
        ajaxRequestModel,
        executorFunctionClassName
    ) {
        ajaxRequestModel.CB_AjaxRequest_executorFunctionClassName_property = (
            executorFunctionClassName
        );
    }
    /* CB_AjaxRequest_setExecutorFunctionClassName() */



    /**
     * @param object ajaxRequestModel
     * @param string executorFunctionName
     *
     * @return undefined
     */
    function
    CB_AjaxRequest_setExecutorFunctionName(
        ajaxRequestModel,
        executorFunctionName
    ) {
        ajaxRequestModel.CB_AjaxRequest_executorFunctionName_property = (
            executorFunctionName
        );
    }
    /* CB_AjaxRequest_setExecutorFunctionName() */

})();
