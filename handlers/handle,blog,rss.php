<?php

header('Content-type: application/rss+xml');

$blogPostsGroupId = '37151457af40ee706cc23de4a11e7ebacafd0c10';

$document = new DOMDocument('1.0', 'utf-8');
$document->formatOutput = true;

$rss = $document->appendChild(new DOMElement('rss'));
$rss->setAttribute('version', '2.0');
$rss->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:atom', 'http://www.w3.org/2005/Atom');
$rss->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:content', 'http://purl.org/rss/1.0/modules/content/');

$channel = $rss->appendChild(new DOMElement('channel'));

appendChildWithText($channel, 'title', COLBY_SITE_NAME . ' Blog Posts');
appendChildWithText($channel, 'link', COLBY_SITE_URL . '/blog/');
appendChildWithText($channel, 'description', COLBY_SITE_NAME . ' Blog Posts');
appendChildWithText($channel, 'language', 'en-us');

$link = $channel->appendChild(new DOMElement('atom:link', null, 'http://www.w3.org/2005/Atom'));
$link->setAttribute('href', COLBY_SITE_URL . '/blog/rss/');
$link->setAttribute('rel', 'self');
$link->setAttribute('type', 'application/rss+xml');

$sql = <<<EOT
SELECT
    LOWER(HEX(`archiveId`)) AS `archiveId`,
    `stub`,
    `titleHTML`,
    `subtitleHTML`,
    UNIX_TIMESTAMP(`published`) as `published`
FROM
    `ColbyPages`
WHERE
    `groupId` = UNHEX('{$blogPostsGroupId}') AND
    `published` IS NOT NULL
ORDER BY
    `published` DESC
EOT;

$result = Colby::query($sql);

if ($result->num_rows > 0)
{
    while ($row = $result->fetch_object())
    {
        $item = $channel->appendChild(new DOMElement('item'));

        appendChildWithText($item, 'title', $row->titleHTML);
        appendChildWithText($item, 'guid', COLBY_SITE_URL . "/{$row->stub}/");
        appendChildWithText($item, 'link', COLBY_SITE_URL . "/{$row->stub}/");
        appendChildWithText($item, 'description', $row->subtitleHTML);
        appendChildWithText($item, 'pubDate', gmdate(DateTime::RSS, $row->published));
    }
}

$result->free();

echo $document->saveXML();

function appendChildWithText($parent, $name, $text)
{
    $element = $parent->appendChild(new DOMElement($name));
    $element->appendChild(new DOMText($text));
}
