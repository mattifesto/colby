<?php

/**
 * 2015.08.02
 * @deprecated This file uses deprecated technology that won't work but it is
 * a good example for how to create an RSS feed and the techniques should be
 * transferred to a new working example.
 */

header('Content-type: application/rss+xml');

$blogPostsGroupId = '37151457af40ee706cc23de4a11e7ebacafd0c10';

$document = new DOMDocument('1.0', 'utf-8');
$document->formatOutput = true;

$rss = $document->appendChild(new DOMElement('rss'));
$rss->setAttribute('version', '2.0');
$rss->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:atom', 'http://www.w3.org/2005/Atom');
$rss->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:content', 'http://purl.org/rss/1.0/modules/content/');

$channel = $rss->appendChild(new DOMElement('channel'));

appendChildWithText($channel, 'title', CBSitePreferences::siteName() . ' Blog Posts');
appendChildWithText($channel, 'link', cbsiteurl() . '/blog/');
appendChildWithText($channel, 'description', CBSitePreferences::siteName() . ' Blog Posts');
appendChildWithText($channel, 'language', 'en-us');

$link = $channel->appendChild(new DOMElement('atom:link', null, 'http://www.w3.org/2005/Atom'));
$link->setAttribute('href', cbsiteurl() . '/blog/rss/');
$link->setAttribute('rel', 'self');
$link->setAttribute('type', 'application/rss+xml');

$sql = <<<EOT
SELECT
    LOWER(HEX(`archiveId`)) AS `archiveId`,
    `URI`,
    `titleHTML`,
    `subtitleHTML`,
    `published`
FROM
    `ColbyPages`
WHERE
    `groupId` = UNHEX('{$blogPostsGroupId}') AND
    `published` IS NOT NULL
ORDER BY
    `published` DESC
LIMIT
    20
EOT;

$result = Colby::query($sql);

if ($result->num_rows > 0)
{
    while ($row = $result->fetch_object())
    {
        $item = $channel->appendChild(new DOMElement('item'));

        $title = htmlspecialchars_decode($row->titleHTML, ENT_QUOTES);
        $subtitle = htmlspecialchars_decode($row->subtitleHTML, ENT_QUOTES);
        $url = cbsiteurl() . "/{$row->URI}/";
        $published = gmdate(DateTime::RSS, $row->published);

        appendChildWithText($item, 'title', $title);
        appendChildWithText($item, 'description', $subtitle);
        appendChildWithText($item, 'pubDate', $published);
        appendChildWithText($item, 'guid', $url);
        appendChildWithText($item, 'link', $url);
    }
}

$result->free();

echo $document->saveXML();

function appendChildWithText($parent, $name, $text)
{
    $element = $parent->appendChild(new DOMElement($name));
    $element->appendChild(new DOMText($text));
}
