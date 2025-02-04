<?php
require_once __DIR__ . '/../model/php/env_settings.php';
require_once __DIR__ . '/ApiRouter.php';
require_once __DIR__ . '/../schemas/storage_schema.php';
require_once __DIR__ . '/../model/repository/StorageRepository.php';

$router = new ApiRouter();

/**
 * 处理获取仓库列表的请求
 * @return array<string,array<string,mixed>> 仓库列表数据
 */
function handleListStorage(): array
{
    $database = new DBModel();
    $storageRepository = new StorageRepository(
        $database->get_connection()
    );
    /** @var Storage[] $storages */
    $storages = $storageRepository->findAll();

    // 转换实体为Schema
    $storageSchemas = array_map([StorageSchema::class, 'fromEntity'], $storages);
    $response = ListStorageResponse::create($storageSchemas);
    return $response->toArray();
}

/**
 * 处理添加仓库的请求
 * @param array<string,mixed> $data 仓库数据
 * @throws Exception 当数据验证失败时抛出异常
 */
function handleAddStorage(array $data): void
{
    $req = CreateStorageRequest::fromArray($data);
    if (!$req->validate()) {
        throw new Exception('Invalid storage data');
    }

    $database = new DBModel();
    $storageRepository = new StorageRepository(
        $database->get_connection()
    );
    $storageRepository->create($req->toArray());
}

$router->register('GET', 'storage/list', 'handleListStorage');
$router->register('POST', 'storage/add', 'handleAddStorage');

$router->handleRequest();

?>