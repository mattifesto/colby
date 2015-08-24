<?php

final class CBPageKindLibraryPageKind {

    /**
     * @return {stdClass} | null | false
     */
    public static function createModelForKind() {
        $model          = CBModels::modelWithClassName(__CLASS__);
        $model->type    = isset($_GET['CBPageKindLibraryViewType']) ? $_GET['CBPageKindLibraryViewType'] : 'recent';

        switch ($model->type) {
            case 'library':
            case 'recent':
                break;

            case 'month':
                $month              = isset($_GET['CBPageKindLibraryViewMonth']) ? $_GET['CBPageKindLibraryViewMonth'] : '';
                $model->monthData   = CBPageKindLibraryPageKind::parseMonth($month);

                if ($model->monthData === false) {
                    return false;
                }

                break;

            default:
                return false;
        }

        return $model;
    }

    /**
     * @return  {stdClass} | false
     */
    private static function parseMonth($month) {
        if (preg_match('/^[0-9]{6}$/', $month)) {
            $monthNumber = substr($month, 4, 2);

            if ($monthNumber > 0 && $monthNumber < 13) {
                $dateTime               = DateTime::createFromFormat('!m', $monthNumber);
                $data                   = new stdClass();
                $data->month            = $month;
                $data->monthName        = $dateTime->format('F');
                $data->monthNameAsHTML  = ColbyConvert::textToHTML($data->monthName);
                $data->year             = substr($month, 0, 4);

                return $data;
            }
        }

        return false;
    }

    /**
     * @return {string}
     */
    public static function transformTitle($title, $args = []) {
        $modelForKind = null;
        extract($args, EXTR_IF_EXISTS);

        switch ($modelForKind->type) {
            case 'library':
                return "{$title}: Library";
            case 'month':
                return "{$title}: {$modelForKind->monthData->monthName} {$modelForKind->monthData->year}";
            default:
                return $title;
        }
    }
}
