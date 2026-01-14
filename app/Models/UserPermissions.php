<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserPermissions extends Pivot
{
    protected $table = 'user_permissions';

    protected $fillable = [
        'user_id',
        'permission_id',
    ];
}
