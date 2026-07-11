<?php

namespace Database\Seeders;

use App\Models\ContactSubmission;
use Illuminate\Database\Seeder;

class ContactSubmissionSeeder extends Seeder
{
    public function run(): void
    {
        ContactSubmission::insert([
            ['id' => 'MSG-100001', 'name' => 'Priya Patel', 'email' => 'priya@example.com', 'phone' => '+919812345678', 'subject' => 'Insurance query', 'message' => 'Do you accept Star Health insurance for inpatient procedures?', 'status' => 'new', 'created_at' => now()->subHours(2), 'updated_at' => now()->subHours(2)],
        ]);
    }
}
