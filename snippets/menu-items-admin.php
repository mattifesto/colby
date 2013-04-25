<?php

if (ColbyUser::current()->isOneOfThe('Administrators'))
{
    ?>

    <li><h1>Administrators</h1></li>

    <li><a href="/admin/pages/">Pages</a></li>
    <li><a href="/admin/blog/">Blog</a></li>

    <li><h1>Help</h1></li>

    <li><a href="/admin/help/markaround-syntax/">Markaround</a></li>

    <?php
}

if (ColbyUser::current()->isOneOfThe('Developers'))
{
    ?>

    <li><h1>Developers</h1></li>

    <li><a href="/developer/archives/">Archives</a></li>
    <li><a href="/developer/groups/">Document Groups</a></li>
    <li><a href="/developer/models/">Document Types</a></li>
    <li><a href="/developer/test/">Tests</a></li>

    <?php
}

