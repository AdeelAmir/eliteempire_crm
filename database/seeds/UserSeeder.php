<?php

use App\Cooling_towers;
use Illuminate\Database\Seeder;
use App\User;
use App\Plant_report_trains;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'ISol',
            'email'=>'admin@isol.pk',
            'password'=>bcrypt('12345678'),
        ]);

        $coolingTower1 = Cooling_towers::create([
            'title' => 'Cooling Tower 1'
        ]);

        $coolingTower2 = Cooling_towers::create([
            'title' => 'Cooling Tower 2'
        ]);

        $coolingTower3 = Cooling_towers::create([
            'title' => 'Cooling Tower 3'
        ]);

        $coolingTower4 = Cooling_towers::create([
            'title' => 'Cooling Tower 4'
        ]);

        $coolingTower5 = Cooling_towers::create([
            'title' => 'Cooling Tower 5'
        ]);

        $PlantReportTrain1 = Plant_report_trains::create([
            'title' => 'Train 1'
        ]);

        $PlantReportTrain2 = Plant_report_trains::create([
            'title' => 'Train 2'
        ]);

        $PlantReportTrain3 = Plant_report_trains::create([
            'title' => 'Train 3'
        ]);

        $PlantReportTrain4 = Plant_report_trains::create([
            'title' => 'Train 4'
        ]);

        $PlantReportTrain5 = Plant_report_trains::create([
            'title' => 'Train 5'
        ]);
    }
}