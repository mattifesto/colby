/* global
    CBDevelopersUserGroup_currentUserIsAMember_jsvariable,
*/


(function ()
{
    "use strict";



    let CBDevelopersUserGroup =
    {
        currentUserIsMember:
        CBDevelopersUserGroup_currentUserIsAMember,
    };

    window.CBDevelopersUserGroup =
    CBDevelopersUserGroup;



    /**
     * @return bool
     */
    function
    CBDevelopersUserGroup_currentUserIsAMember(
    ) // -> bool
    {
        return CBDevelopersUserGroup_currentUserIsAMember_jsvariable;
    }
    // CBDevelopersUserGroup_currentUserIsAMember()n

})();
