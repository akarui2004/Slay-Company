<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserPermission extends Pivot
{
    protected $table = 'user_permission';

    protected $fillable = [
        'user_id',
        'permission_id',
    ];
}
