<?php

$response = new ColbyOutputManager('ajax-response');
$response->begin();


include_once CBSystemDirectory . '/classes/CBDataStore.php';
include_once CBSystemDirectory . '/classes/CBPages.php';
include_once CBSystemDirectory . '/templates/CBBlogPostPageTemplate.php';


/**
 *
 */

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    $response->message = 'You are not authorized to use this feature.';

    goto done;
}

/**
 *
 */

CBTestPagesGenerator::initialize();

Colby::mysqli()->autocommit(false);

for ($i = 0; $i < 10; $i++)
{
    /**
     * 90% of pages with timestamps will be published.
     */

    $dataStoreID                        = Colby::uniqueSHA1Hash();
    $pageModel                          = json_decode(CBBlogPostPageTemplateModelJSON);
    $pageModel->dataStoreID             = $dataStoreID;
    $pageModel->title                   = CBTestPagesGenerator::randomPhrase(8);
    $pageModel->titleHTML               = ColbyConvert::textToHTML($pageModel->title);
    $pageModel->description             = CBTestPagesGenerator::randomPhrase(20);
    $pageModel->descriptionHTML         = ColbyConvert::textToHTML($pageModel->description);
    $pageModel->URI                     = "test42/{$dataStoreID}";
    $pageModel->URIIsStatic             = true;
    $pageModel->publicationTimeStamp    = CBTestPagesGenerator::randomPublicationTimeStamp();
    $pageModel->isPublished             = $pageModel->publicationTimeStamp ? (mt_rand(0, 9) ? true : false) : false;
    $pageModel->publishedBy             = ColbyUser::currentUserId();


    /**
     *
     */

    $rowData                    = CBPages::insertRow($dataStoreID);
    $rowData->typeID            = CBPageTypeID;
    $rowData->groupID           = $pageModel->groupID;
    $rowData->titleHTML         = $pageModel->titleHTML;
    $rowData->descriptionHTML   = $pageModel->descriptionHTML;
    $rowData->published         = $pageModel->isPublished ? $pageModel->publicationTimeStamp : null;
    $rowData->publishedBy       = $pageModel->publishedBy;
    $rowData->searchText        = CBTestPagesGenerator::searchTextForPage($pageModel);
    CBPages::updateRow($rowData);
    CBPages::tryUpdateRowURI($rowData->rowID, $pageModel->URI);

    $pageModel->rowID = $rowData->rowID;


    /**
     *
     */

    $dataStore  = new CBDataStore($dataStoreID);
    $dataStore->makeDirectory();

    $pageModelJSON      = json_encode($pageModel);
    $dataStoreDirectory = $dataStore->directory();
    file_put_contents("{$dataStoreDirectory}/model.json", $pageModelJSON, LOCK_EX);

    Colby::mysqli()->commit();
}

$response->wasSuccessful = true;

done:

$response->end();

/**
 *
 */
class CBTestPagesGenerator
{
    private static $words           = array();
    private static $countOfWords    = 0;

    /**
     * @return void
     */
    public static function initialize()
    {
        self::$words        = file('/usr/share/dict/words', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        self::$countOfWords = count(self::$words);
    }

    /**
     * @return string
     */
    public static function randomPhrase($maxCountOfWords)
    {
        $countOfWords = mt_rand(1, $maxCountOfWords);

        $phrase = array();

        for ($i = 0; $i < $countOfWords; $i++)
        {
            $phrase[] = self::randomWord();
        }

        return implode(' ', $phrase);
    }

    /**
     *  @return int | null
     *
     *  Returns a timestamp 90% of the time and null 10% of the time.
     */
    public static function randomPublicationTimestamp()
    {
        if (mt_rand(0, 9))
        {
            return mt_rand(/* 1850/01/01 */ -3786825600, time());
        }

        return null;
    }


    /**
     * @return string
     */
    public static function randomWord()
    {
        $index = mt_rand(0, self::$countOfWords - 1);

        return self::$words[$index];
    }

    /**
     * @return string
     */
    public static function searchTextForPage($pageModel)
    {
        $searchText[] = $pageModel->title;
        $searchText[] = $pageModel->description;

        global $CBSectionSnippetsForSearchText;

        foreach ($pageModel->sectionModels as $sectionModel)
        {
            if (isset($CBSectionSnippetsForSearchText[$sectionModel->sectionTypeID]))
            {
                ob_start();

                include $CBSectionSnippetsForSearchText[$sectionModel->sectionTypeID];

                $searchText[] = ob_get_clean();
            }
        }

        return implode(' ', $searchText);
    }
}
