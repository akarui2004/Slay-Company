<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleSeederFile = database_path('seeders/master_data/role.csv');
        $handle = fopen($roleSeederFile, 'r');
        if (!$handle) {
            $this->command->error("Could not open the file: " . $roleSeederFile);
            return;
        }

        $roles = [];
        $row = 0;
        while($data = fgetcsv($handle, 2000, ",")) {
            if ($row >= 1) {
                $roles[] = [
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

        $tableName = (new Role())->getTable();
        DB::table($tableName)->upsert($roles, ['code'], ['name', 'description', 'updated_at']);
        $this->command->info("Roles seeded/updated successfully.");
    }
}
