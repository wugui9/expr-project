<?php
require_once __DIR__ . '/../model/php/env_settings.php';
require_once __DIR__ . '/ApiRouter.php';
require_once __DIR__ . '/../schemas/hello_schema.php';

// Hello请求处理函数
function handleHello($data) {
    $request = HelloRequest::fromArray($data);
    if (!$request->validate()) {
        throw new Exception('Invalid msg parameter');
    }
    
    $response = HelloResponse::create('hello ' . $request->msg);
    return $response->toArray();
}

function handleGetHello($data) {
    $request = HelloRequest::fromArray($data);
    if (!$request->validate()) {
        throw new Exception('Invalid msg parameter');
    }
    
    $response = HelloResponse::create('hello ' . $request->msg);
    return $response->toArray();
}

$router = new ApiRouter();

$router->register('POST', 'hello/sayHello', 'handleHello');
$router->register('GET', 'hello/getHello', 'handleGetHello');

$router->handleRequest();
