<?php

if (isset($archive))
{
    $page = ColbyOutputManager::beginPage($archive->data()->titleHTML,
                                          $archive->data()->subtitleHTML);
}
else
{
    // If this URL handler is called directly, it's because a verified user wants to preview a post.
    // So we get the post and display the page, but only for verified users.
    // This method will display both published and unpublished posts.
    // TODO: Can this work be generalized for all types of blog posts?

    if (!isset($_GET['archive-id']))
    {
        return false;
    }

    $archive = ColbyArchive::open($_GET['archive-id']);

    $page = ColbyOutputManager::beginVerifiedUserPage($archive->data()->titleHTML,
                                                      $archive->data()->subtitleHTML);
}

$data = $archive->data();
$model = ColbyPageModel::modelWithData($data);
$javascriptPublicationTimestamp = $model->isPublished() ? $model->publicationDate() * 1000 : '';
$publicationDateTime = $model->isPublished() ? date(DateTime::RFC3339, $model->publicationDate()) : '';
$publicationDateText = $model->isPublished() ? date('Y/m/d g:i A T', $model->publicationDate()) : 'not published';

$publishedBy = 'not published';

if ($model->isPublished())
{
    $row = ColbyUser::userRow($model->publishedBy());

    $publishedBy = $row->facebookName;
}

$borderColor = 'rgba(128, 0, 128, 0.3)';

?>

<link href='http://fonts.googleapis.com/css?family=Open+Sans+Condensed:700' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Gentium+Basic:400,400italic' rel='stylesheet' type='text/css'>

<section id="document">

    <style scoped="scoped">
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
            padding: 10px 100px;
            border-color: <?php echo $borderColor; ?>;
            border-style: solid;
            border-width: 1px 0px 1px;
            margin: 2.0em 0.0em 4.0em;
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

    <h1><?php echo $archive->data()->titleHTML; ?></h1>

    <h2><?php echo $archive->data()->subtitleHTML; ?></h2>

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

    <div class="formatted-content"><?php echo $archive->data()->contentHTML; ?></div>

</section>
<?php

$page->end();
