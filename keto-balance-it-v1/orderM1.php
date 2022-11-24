<?php

// /**
//  * Базовая конфигурация
//  */
// // * Апи ключ вашего акканута
// $apiKey = 'fce47a96fe5879e83a7783d84f126a04';
// // * Домен проекта на который происходит отправка заказов
// $domain = 'shakes.pro';
// // Урл оригинального лендинга, необходим для корректого расчета Вашей статистики
// $landingUrl = 'IT';
// // * Идентификатор оффера на который вы льете
// $offerId = '9685';
// // Код потока заведенного в системе, если указан, статистика будет записываться на данный поток
// $streamCode = '';
// // Страница, отдаваемая при успешном заказе
$successPage = 'success.html';
// // Страница, отдаваемая в случае ошибки
$errorPage = 'index.html';
// /**
//  * Формирование отправляемого заказа
//  */
// $url = "http://$domain?r=/api/order/in&key=$apiKey";
// $order = [
//     'countryCode' => (!empty($_POST['country']) ? $_POST['country'] : ($_GET['country'] ? $_GET['country'] : 'RU')),
//     'comment' => (!empty($_POST['comment']) ? $_POST['comment'] : ($_GET['comment'] ? $_GET['comment'] : '')),
//     'createdAt' => date('Y-m-d H:i:s'),
//     'ip' => (!empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null), // ip пользователя
//     'landingUrl' => $landingUrl,
//     'name' => (!empty($_POST['name']) ? $_POST['name'] : ($_GET['name'] ? $_GET['name'] : '')),
//     'offerId' => $offerId,
//     'phone' => (!empty($_POST['phone']) ? $_POST['phone'] : ($_GET['phone'] ? $_GET['phone'] : '')),
//     'referrer' => (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null),
//     'streamCode' => $streamCode,
//     'sub1' => (!empty($_POST['sub1']) ? $_POST['sub1'] : ''),
//     'sub2' => (!empty($_GET['sub2']) ? $_GET['sub2'] : ''),
//     'sub3' => (!empty($_GET['sub3']) ? $_GET['sub3'] : ''),
//     'sub4' => (!empty($_GET['sub4']) ? $_GET['sub4'] : ''),
//     'userAgent' => (!empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '-'),
// ];

// /**
//  * Отправка заказа
//  */
// /**
//  * @see http://php.net/manual/ru/book.curl.php
//  */
// $curl = curl_init();
// /**
//  * @see http://php.net/manual/ru/function.curl-setopt.php
//  */
// curl_setopt($curl, CURLOPT_URL, $url);
// curl_setopt($curl, CURLOPT_POST, true);
// curl_setopt($curl, CURLOPT_POSTFIELDS, $order);
// curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($curl, CURLOPT_USERAGENT, 'curl/' . (curl_version()['version'] ?? '7'));
// /**
//  * @see http://php.net/manual/ru/language.exceptions.php
//  */




$url = 'http://m1.top/send_order/';
$data = [
'ref' => 966870,
'api_key' => 'ba64c82d7589b0e4d77916be26857c46',
'product_id' => 13002,
'phone' => $_REQUEST['phone'],
'name' => $_REQUEST['name'],
'ip' => $_SERVER['REMOTE_ADDR'],
's' => $_REQUEST['subid'],
'w' => '',
't' => '',
'p' => '',
'm' => ''
];

/** 
 * Язык лендинга (для бурж лендингов)
 * 
 * Указывается для того, чтобы все заказы, независимо от IP юзера приходили на ГЕО,
 * связанное с лендом.
 * 
 * Пример: $data['langCode'] = 'es';
 * 
 * Таким образом, даже если пользователь зайдет на лендинг с российского IP,
 * и у оффера есть при этом RU ГЕО, то заказ все равно уйдет на Испанию (ES)
 */
$data['langCode'] = 'IT';

$process = curl_init();
curl_setopt($process, CURLOPT_HEADER, 0);
curl_setopt($process, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.0.3705; .NET CLR 1.1.4322; Media Center PC 4.0)");
curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($process, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($process, CURLOPT_TIMEOUT, 20);
curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($process, CURLOPT_POST, true);
curl_setopt($process, CURLOPT_POSTFIELDS, $data);
curl_setopt($process, CURLOPT_URL, $url);

// echo $return = curl_exec($process);

// curl_close($process);


try {
    $responseBody = curl_exec($process);
    /**
     * Логгируем все ответы сервера.
     * Если заказ не отправляется, данный файл может быть затребован саппортом
     * для диагностики проблемы.
     * @see http://php.net/manual/ru/function.file-put-contents.php
     */
    @file_put_contents(
        __DIR__ . '/M1.response.log',
        date('Y.m.d H:i:s') . PHP_EOL . $responseBody,
        FILE_APPEND
    );

    // тело оказалось пустым
    if (empty($responseBody)) {
        throw new Exception('Error: Empty response for order. ' . var_export($data, true));
    }
    /**
     * @var StdClass $response
     */
    $response = json_decode($responseBody, true);
    // возможно пришел некорректный формат
    if (empty($response)) {
        throw new Exception('Error: Broken json format for order. ' . PHP_EOL . var_export($order, true));
    }
    // заказ не принят API
    if ($response['result'] !== 'ok') {
        throw new Exception('Success: Order is accepted. '
            . PHP_EOL . 'Order: ' . var_export($data, true)
            . PHP_EOL . 'Response: ' . var_export($response, true)
        );
    }
    /**
     * логируем данные об обработке заказа
     * @see http://php.net/manual/ru/function.file-put-contents.php
     */
    @file_put_contents(
        __DIR__ . '/order.success.log',
        date('Y.m.d H:i:s') . ' ' . $responseBody,
        FILE_APPEND
    );
    curl_close($process);

    if(!empty($successPage) && is_file(__DIR__ . '/success/' . $successPage)) {
        include __DIR__ . '/success/' . $successPage;
    }
} catch (Exception $e) {
    /**
     * логируем ошибку
     * @see http://php.net/manual/ru/function.file-put-contents.php
     */
    @file_put_contents(
        __DIR__ . '/order.error.log',
        date('Y.m.d H:i:s') . ' ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString(),
        FILE_APPEND
    );

    if(!empty($errorPage) && is_file(__DIR__ . '/' . $errorPage)) {
        include __DIR__ . '/' . $errorPage;
    }
}
