<?php

namespace Database\Seeders;

use App\Models\MessageVariable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample message variables
        $variables = [
            ['code' => '{0}', 'description' => 'نام اقامتگر', 'field_name' => 'resident_name'],
            ['code' => '{1}', 'description' => 'شماره تخت', 'field_name' => 'bed_number'],
            ['code' => '{2}', 'description' => 'شماره اتاق', 'field_name' => 'room_number'],
            ['code' => '{3}', 'description' => 'تاریخ سررسید', 'field_name' => 'due_date'],
            ['code' => '{4}', 'description' => 'مبلغ پرداختی', 'field_name' => 'payment_amount'],
            ['code' => '{5}', 'description' => 'تاریخ شروع اقامت', 'field_name' => 'start_date'],
            ['code' => '{6}', 'description' => 'تاریخ پایان اقامت', 'field_name' => 'end_date'],
            ['code' => '{7}', 'description' => 'شماره قرارداد', 'field_name' => 'contract_id'],
            ['code' => '{8}', 'description' => 'رمز یکبار مصرف', 'field_name' => 'otp_code'],
            ['code' => '{9}', 'description' => 'آدرس وبسایت', 'field_name' => 'website_url'],
        ];

        foreach ($variables as $variable) {
            MessageVariable::firstOrCreate(
                ['code' => $variable['code']],
                [
                    'description' => $variable['description'],
                    'field_name' => $variable['field_name'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Message variables seeded successfully!');
    }
}
