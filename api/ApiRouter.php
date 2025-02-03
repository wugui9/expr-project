<?php

class ApiRouter {
    private $routes = [];

    /**
     * 注册一个API路由
     * @param string $method HTTP方法 (GET, POST等)
     * @param string $path 路径
     * @param callable $handler 处理函数
     */
    public function register($method, $path, $handler) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }

    /**
     * 处理请求
     */
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // 移除基础路径，只保留API路径部分
        $basePath = '/api/';
        if (strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                $this->handleJsonRequest($route['handler']);
                return;
            }
        }

        // 如果没有找到匹配的路由
        http_response_code(404);
        echo json_encode(['error' => 'API endpoint not found']);
    }

    /**
     * 处理JSON API请求
     * @param callable $handler 处理函数
     */
    private function handleJsonRequest($handler) {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if ($data === null) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON format']);
            return;
        }

        try {
            $response = $handler($data);
            header('Content-Type: application/json');
            echo json_encode($response);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
} 