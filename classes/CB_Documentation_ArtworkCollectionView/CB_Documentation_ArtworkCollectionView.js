/* global
    CBMessageView,
*/


(function () {
    "use strict";

    let mainElement = document.getElementsByTagName(
        "main"
    )[0];

    {
        let messageViewElement = CBMessageView.create();

        messageViewElement.CBMessageView_setCBMessage(`

            This view shows a multple image viewer. When each thumbnail is
            clicked its image is show in the main viewer.

        `);

        mainElement.append(
            messageViewElement
        );
    }

})();
