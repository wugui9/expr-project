<?php

require_once __DIR__ . '/BaseSchema.php';

class HelloRequest extends BaseSchema {
    /** @var string */
    public string $msg = '';

    /**
     * 验证数据是否有效
     * @return bool
     */
    public function validate(): bool {
        return !empty($this->msg) && is_string($this->msg);
    }
}

class HelloResponse extends BaseSchema {
    /** @var string */
    public string $msg = '';

    /**
     * 创建响应对象
     * @param string $msg 消息内容
     * @return self
     */
    public static function create(string $msg): self {
        $response = new self();
        $response->msg = $msg;
        return $response;
    }
}
