<?php

namespace SmsAuth\Code;

use Exception;

class SmsAuth {


    private $apiurl = 'http://apiagent.ru/password_generation/api.php';

    /**
     * Создание подключения.
     *
     * @param string $login    логин в системе
     * @param string $password пароль в системе
     */
    public function __construct($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * Генерация кода авторизации
     * @param string $phone номер телефона получателя
     * @param string $sender подпись отправителя
     * @param integer $len длина кода
     * @param string $text текст персонификации
     * @return \$1|array|\SimpleXMLElement
     * @throws Exception
     */
    public function generateCode($phone, $sender, $len = 4, $text = '')
    {
        $xml = '<?xml version="1.0" encoding="utf-8"?>
                <request>
                 <security>
                     <login>'.$this->login.'</login>
                     <password>'.$this->password.'</password>
                 </security>
                 <phone>'.$phone.'</phone>
                 <sender>'.$sender.'</sender>
                 <random_string_len>'.$len.'</random_string_len>
                 <text>'.$text.'</text>
                </request>';

        return $this->send($xml);
    }

    /**
     * Отправка xml на сервер
     * @return \$1|array|\SimpleXMLElement
     */
    private function send($data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/xml; charset=utf-8'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CRLF, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_URL, $this->apiurl);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if (!isset($info['http_code']) || $info['http_code'] >= 400) {
            throw new Exception('Ошибка запроса к серверу авторизации. Код: '.
                $info['http_code']. '. Ошибка: '.$error);
        }

        $xml = @simplexml_load_string($result);
        if (!$xml) {
            throw new Exception('Неверный формат ответ от сервера.');
        }

        if (isset($xml->error)) {
            throw new Exception($xml->error);
        }

        return $xml;

    }

}
