<?php

$page = ColbyOutputManager::beginVerifiedUserPage('Blog Post Types',
                                                  'Developer tools for blog post types.',
                                                  'admin');

?>

<h1>Built in post types</h1>

<?php

$absoluteDataFilenames = glob(COLBY_SITE_DIRECTORY . '/colby/handlers/handle,admin,blog,*.data');

displayPostTypes($absoluteDataFilenames);

?>

<h1>Site specific post types</h1>

<?php

$absoluteDataFilenames = glob(COLBY_SITE_DIRECTORY . '/handlers/handle,admin,blog,*.data');

displayPostTypes($absoluteDataFilenames);

?>

<div><a href="<?php echo "{$_SERVER['REQUEST_URI']}/edit/"; ?>">Create a new blog post type</a></div>

<?php

$page->end();

/**
 * @return void
 */
function displayPostTypes($absoluteDataFilenames)
{
    foreach ($absoluteDataFilenames as $absoluteDataFilename)
    {
        preg_match('/blog,([^,]*).data$/', $absoluteDataFilename, $matches);

        $editURL = COLBY_SITE_URL . "/development/blog-post-types/edit/?blog-post-type-id={$matches[1]}";

        $data = unserialize(file_get_contents($absoluteDataFilename));

        ?>

        <h1 style="font-size: 1.5em;"><?php echo $data->nameHTML; ?></h1>
        <p><?php echo $data->descriptionHTML; ?>
        <p><a href="<?php echo $editURL; ?>">edit</a>

        <?php
    }
}
