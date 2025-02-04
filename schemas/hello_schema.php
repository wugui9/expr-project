<?php

class HelloRequest {
    /** @var string */
    public string $msg;

    public static function fromArray(array $data): self {
        $request = new self();
        $request->msg = $data['msg'] ?? '';
        return $request;
    }

    public function validate(): bool {
        return !empty($this->msg) && is_string($this->msg);
    }
}

class HelloResponse {
    /** @var string */
    public string $msg;

    public static function create(string $msg): self {
        $response = new self();
        $response->msg = $msg;
        return $response;
    }

    public function toArray(): array {
        return [
            'msg' => $this->msg
        ];
    }
}
