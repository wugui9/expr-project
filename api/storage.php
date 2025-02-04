<?php
require_once __DIR__ . '/../model/php/env_settings.php';
require_once __DIR__ . '/ApiRouter.php';
require_once __DIR__ . '/../schemas/storage_schema.php';
require_once __DIR__ . '/../model/repository/StorageRepository.php';


$router = new ApiRouter();

function handleListStorage()
{
    $database = new DBModel();
    $storageRepository = new StorageRepository(
        $database->get_connection()
    );
    $storages = $storageRepository->findAll();

    // 使用schema验证和格式化返回值
    $schema = new StorageSchema();
    return [
        'storages' => array_map([$schema, 'format'], $storages)
    ];
}

$router->register('GET', 'storage/list', 'handleListStorage');

$router->handleRequest();

?>