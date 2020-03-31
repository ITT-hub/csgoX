<?php
/*
 * Created 26.03.2020 12:45
 */

namespace ITTech\app;

/**
 * Class cUrl
 * @package ITTech\app
 * @author Alexandr Pokatskiy
 * @copyright ITTechnology
 */
class cUrl
{
    /**
     * Отправляемые данные
     * @var array
     */
    private static $curData;

    /**
     * URL для запроса
     * @var string
     */
    private static $url;

    /**
     * Метод запроса
     * @var string
     */
    private static $method;

    /**
     * Передача метода запроса
     * @param string $method
     * @return self
     */
    public function method(string $method): self
    {
        self::$method = $method;
        return $this;
    }

    public function postData(array $data)
    {
        self::$curData = json_encode($data);
        return $this;
    }

    /**
     * Выполнить cUrl запрос
     * @return array|bool
     */
    public function send()
    {
        try {
            $header   = [
                "Content-Type: application/json"
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_URL, self::$url);

            if(self::$method != "GET")
            {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, self::$curData);
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/cacert.pem');

            $responce = curl_exec($ch);
            if(curl_error($ch)) {
                echo curl_error($ch);
            }
            curl_close($ch);

            return $responce;
        } catch (\ErrorException $e)
        {
            echo $e->getMessage();
        }
    }

    /**
     * Создать данные для запроса
     * @param string $url
     * @return self
     */
    public static function create(string $url): self
    {
        self::$url     = $url;
        return new self();
    }
}
