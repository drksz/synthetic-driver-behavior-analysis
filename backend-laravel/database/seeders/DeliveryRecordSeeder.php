<?php

namespace Database\Seeders;

use App\Models\DeliveryRecord;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


/*
    DATABASE SEEDER FOR DELIVERY RECORDS

    Loads in the deliveries_processed.csv file and 
    maps each row to the actual database
*/


class DeliveryRecordSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = storage_path('../../data/deliveries_processed.csv');

        if (!file_exists($filePath)) {
            throw new \Exception("No such CSV file in path: {$filePath}");
        }

        DB::table('delivery_records')->truncate();

        $file = fopen($filePath, 'r');
        // consumes first row, the columns
        $headers = fgetcsv($file);

        $batch = [];
        $batchSize = 1000;

        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($headers, $row);

            $batch[] = [
                'delivery_id' => $data['delivery_id'],
                'driver_id' => $data['driver_id'],
                'date' => $data['date'],
                'weekday' => $data['weekday'],
                'priority' => $data['priority'],
                'vehicle_type' => $data['vehicle_type'],
                'delivery_distance' => (float)$data['delivery_distance'],
                'idle' => (float)$data['idle'],
                'arrival_est' => (float)$data['arrival_est'],
                'arrival_act' => (float)$data['arrival_act'],
                'delay' => (float)$data['delay'],
                'on_time' => filter_var($data['on_time'], FILTER_VALIDATE_BOOLEAN),
                'est_veh_spd' => (float)$data['est_veh_spd'],
                'attitude' => (float)$data['attitude'],
                'pkg_care' => (float)$data['pkg_care'],
                'responsiveness' => (float)$data['responsiveness'],
                'delivery_spd' => (float)$data['delivery_spd'],
                'weather' => $data['weather'],
                'traffic_cond' => $data['traffic_cond'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= $batchSize) {
                DeliveryRecord::insert($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            DeliveryRecord::insert($batch);
        }

        fclose($file);
    }
}
