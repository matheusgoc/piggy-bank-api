<?php

namespace Database\Seeders;

use App\Models\TransactionUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TransactionUser::factory()
            ->times(720)
            ->create([
                'user_id' => User::whereEmail('matheus.goc@gmail.com')->first(),
            ]);
    }
}
