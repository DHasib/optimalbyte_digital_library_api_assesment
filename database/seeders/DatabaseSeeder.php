<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Category, Discount, Role, User, Service, Booking};
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       // 1) Core lookups

       Role::factory()->state(['name'=>'admin'])->create();
       Role::factory()->state(['name'=>'employee'])->create();
       Role::factory()->state(['name'=>'customer'])->create();

       $adminRole    = Role::where('name','admin')->first();
       $empRole      = Role::where('name','employee')->first();
       $custRole     = Role::where('name','customer')->first();

       // 2) Users
       User::factory(2)->create(['role_id' => $adminRole->id]);
       User::factory(8)->create(['role_id' => $empRole->id]);
       User::factory(20)->create(['role_id'=> $custRole->id]);

       // create a single admin user with known credentials
       User::factory()->admin()->create();


    }
}
