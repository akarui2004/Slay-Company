<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionSeederFile = database_path('seeders/master_data/permission.csv');
        $handle = fopen($permissionSeederFile, 'r');
        if (!$handle) {
            $this->command->error("Could not open the file: " . $permissionSeederFile);
            return;
        }

        $permissions = [];
        $row = 0;
        while($data = fgetcsv($handle, 2000, ",")) {
            if ($row >= 1) {
                $permissions[] = [
                    'name' => $data[0],
                    'code' => $data[1],
                    'description' => $data[2],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            $row++;
        }

        fclose($handle); // close the file after reading

        $tableName = (new Permission())->getTable();
        DB::table($tableName)->upsert($permissions, ['code'], ['name', 'description', 'updated_at']);
        $this->command->info("Permissions seeded/updated successfully.");
    }
}
