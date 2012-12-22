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
$publicationJavascriptTimestamp = $model->isPublished() ? $model->publicationDate() * 1000 : '';
$publicationDateTime = $model->isPublished() ? date(DateTime::RFC3339, $model->publicationDate()) : '';
$publicationDateText = $model->isPublished() ? date('Y/m/d g:i A T', $model->publicationDate()) : 'not published';

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
            margin-bottom: 1.2em;
            color: #222222;
            font-family: 'Open Sans Condensed';
            font-size: 2.0em;
            line-height: 1.2;
        }

        #document h2
        {
            padding: 0px 100px;
            margin-bottom: 50px;
            color: purple;
            font-family: 'Gentium Basic', serif;
            font-size: 1.0em;
            font-style: italic;
            font-weight: normal;
            text-align: center;
        }

        #document time
        {
            margin-bottom: 20px;
            float: right;
        }

        div.formatted-content
        {
            color: #444444;
            font-family: 'Gentium Basic', serif;
        }
    </style>

    <time datetime="<?php echo $publicationDateTime; ?>">Posted:
        <span class="time"
              data-timestamp="<?php echo $publicationJavascriptTimestamp; ?>">
            <?php echo $publicationDateText; ?>
        </span>
    </time>

    <h1><?php echo $archive->data()->titleHTML; ?></h1>

    <h2><?php echo $archive->data()->subtitleHTML; ?></h2>

    <div class="formatted-content"><?php echo $archive->data()->contentHTML; ?></div>

</section>
<?php

$page->end();
