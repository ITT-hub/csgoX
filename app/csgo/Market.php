<?php
/*
 * Created 26.03.2020 12:45
 */

namespace ITTech\app\csgo;

use ITTech\app\cUrl;

/**
 * Class Market
 * @package ITTech\app\csgo
 * @author Alexandr Pokatskiy
 * @copyright ITTechnology
 */
class Market
{
    /**
     * Вернуть все скины
     * @return array
     */
    public static function getSkins(): array
    {
        $dbFile = $_SERVER["DOCUMENT_ROOT"]."/files/market.db";
        return unserialize(file_get_contents($dbFile));
    }
}