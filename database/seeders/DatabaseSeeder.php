<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default admin user
        \App\Models\User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@crm.local',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role' => 'admin',
        ]);

        // Create manager user
        \App\Models\User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@crm.local',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role' => 'manager',
        ]);

        // Create staff user
        \App\Models\User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@crm.local',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role' => 'staff',
        ]);

        // Create sample customers
        $customers = \App\Models\Customer::factory(5)->create();

        // Create sample leads with existing customers and users
        $users = \App\Models\User::all();
        foreach ($customers as $customer) {
            \App\Models\Lead::factory(2)->create([
                'customer_id' => $customer->id,
                'assigned_to' => $users->random()->id,
            ]);
        }

        // Create sample tasks for leads
        $leads = \App\Models\Lead::all();
        foreach ($leads as $lead) {
            \App\Models\Task::factory(3)->create([
                'lead_id' => $lead->id,
                'assigned_to' => $lead->assigned_to,
            ]);
        }
    }
}
