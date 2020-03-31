<?php require_once $_SERVER["DOCUMENT_ROOT"]."/vendor/autoload.php";
/*
 * Created 29.03.2020 15:51
 */

/**
 * Class Skins
 * @author Alexandr Pokatskiy
 * @copyright ITTechnology
 */
class Skins
{
    /**
     * Файл базы скинов
     * @var string
     */
    private static $fileName = "market.csv";

    /**
     * Загрузить на сервер файл базы
     * @return bool
     */
    public static function get()
    {
        // Запросить наименование базы
        $dbFileInfo = \ITTech\app\cUrl::create("https://market.csgo.com/itemdb/current_730.json")
            ->method("GET")
            ->send();
        $json = json_decode($dbFileInfo);

        // Запросить базу
        $dbFile = \ITTech\app\cUrl::create("https://market.csgo.com/itemdb/".$json->db)
            ->method("GET")
            ->send();

        $database = $_SERVER["DOCUMENT_ROOT"]."/files/".self::$fileName;

        // Сохранить базу на сервер
        if(file_put_contents($database, $dbFile))
        {
            // Переформировать данные базы добавив изображение
            return self::createCacheDB($database);
        }

        return false;
    }

    /**
     * Получить изображение скина
     * @param string $imageName
     * @return string
     */
    public static function getImage(string $imageName)
    {
        $imageURL = self::getImageName($imageName).".png";
        try
        {
            if(!is_file($_SERVER["DOCUMENT_ROOT"]."/images/skins/".$imageURL))
            {
                $img = file_get_contents("http://api.steamapis.com/image/item/730/".$imageName);
                file_put_contents($_SERVER["DOCUMENT_ROOT"]."/images/skins/".$imageURL, $img);
            }

            return $imageURL;
        } catch (\ErrorException $e)
        {
            echo $e->getCode();
        }
    }

    /**
     * Преобразовать в удобное название
     * @param $image
     * @return mixed
     */
    public static function getImageName($image)
    {
        $notProbel = str_replace(" ", "_", $image);
        return str_replace("|", "@", $notProbel);
    }

    /**
     * Перезаписать базу
     * @param string $dbFile
     * @return bool
     */
    protected static function createCacheDB(string $dbFile)
    {
        $skinDBFile = $_SERVER["DOCUMENT_ROOT"]."/files/market.db";

        set_time_limit(300);
        $result = [];
        $handle = fopen($dbFile, "r");

        for($i=0; $data = fgetcsv($handle, 1000, ";"); $i++)
        {
            if($i > 0)
            {
                if($data[2] > 0.25 && $data[2] < 1500)
                {
                    $result[$i]["c_classid"] = $data[0];
                    $result[$i]["c_instanceid"] = $data[1];
                    $result[$i]["c_price"] = $data[2];
                    $result[$i]["c_offers"] = $data[3];
                    $result[$i]["c_popularity"] = $data[4];
                    $result[$i]["c_rarity"] = $data[5];
                    $result[$i]["c_quality"] = $data[6];
                    $result[$i]["c_heroid"] = $data[7];
                    $result[$i]["c_slot"] = $data[8];
                    $result[$i]["c_stickers"] = $data[9];
                    $result[$i]["c_market_name"] = $data[10];
                    $result[$i]["c_market_name_en"] = $data[11];
                    $result[$i]["c_market_hash_name"] = $data[12];
                    $result[$i]["c_name_color"] = $data[13];
                    $result[$i]["c_price_updated"] = $data[14];
                    $result[$i]["c_pop"] = $data[15];
                    $result[$i]["c_base_id"] = $data[16];
                    $result[$i]["c_image"] = self::getImage($data[12]);
                }
            }

        }

        fclose($handle);
        sort($result);

        if(file_put_contents($skinDBFile, serialize($result)))
        {
            return true;
        }

        return false;
    }
}
