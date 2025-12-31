<?php

namespace Database\Seeders;

use App\Models\Bed;
use App\Models\Contract;
use App\Models\Resident;
use App\Enums\ReferralSource;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ExitedResidentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('fa_IR');
        
        // تعداد اقامتگران خروجی که می‌خواهیم ایجاد کنیم
        $count = $this->command->ask('چند اقامتگر خروجی می‌خواهید ایجاد کنید?', 50);

        $this->command->info("شروع ایجاد {$count} اقامتگر خروجی...");

        // دریافت تمام تخت‌ها
        $beds = Bed::where('state', 'active')
            ->with('room.unit')
            ->get();

        if ($beds->isEmpty()) {
            $this->command->error('هیچ تخت یافت نشد!');
            return;
        }

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

        // تاریخ شروع سال (اول فروردین)
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();

        $counter = 0;
        for ($i = 0; $i < $count; $i++) {
            // انتخاب تصادفی یک تخت
            $bed = $beds->random();

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

            // تولید تاریخ پایان (در طول سال جاری - تصادفی)
            $endDate = Carbon::createFromTimestamp(
                $faker->dateTimeBetween($startOfYear, $endOfYear)->getTimestamp()
            );

            // تولید تاریخ شروع (بین 6 تا 12 ماه قبل از تاریخ پایان)
            $startDate = $endDate->copy()->subMonths($faker->numberBetween(6, 12))->subDays($faker->numberBetween(0, 30));
            
            // اطمینان از اینکه تاریخ شروع قبل از تاریخ پایان باشد
            if ($startDate->greaterThanOrEqualTo($endDate)) {
                $startDate = $endDate->copy()->subMonths(6);
            }

            // تولید تاریخ پرداخت (بین تاریخ شروع و پایان)
            $paymentDate = Carbon::createFromTimestamp(
                $faker->dateTimeBetween($startDate, $endDate)->getTimestamp()
            );

            // ایجاد قرارداد با state = 'leaving'
            $contract = Contract::create([
                'resident_id' => $resident->id,
                'bed_id' => $bed->id,
                'state' => 'leaving',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'payment_date' => $paymentDate,
            ]);

            // حذف نرم قرارداد (deleted_at) - تاریخ حذف = تاریخ پایان
            $contract->deleted_at = $endDate;
            $contract->save();

            // تخت را خالی می‌کنیم چون قرارداد deleted شده
            $bed->update([
                'state_ratio_resident' => 'empty',
            ]);

            // حذف نرم اقامتگر (deleted_at) - 70% احتمال
            if ($faker->boolean(70)) {
                $resident->deleted_at = $endDate->copy()->addDays($faker->numberBetween(0, 7));
                $resident->save();
            }

            $counter++;
            if ($counter % 10 == 0) {
                $this->command->info("{$counter} اقامتگر خروجی ایجاد شد...");
            }
        }

        $this->command->info("✅ {$counter} اقامتگر خروجی با موفقیت ایجاد شدند!");
        $this->command->info("تاریخ‌های خروج در طول سال جاری ({$startOfYear->format('Y/m/d')} تا {$endOfYear->format('Y/m/d')}) توزیع شده‌اند.");
    }
}

