<?php

$query = isset($_GET['query']) ? $_GET['query'] : null;

$title = 'Search';

if ($query)
{
    $title = "{$title}: {$query}";
}

$page = ColbyOutputManager::beginPage($title, 'Search for site content.');

?>

<p><?php echo $query; ?>

<?php

$page->end();
