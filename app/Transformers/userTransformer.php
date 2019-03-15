<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\User;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'verified' => (boolean) $user->verified,
            'added' => date('Y-m-d', strtotime($user->created_at))
        ];
    }
}