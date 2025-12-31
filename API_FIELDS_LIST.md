# لیست کامل فیلدهای API Residents

این فایل شامل تمام فیلدهایی است که در API `/api/residents` برگردانده می‌شود.

## ساختار داده

هر ردیف در پاسخ API شامل تمام اطلاعات مرتبط با یک Contract است که به صورت flat و مرتب شده است.

---

## فیلدهای اصلی (در ابتدای هر ردیف)

| نام فیلد | نوع | توضیحات |
|---------|-----|---------|
| `resident_id` | integer | شناسه ساکن |
| `contract_id` | integer | شناسه قرارداد |

---

## فیلدهای Unit (واحد)

| نام فیلد | نوع | توضیحات |
|---------|-----|---------|
| `unit_id` | integer | شناسه واحد |
| `unit_name` | string | نام واحد |
| `unit_code` | bigInteger | کد واحد |
| `unit_desc` | text | توضیحات واحد |
| `unit_created_at` | timestamp | تاریخ ایجاد واحد |
| `unit_updated_at` | timestamp | تاریخ به‌روزرسانی واحد |

---

## فیلدهای Room (اتاق)

| نام فیلد | نوع | توضیحات |
|---------|-----|---------|
| `room_id` | integer | شناسه اتاق |
| `room_name` | string | نام اتاق |
| `room_code` | integer | کد اتاق |
| `room_unit_id` | integer | شناسه واحد (foreign key) |
| `room_bed_count` | bigInteger | تعداد تخت‌های اتاق |
| `room_desc` | text | توضیحات اتاق |
| `room_type` | enum | نوع اتاق ('room' یا 'reception') |
| `room_created_at` | timestamp | تاریخ ایجاد اتاق |
| `room_updated_at` | timestamp | تاریخ به‌روزرسانی اتاق |

---

## فیلدهای Bed (تخت)

| نام فیلد | نوع | توضیحات |
|---------|-----|---------|
| `bed_id` | integer | شناسه تخت |
| `bed_name` | string | نام تخت |
| `bed_code` | integer | کد تخت |
| `bed_room_id` | integer | شناسه اتاق (foreign key) |
| `bed_state_ratio_resident` | enum | وضعیت نسبت به ساکن ('rezerve', 'full', 'empty', 'nightly') |
| `bed_state` | enum | وضعیت تخت ('active', 'repair') |
| `bed_desc` | text | توضیحات تخت |
| `bed_created_at` | timestamp | تاریخ ایجاد تخت |
| `bed_updated_at` | timestamp | تاریخ به‌روزرسانی تخت |

---

## فیلدهای Contract (قرارداد)

| نام فیلد | نوع | توضیحات |
|---------|-----|---------|
| `contract_resident_id` | integer | شناسه ساکن (foreign key) |
| `contract_payment_date` | date | تاریخ پرداخت/تمدید قرارداد |
| `contract_payment_date_jalali` | string | تاریخ پرداخت به شمسی (Y/m/d) |
| `contract_bed_id` | integer | شناسه تخت (foreign key) |
| `contract_state` | enum | وضعیت قرارداد ('rezerve', 'nightly', 'active', 'leaving', 'exit') |
| `contract_start_date` | date | تاریخ شروع قرارداد |
| `contract_start_date_jalali` | string | تاریخ شروع به شمسی (Y/m/d) |
| `contract_end_date` | date | تاریخ پایان قرارداد |
| `contract_end_date_jalali` | string | تاریخ پایان به شمسی (Y/m/d) |
| `contract_created_at` | timestamp | تاریخ ایجاد قرارداد |
| `contract_updated_at` | timestamp | تاریخ به‌روزرسانی قرارداد |
| `contract_deleted_at` | timestamp | تاریخ حذف قرارداد (soft delete) |

---

## فیلدهای Resident (ساکن)

| نام فیلد | نوع | توضیحات |
|---------|-----|---------|
| `resident_full_name` | string | نام کامل ساکن |
| `resident_phone` | string | شماره تلفن |
| `resident_age` | integer | سن ساکن |
| `resident_birth_date` | date | تاریخ تولد |
| `resident_job` | enum | شغل ('daneshjo_dolati', 'daneshjo_azad', 'daneshjo_other', 'karmand_dolat', 'karmand_shakhse', 'azad', 'nurse', 'other') |
| `resident_referral_source` | enum | منبع معرفی ('university_introduction', 'university_website', 'google', 'map', 'khobinja_website', 'introducing_friends', 'street', 'divar', 'other') |
| `resident_form` | boolean | فرم |
| `resident_document` | boolean | مدرک |
| `resident_rent` | boolean | اجاره |
| `resident_trust` | boolean | امانت |
| `resident_created_at` | timestamp | تاریخ ایجاد ساکن |
| `resident_updated_at` | timestamp | تاریخ به‌روزرسانی ساکن |
| `resident_deleted_at` | timestamp | تاریخ حذف ساکن (soft delete) |

---

## فیلدهای Notes (یادداشت‌ها)

فیلد `notes` یک آرایه از یادداشت‌های ساکن است. هر یادداشت شامل:

| نام فیلد | نوع | توضیحات |
|---------|-----|---------|
| `note_id` | integer | شناسه یادداشت |
| `note_resident_id` | integer | شناسه ساکن (foreign key) |
| `note_type` | enum | نوع یادداشت ('payment', 'end_date', 'exit', 'demand', 'other') |
| `note_note` | text | متن یادداشت |
| `note_created_at` | timestamp | تاریخ ایجاد یادداشت |
| `note_updated_at` | timestamp | تاریخ به‌روزرسانی یادداشت |
| `note_deleted_at` | timestamp | تاریخ حذف یادداشت (soft delete) |

---

## خلاصه تعداد فیلدها

- **فیلدهای اصلی**: 2 فیلد
- **فیلدهای Unit**: 6 فیلد
- **فیلدهای Room**: 9 فیلد
- **فیلدهای Bed**: 9 فیلد
- **فیلدهای Contract**: 12 فیلد
- **فیلدهای Resident**: 13 فیلد
- **فیلدهای Notes**: 7 فیلد (در آرایه)

**جمع کل فیلدهای اصلی در هر ردیف**: 51 فیلد + آرایه notes

---

## نکات مهم

1. هر ردیف در پاسخ API مربوط به یک Contract است
2. اگر یک Resident چندین Contract داشته باشد، برای هر Contract یک ردیف جداگانه ایجاد می‌شود
3. تمام Notes مربوط به یک Resident در هر ردیف قرار می‌گیرد
4. نام فیلدها دقیقاً مطابق با نام‌های موجود در دیتابیس است (با prefix برای جلوگیری از تداخل)
5. تاریخ‌های شمسی (jalali) به صورت خودکار محاسبه می‌شوند



