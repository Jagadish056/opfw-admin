<?php

namespace Database\Seeders;

use App\Player;
use Illuminate\Database\Seeder;

class ServerSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Player::factory()->create();
    }

}
