<?php

class CPAdminImageListView {

    /**
     * @return stdClass
     */
    public static function compile($spec) {
        $model                  = new stdClass();
        $model->viewClassName   = __CLASS__;

        return $model;
    }

    /**
     * @return string (HTML)
     */
    public static function IDToDescriptionHTML($ID) {
        return $ID;
    }

    /**
     * @return string (HTML)
     */
    public static function IDToThumbnailHTML($ID, $extension) {
        $dataStore          = new CBDataStore($ID);
        $thumbnailFilename  = "rs200clc200.{$extension}";
        $thumbnailFilepath  = $dataStore->directory() . "/{$thumbnailFilename}";

        if (!is_file($thumbnailFilename)) {
            $originalFilepath   = $dataStore->directory() . "/original.{$extension}";
            $size               = getimagesize($originalFilepath);
            $projection         = CBProjection::withSize($size[0], $size[1]);

            $projection = CBProjection::reduceShortEdge($projection, 200);
            $projection = CBProjection::cropLongEdgeFromCenter($projection, 200);

            CBImages::reduceImageFile($originalFilepath, $thumbnailFilepath, $projection);
        }

        $thumbnailURL = $dataStore->URL() . "/{$thumbnailFilename}";

        return "<img src=\"{$thumbnailURL}\" style=\"max-height: 50px; max-width: 50px;\">";
    }

    public static function queryForRecentlyModifiedImages() {
        $rows   = array();
        $SQL    = <<<EOT

            SELECT
                LOWER(HEX(`ID`)) as `ID`,
                `created`,
                `extension`,
                `modified`
            FROM
                `CBImages`
            ORDER BY
                `modified` DESC
            LIMIT 20

EOT;

        $result = Colby::query($SQL);

        while ($row = $result->fetch_object()) {
            $rows[] = $row;
        }

        $result->free();

        return $rows;
    }

    /**
     * @return void
     */
    public static function renderAsHTML($model) {

        $images = self::queryForRecentlyModifiedImages();

        echo '<div class="CPAdminImageListView"><table>';

        foreach ($images as $image) {
            $created        = self::timestampToHTML($image->created);
            $modified       = self::timestampToHTML($image->modified);
            $description    = self::IDToDescriptionHTML($image->ID);
            $thumbnail      = self::IDToThumbnailHTML($image->ID, $image->extension);

            echo "<tr><td>$thumbnail</td><td>{$description}</td><td>{$created}</td><td>{$modified}</td></tr>";
        }

        echo '</table></div>';
    }

    /**
     * @return string (HTML)
     */
    public static function timestampToHTML($timestamp) {
        $javaScriptTimestamp = $timestamp * 1000;

        return "<span class=\"time\" data-timestamp=\"{$javaScriptTimestamp}\"></span>";
    }

    /**
     * @return array
     */
    public static function URLsForCSS() {
        $URL = CBSystemURL . '/classes/CPView/views/CPAdminImageListView';

        return [$URL . '/CPAdminImageListViewHTML.css'];
    }
}
