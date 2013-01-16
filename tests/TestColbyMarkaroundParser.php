<?php

// Simple paragraphs

$markaround = "Hello";
$expected = "<p>Hello\n";
$actual = ColbyConvert::markaroundToHTML($markaround);

ColbyUnitTests::verifyActualStringIsExpected($actual, $expected);

// Unordered lists

$markaround = "-Hello";
$expected = "<ul>\n<li><p>Hello\n</ul>\n";
$actual = ColbyConvert::markaroundToHTML($markaround);

ColbyUnitTests::verifyActualStringIsExpected($actual, $expected);
