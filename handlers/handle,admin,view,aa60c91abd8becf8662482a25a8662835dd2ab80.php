<?php // View for a blog post with no images

$page = new ColbyOutputManager();

if (isset($_GET['action']) && 'preview' == $_GET['action'])
{
    if (!ColbyUser::current()->isOneOfThe('Administrators'))
    {
        $page->titleHTML = 'Authorization Required';
        $page->descriptionHTML = 'You are not authorized to view this page.';

        $page->begin();

        include Colby::findSnippet('authenticate.php');

        goto done;
    }

    $archive = ColbyArchive::open($_GET['archive-id']);
}

$page->titleHTML = $archive->valueForKey('titleHTML');
$page->descriptionHTML = $archive->valueForKey('subtitleHTML');

$isPublished = $archive->valueForKey('isPublished');
$publicationDate = $archive->valueForKey('publicationDate');

$javascriptPublicationTimestamp = $isPublished ? $publicationDate * 1000 : '';
$publicationDateTime = $isPublished ? date(DateTime::RFC3339, $publicationDate) : '';
$publicationDateText = $isPublished ? date('Y/m/d g:i A T', $publicationDate) : 'not published';

$publishedBy = 'not published';

if ($isPublished)
{
    $row = ColbyUser::userRow($archive->valueForKey('publishedBy'));

    $publishedBy = $row->facebookName;
}

$borderColor = 'rgba(128, 0, 128, 0.3)';

?>

<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Gentium+Basic:400,400italic' rel='stylesheet' type='text/css'>

<article id="document">

    <style scoped>
        body
        {
            background-color: #FCFAF2;
        }

        #document
        {
            width: 500px;
            padding: 50px 100px 50px;
            margin: 50px auto;
        }

        #document h1
        {
            color: #222222;
            font-family: 'Open Sans Condensed';
            font-size: 2.0em;
            line-height: 1.2;
        }

        #document h2
        {
            padding: 0.6em 2.0em;
            border-color: <?php echo $borderColor; ?>;
            border-style: solid;
            border-width: 1px 0px 1px;
            margin: 2.0em 4.0em 4.0em;
            color: purple;
            font-family: 'Gentium Basic', serif;
            font-size: 1.0em;
            font-style: italic;
            font-weight: normal;
            text-align: center;
        }

        #published
        {
            width: 150px;
            padding-right: 10px;
            padding-bottom: 30px;
            border-right: 1px solid <?php echo $borderColor; ?>;
            margin-right: 10px;
            margin-bottom: 5px;
            float: left;
            color: purple;
            font-family: 'Gentium Basic', serif;
            font-size: 0.9em;
            line-height: 1.3;
        }

        #published p
        {
            margin: 1.0em 0.0em;
        }

        div.formatted-content
        {
            color: #444444;
            font-family: 'Gentium Basic', serif;
        }
    </style>

    <h1><?php echo $archive->valueForKey('titleHTML'); ?></h1>

    <h2><?php echo $archive->valueForKey('subtitleHTML'); ?></h2>

    <div id="published">
        <p>Published:<br>
            <time data-timestamp="<?php echo $javascriptPublicationTimestamp; ?>"
                  datetime="<?php echo $publicationDateTime; ?>"
                  class="time">
                    <?php echo $publicationDateText; ?>
            </time>
        <p>By:<br>
            <?php echo $publishedBy; ?>
    </div>

    <div class="formatted-content"><?php echo $archive->valueForKey('contentHTML'); ?></div>

</article>

<?php

done:

$page->end();
