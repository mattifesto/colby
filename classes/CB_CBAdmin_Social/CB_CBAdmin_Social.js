/* global
    CBAjax,
    CBDevelopersUserGroup,
    CBErrorHandler,
    CBModel,
    Colby,

    CB_CBAdmin_Social_youtubeChannels_jsvariable,
*/


(function ()
{
    "use strict";

    document.addEventListener(
        "DOMContentLoaded",
        function () {
            afterDOMContentLoaded();
        }
    );


    /**
     * @return undefined
     */
    function
    afterDOMContentLoaded(
    ) {
        let element =
        document.getElementsByClassName(
            "CB_CBAdmin_Social_element"
        )[0];

        if (
            CBDevelopersUserGroup.currentUserIsMember() ===
            true
        ) {
            element.append(
                createYouTubeStatusElement()
            );
        }
    }
    /* afterDOMContentLoaded() */



    /**
     * This function renders information for developers.
     *
     * @return Element
     */
    function
    createYouTubeStatusElement(
    ) // -> undefined
    {
        let element = document.createElement(
            "div"
        );

        element.className =
        "CBStatusAdminPage_YouTubeStatus_element";

        (async function () {
            try {
                let youtubeChannelCount =
                CB_CBAdmin_Social_youtubeChannels_jsvariable.length;

                for (
                    let youtubeChannelIndex = 0;
                    youtubeChannelIndex < youtubeChannelCount;
                    youtubeChannelIndex += 1
                ) {
                    let youtubeChannel =
                    CB_CBAdmin_Social_youtubeChannels_jsvariable[
                        youtubeChannelIndex
                    ];

                    let youtubeChannelModelCBID =
                    CBModel.valueAsCBID(
                        youtubeChannel,
                        'CB_CBAdmin_Social_YouTubeChannel_cbid_property'
                    );



                    let youtubeChannelModelTitle =
                    CBModel.valueToString(
                        youtubeChannel,
                        'CB_CBAdmin_Social_YouTubeChannel_title_property'
                    );

                    let titleElement =
                    document.createElement(
                        "div"
                    );

                    element.append(
                        titleElement
                    );

                    titleElement.textContent =
                    youtubeChannelModelTitle;



                    let arrayOfYouTubeStatisticsModels =
                    await
                    CBAjax.call2(
                        'CB_Ajax_YouTube_GetStatistics',
                        {
                            'CB_Ajax_YouTube_GetStatistics_youtubeChannelModelCBID_parameter':
                            youtubeChannelModelCBID,
                        }
                    );

                    let arrayOfYouTubeStatisticsModelsLength =
                    Math.min(
                        arrayOfYouTubeStatisticsModels.length,
                        1
                    );

                    for (
                        let youtubeStatisticsModelIndex =
                        0;

                        youtubeStatisticsModelIndex <
                        arrayOfYouTubeStatisticsModelsLength;

                        youtubeStatisticsModelIndex++
                    ) {
                        let youtubeStatisticsModel =
                        arrayOfYouTubeStatisticsModels[
                            youtubeStatisticsModelIndex
                        ];



                        let statisticsContainerElement =
                        document.createElement(
                            "div"
                        );

                        element.append(
                            statisticsContainerElement
                        );

                        statisticsContainerElement.style.padding =
                        "10px";



                        let viewCount =
                        CBModel.valueAsInt(
                            youtubeStatisticsModel,
                            "CB_YouTubeStatistics_statistics_property." +
                            "items.[0].statistics.viewCount"
                        );

                        let subscriberCount =
                        CBModel.valueAsInt(
                            youtubeStatisticsModel,
                            "CB_YouTubeStatistics_statistics_property." +
                            "items.[0].statistics.subscriberCount"
                        );

                        let videoCount =
                        CBModel.valueAsInt(
                            youtubeStatisticsModel,
                            "CB_YouTubeStatistics_statistics_property." +
                            "items.[0].statistics.videoCount"
                        );

                        let textElement =
                        document.createElement(
                            "div"
                        );

                        statisticsContainerElement.append(
                            textElement
                        );

                        textElement.textContent =
                        `
                            Subscribers: ${subscriberCount}
                            Views: ${viewCount}
                            Videos: ${videoCount}
                        `;

                        let unixTimestamp =
                        CBModel.valueAsInt(
                            youtubeStatisticsModel,
                            "CB_YouTubeStatistics_cbtimestamp_property." +
                            "CB_Timestamp_unixTimestamp_property"
                        );

                        let timeElement =
                        Colby.unixTimestampToElement(
                            unixTimestamp,
                            undefined,
                            "Colby_time_element_style_compact"
                        );

                        timeElement.style.color =
                        "var(--CBTextColor3)";

                        timeElement.style.fontSize =
                        "80%";

                        statisticsContainerElement.append(
                            timeElement
                        );
                    }
                    // for statistics

                }
                // for channel

            }
            // try

            catch (
                error
            ) {
                CBErrorHandler.report(
                    error
                );
            }
        })();

        return element;
    }
    /* createYouTubeStatusElement() */

}
)();
