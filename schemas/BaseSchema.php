<?php

abstract class BaseSchema {
    /**
     * 从数组创建对象
     * @param array<string,mixed> $data 数据数组
     * @return static
     */
    public static function fromArray(array $data): static {
        $instance = new static();
        foreach (get_object_vars($instance) as $property => $value) {
            if (isset($data[$property])) {
                $type = (new ReflectionProperty(static::class, $property))->getType();
                if ($type instanceof ReflectionNamedType) {
                    switch ($type->getName()) {
                        case 'int':
                            $instance->$property = (int)$data[$property];
                            break;
                        case 'float':
                            $instance->$property = (float)$data[$property];
                            break;
                        case 'bool':
                            $instance->$property = (bool)$data[$property];
                            break;
                        default:
                            $instance->$property = $data[$property];
                    }
                } else {
                    $instance->$property = $data[$property];
                }
            }
        }
        return $instance;
    }

    /**
     * 转换为数组
     * @return array<string,mixed>
     */
    public function toArray(): array {
        return get_object_vars($this);
    }

    /**
     * 验证数据是否有效
     * 子类可以重写此方法以添加特定的验证规则
     * @return bool
     */
    public function validate(): bool {
        return true;
    }
} 