<?php

if (ColbyUser::current()->isOneOfThe('Administrators'))
{
    ?>

    <li><h1>General</h1></li>

    <li><a href="/admin/">Status</a></li>

    <li><h1>Documents</h1></li>

    <li><a href="/admin/pages/">Pages</a></li>
    <li><a href="/admin/blog/">Blog Posts</a></li>

    <li><h1>Help</h1></li>

    <li><a href="/admin/help/markaround-syntax/">Markaround</a></li>
    <li><a href="/admin/help/title-subtitle/">
        Title <span style="font-size: 0.8em;">&amp;</span> Subtitle
    </a></li>
    <li><a href="/admin/help/caption-alternative-text/">
        Caption <span style="font-size: 0.8em;">&amp;</span> Alt Text
    </a></li>

    <?php
}

if (ColbyUser::current()->isOneOfThe('Developers'))
{
    ?>

    <li><h1>Users</h1></li>

    <li><a href="/admin/users/">Permissions</a></li>

    <li><h1>Developers</h1></li>

    <li><a href="/developer/update/">Update</a></li>
    <li><a href="/admin/documents/">Documents</a></li>
    <li><a href="/developer/groups/">Document Groups</a></li>
    <li><a href="/developer/models/">Document Types</a></li>
    <li><a href="/developer/mysql/">MySQL</a></li>
    <li><a href="/developer/test/">Tests</a></li>

    <li><h1>Performance Tests</h1></li>

    <li><a href="/developer/performance-tests/mysql-vs-colbyarchive/">MySQL vs ColbyArchive</a></li>

    <?php
}

