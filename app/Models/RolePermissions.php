<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RolePermissions extends Pivot
{
    protected $table = 'role_permissions';

    protected $fillable = [
        'role_id',
        'permission_id',
    ];
}
