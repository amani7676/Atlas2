<?php

namespace Database\Seeders;

use App\Models\Bed;
use App\Models\Contract;
use App\Models\Resident;
use App\Models\Room;
use App\Enums\ReferralSource;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Morilog\Jalali\Jalalian;
use Faker\Factory as Faker;

class ActiveResidentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('fa_IR');
        
        // دریافت تمام تخت‌های خالی
        $emptyBeds = Bed::where('state', 'active')
            ->whereDoesntHave('contracts', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->with('room.unit')
            ->get();

        if ($emptyBeds->isEmpty()) {
            $this->command->info('هیچ تخت خالی یافت نشد!');
            return;
        }

        $this->command->info("شروع پر کردن {$emptyBeds->count()} تخت خالی...");

        $jobs = [
            'daneshjo_dolati',
            'daneshjo_azad',
            'daneshjo_other',
            'karmand_dolat',
            'karmand_shakhse',
            'azad',
            'nurse',
            'other'
        ];

        $referralSources = ReferralSource::all();

        $counter = 0;
        foreach ($emptyBeds as $bed) {
            // تولید نام فارسی
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            $fullName = $firstName . ' ' . $lastName;

            // تولید شماره تلفن (11 رقم)
            $phone = '09' . $faker->numerify('#########');

            // تولید تاریخ تولد (بین 18 تا 35 سال)
            $age = $faker->numberBetween(18, 35);
            $birthDate = Carbon::now()->subYears($age)->subDays($faker->numberBetween(0, 365));

            // انتخاب تصادفی شغل
            $job = $faker->randomElement($jobs);

            // انتخاب تصادفی نحوه آشنایی
            $referralSource = $faker->randomElement($referralSources);

            // تولید کد ملی (boolean - 70% احتمال داشتن کد ملی)
            $hasDocument = $faker->boolean(70);

            // ایجاد اقامتگر با تمام فیلدها
            $resident = Resident::create([
                'full_name' => $fullName,
                'phone' => $phone,
                'age' => $age,
                'birth_date' => $birthDate,
                'job' => $job,
                'referral_source' => $referralSource,
                'form' => $faker->boolean(60), // 60% احتمال داشتن فرم
                'document' => $hasDocument,
                'rent' => $faker->boolean(50), // 50% احتمال پرداخت اجاره
                'trust' => $faker->boolean(30), // 30% احتمال ودیعه
            ]);

            // تولید تاریخ شروع (بین 1 تا 6 ماه پیش)
            $startDate = Carbon::now()->subMonths($faker->numberBetween(1, 6))->subDays($faker->numberBetween(0, 30));

            // تولید تاریخ پرداخت (بین 1 تا 3 ماه پیش)
            $paymentDate = Carbon::now()->subMonths($faker->numberBetween(1, 3))->subDays($faker->numberBetween(0, 30));

            // ایجاد قرارداد با state = 'active'
            $contract = Contract::create([
                'resident_id' => $resident->id,
                'bed_id' => $bed->id,
                'state' => 'active',
                'start_date' => $startDate,
                'payment_date' => $paymentDate,
            ]);

            // به‌روزرسانی وضعیت تخت
            $bed->update([
                'state_ratio_resident' => 'full',
            ]);

            $counter++;
            if ($counter % 10 == 0) {
                $this->command->info("{$counter} تخت پر شد...");
            }
        }

        $this->command->info("✅ {$counter} اقامتگر فعال با موفقیت ایجاد شدند و تمام تخت‌ها پر شدند!");
    }
}

