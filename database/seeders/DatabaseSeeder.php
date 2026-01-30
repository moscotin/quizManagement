<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Maxim',
            'email' => 'makmos@yandex.ru',
            'phone_number' => '+79261234567',
            'age' => 30,
            'password' => bcrypt('password'),
        ]);

        $this->call(FebDeSeeder::class);
        $this->call(QuizCatSeeder::class);
        $this->call(DemoQuizWithAllTypesSeeder::class);
    }
}
