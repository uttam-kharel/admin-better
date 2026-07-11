<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        AdminUser::upsert([
            ['id' => 1, 'email' => 'admin@lumina.health', 'name' => 'System Administrator', 'role' => 'super-admin', 'password' => bcrypt('admin123')],
            ['id' => 2, 'email' => 'editor@lumina.health', 'name' => 'Content Editor', 'role' => 'editor', 'password' => bcrypt('editor123')],
        ], 'email');
    }
}
