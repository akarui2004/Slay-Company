<?php

namespace Database\Seeders;

use App\Models\RolePermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolePermissionSeederFile = database_path('seeders/master_data/role_permission.csv');
        $handle = fopen($rolePermissionSeederFile, 'r');

        if (!$handle) {
            $this->command->error("Could not open the file: " . $rolePermissionSeederFile);
            return;
        }

        $rolePermissions = [];
        $row = 0;
        while ($data = fgetcsv($handle, 2000, ",")) {
            if ($row >= 1) {
                $rolePermissions[] = [
                    'id' => $data[0],
                    'role_id' => $data[1],
                    'permission_id' => $data[2],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $row++;
        }

        fclose($handle); // close the file after reading

        $tableName = (new RolePermission())->getTable();
        DB::table($tableName)->upsert($rolePermissions, ['role_id', 'permission_id'], ['updated_at']);
        $this->command->info("Role-Permissions seeded/updated successfully.");
    }
}
