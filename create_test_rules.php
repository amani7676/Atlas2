<?php

// Create test rules
use App\Models\RuleCategory;
use App\Models\Rule;

$cat = RuleCategory::find(1);

Rule::create([
    'title' => 'سکوت در ساعات استراحت',
    'content' => '<p>لطفاً در ساعات استراحت (۱۲ ظهر تا ۲ بعد از ظهر و ۱۰ شب تا ۶ صبح) سکوت را رعایت کنید.</p><p>ممکن است سایر ساکنان نیاز به استراحت داشته باشند.</p>',
    'display_order' => 1,
    'is_active' => true,
    'rule_category_id' => 1
]);

Rule::create([
    'title' => 'تمیز نگه داشتن اتاق',
    'content' => '<p>اتاق خود را همیشه تمیز و مرتب نگه دارید.</p><p>زباله‌ها را در سطل‌های مخصوص قرار دهید و روزانه تخلیه کنید.</p>',
    'display_order' => 2,
    'is_active' => true,
    'rule_category_id' => 1
]);

echo "Test rules created successfully\n";
