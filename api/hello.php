<?php
require_once __DIR__ . '/../model/php/env_settings.php';
require_once __DIR__ . '/ApiRouter.php';

// Hello请求处理函数
function handleHello($data) {
    // 业务层校验
    if (!isset($data['msg']) || !is_string($data['msg'])) {
        throw new Exception('Invalid msg parameter');
    }
    
    return [
        'msg' => 'hello ' . $data['msg']
    ];
}

function handleGetHello($data) {
    return [
        'msg' => 'hello ' . $data['msg']
    ];
}

$router = new ApiRouter();

$router->register('POST', 'hello/sayHello', 'handleHello');
$router->register('GET', 'hello/getHello', 'handleGetHello');

$router->handleRequest();
