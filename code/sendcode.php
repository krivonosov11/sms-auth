<?php

namespace SmsAuth\Code;

use Exception;

class SendCode
{
    /**
     * @var SmsAuth
     */
    private $sender;

    public function __construct()
    {
        $this->sender = new SmsAuth(
            'test', 'pass'
        );
    }

    public function sendMessage()
    {
        try {
            $result = $this->sender->generateCode(
                'телефон',// номер телефона получателя
                'отправитель',// подпись отправителя
                4,// длина кода
                'Код авторизации: {код}'// текст персонификации
            );
            $code = $result->success->attributes()['code'];// сгенерированный код
            $id_sms = $result->success->attributes()['id_sms'];// id смс для проверки статуса доставки
            $status = $result->success->attributes()['status'];// статус доставки
            var_dump($result);
        } catch (Exception $e) {
            $error = $e->getMessage();//ловим ошибку от сервера
            var_dump($error);

        }
    }
}
