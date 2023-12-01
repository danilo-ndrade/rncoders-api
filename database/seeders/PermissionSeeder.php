<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = config('permissions');

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'user_id' => 1,
            ]);
        }
    }
}
