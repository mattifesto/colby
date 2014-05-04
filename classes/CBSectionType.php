<?php

/**
 *
 */
class CBSectionType
{
    private $descriptor = null;
    private $directory  = null;
    private $ID         = null;
    private $path       = null;

    /**
     * @param string $sectionTypePath
     *  The path from the web root leading to the section type directory.
     *
     * @return stdClass
     *  The section type descriptor.
     */
    public static function includeFromDirectory($sectionTypePath)
    {
        $sectionType            = new CBSectionType();
        $sectionType->path      = $sectionTypePath;
        $sectionType->directory = CBSiteDirectory . "/{$sectionTypePath}";

        $sectionType->make();

        return $sectionType->descriptor;
    }

    /**
     * @return void
     */
    private function make()
    {
        $this->ID = $this->makeTypeID();

        global $CBSections;

        if (isset($CBSections[$this->ID]))
        {
            return $CBSections[$this->ID];
        }

        $snippetForHTML         = "{$this->directory}/html.php";
        $snippetForHTML         = is_file($snippetForHTML) ? $snippetForHTML : null;
        $snippetForSearchText   = "{$this->directory}/search-text.php";
        $snippetForSearchText   = is_file($snippetForSearchText) ? $snippetForSearchText : null;
        $URL                    = CBSiteURL . "/{$this->path}";

        if (is_file("{$this->directory}/editor.css"))
        {
            $URLForEditorCSS = "{$URL}/editor.css";
        }
        else
        {
            $URLForEditorCSS = null;
        }

        if (is_file("{$this->directory}/editor.js"))
        {
            $URLForEditorJavaScript = "{$URL}/editor.js";
        }
        else
        {
            $URLForEditorJavaScript = null;
        }

        /**
         * 2014.05.03
         *
         * If the `editor.php` file exists it will be included once when the
         * page editing page loads. This file should do things like include
         * JavaScript and CSS dependencies using the CBHTMLOutput class.
         *
         * The `editor.js` and `editor.css` classes have been special cased
         * above but theoretically those special cases could be removed and
         * the relevant files could be included by `editor.php`.
         */

        $editorInitializer = "{$this->directory}/editor.php";

        if (!is_file($editorInitializer))
        {
            $editorInitializer = null;
        }

        $this->descriptor                           = new stdClass();
        $this->descriptor->editorInitializer        = $editorInitializer;
        $this->descriptor->modelJSON                = $this->makeModelJSON();
        $this->descriptor->name                     = basename($this->directory);
        $this->descriptor->snippetForHTML           = $snippetForHTML;
        $this->descriptor->snippetForSearchText     = $snippetForSearchText;
        $this->descriptor->URL                      = $URL;
        $this->descriptor->URLForEditorCSS          = $URLForEditorCSS;
        $this->descriptor->URLForEditorJavaScript   = $URLForEditorJavaScript;

        $CBSections[$this->ID] = $this->descriptor;
    }

    /**
     * This function generates a fully useable model object and then includes
     * the `model.php` file which is free to update or add properties.
     *
     * For example, `model.php` may set an official `schema` property or update
     * the `schemaVersion` if the current version is not 1.
     *
     * For sections that have custom properties, `model.php` must add all of
     * those properties and their default values.
     *
     * @return string
     */
    private function makeModelJSON()
    {
        $name                   = basename($this->directory);
        $name                   = preg_replace('/\s/', '', $name);

        $model                  = new stdClass();
        $model->schema          = "CBSectionTypeGeneratedModelFor{$name}";
        $model->schemaVersion   = 1;
        $model->sectionID       = null;
        $model->sectionTypeID   = $this->ID;

        $sectionModelFilename   = "{$this->directory}/model.php";

        if (is_file($sectionModelFilename))
        {
            include $sectionModelFilename;
        }

        return json_encode($model);
    }

    /**
     * @return string
     */
    private function makeTypeID()
    {
        $sectionTypeIDFilename = "{$this->directory}/ID.json";

        if (file_exists($sectionTypeIDFilename))
        {
            $sectionTypeID = json_decode(file_get_contents($sectionTypeIDFilename));
        }
        else
        {
            $sectionTypeID = Colby::random160();

            file_put_contents($sectionTypeIDFilename, json_encode($sectionTypeID));
        }

        return $sectionTypeID;
    }
}