<?php

namespace App\Schemas;

class UserSchema extends BaseSchema {
    protected function rules(): array {
        return [
            'user_number' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'first_name' => ['required', 'string'],
            'phone' => ['required', 'string', 'max:15'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
            'salt' => ['required', 'string', 'size:32'],
            'role' => ['required', 'string', 'in:ADMIN,CUSTOMER,DELIVERY_DRIVER,STORE_KEEPER']
        ];
    }
} 