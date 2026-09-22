<?php

namespace Transmissor\Services;

class UserService
{
    public function find($id)
    {
        $userModel = config('auth.providers.users.model', 'App\\Models\\User');
        if (class_exists($userModel)) {
            return $userModel::find($id);
        }

        return null;
    }

    public function all()
    {
        $userModel = config('auth.providers.users.model', 'App\\Models\\User');
        if (class_exists($userModel)) {
            return $userModel::all();
        }

        return collect();
    }
}
