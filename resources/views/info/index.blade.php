<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قوانین و مقررات</title>
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Vazirmatn', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            color: white;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .categories-container {
            display: grid;
            gap: 30px;
        }

        .category-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .category-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
        }

        .category-title {
            text-align: center;
        }

        .category-title h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .category-title p {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .rules-list {
            padding: 25px;
        }

        .rule-item {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border-right: 4px solid #667eea;
            transition: all 0.3s ease;
        }

        .rule-item:hover {
            background: #f0f2ff;
            transform: translateX(5px);
        }

        .rule-item:last-child {
            margin-bottom: 0;
        }

        .rule-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 12px;
        }

        .rule-number {
            background: #667eea;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .rule-title {
            flex: 1;
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }

        .rule-content {
            color: #666;
            line-height: 1.8;
            font-size: 0.95rem;
            padding-right: 45px;
        }

        .rule-content p {
            margin-bottom: 10px;
        }

        .rule-content p:last-child {
            margin-bottom: 0;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: white;
        }

        .empty-state .material-icons {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.7;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            padding: 30px;
            color: white;
            opacity: 0.8;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            .header h1 {
                font-size: 2rem;
            }

            .header p {
                font-size: 1rem;
            }

            .categories-container {
                gap: 20px;
            }

            .category-header {
                padding: 15px 20px;
            }

            .category-title h2 {
                font-size: 1.3rem;
            }

            .rules-list {
                padding: 20px;
            }

            .rule-item {
                padding: 15px;
            }

            .rule-header {
                gap: 10px;
            }

            .rule-number {
                width: 25px;
                height: 25px;
                font-size: 0.8rem;
            }

            .rule-title {
                font-size: 1rem;
            }

            .rule-content {
                padding-right: 35px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 1.8rem;
            }

            .category-header {
                padding: 12px 15px;
            }

            .category-title h2 {
                font-size: 1.2rem;
            }

            .rules-list {
                padding: 15px;
            }

            .rule-item {
                padding: 12px;
            }

            .rule-content {
                padding-right: 0;
                padding-top: 10px;
            }

            .rule-header {
                margin-bottom: 8px;
            }
        }

        /* Loading Animation */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 200px;
            color: white;
        }

        .loading .material-icons {
            font-size: 48px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .header {
                color: #333;
                margin-bottom: 30px;
            }

            .category-card {
                box-shadow: none;
                border: 1px solid #ddd;
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .footer {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <h1>
                <span class="material-icons" style="vertical-align: middle; margin-left: 10px;">gavel</span>
                قوانین و مقررات
            </h1>
            <p>قوانین و مقررات خوابگاه با ترتیب دسته بندی شده</p>
        </header>

        <!-- Search and Quick Navigation -->
        <div class="search-section" style="background: white; border-radius: 12px; padding: 20px; margin-bottom: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <div class="rules-titles-list">
                <h6 style="margin-bottom: 15px; color: #333; font-weight: 600;">
                    <span class="material-icons" style="vertical-align: middle; margin-left: 5px; font-size: 20px;">list</span>
                    لیست سریع قوانین
                </h6>
                <div id="rulesList" style="display: flex; flex-wrap: wrap; gap: 8px;">
                    @foreach($categories as $category)
                        @foreach($category->rules as $rule)
                            <span class="rule-tag" data-category="{{ $category->name }}" data-rule="{{ $rule->title }}" style="background: #f0f2ff; color: #667eea; padding: 6px 12px; border-radius: 20px; font-size: 14px; cursor: pointer; transition: all 0.3s ease; border: 1px solid #e0e6ff;">
                                #{{ $rule->title }}
                            </span>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Categories and Rules -->
        <main class="categories-container">
            @if($categories->count() > 0)
                @foreach($categories as $category)
                    <div class="category-card">
                        <div class="category-header">
                            <div class="category-title">
                                <h2>{{ $category->name }}</h2>
                                @if($category->description)
                                    <p>{{ $category->description }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="rules-list">
                            @foreach($category->rules as $index => $rule)
                                <div class="rule-item">
                                    <div class="rule-header">
                                        <div class="rule-number">{{ $index + 1 }}</div>
                                        <div class="rule-title">{{ $rule->title }}</div>
                                    </div>
                                    <div class="rule-content">
                                        {!! $rule->content !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <span class="material-icons">folder_open</span>
                    <h3>هیچ قانونی یافت نشد</h3>
                    <p>در حال حاضر قانونی برای نمایش وجود ندارد</p>
                </div>
            @endif
        </main>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; {{ date('Y') }} - تمام حقوق محفوظ است</p>
            <p style="font-size: 0.9rem; margin-top: 5px;">
                <span class="material-icons" style="font-size: 16px; vertical-align: middle;">schedule</span>
                آخرین به‌روزرسانی: {{ \Carbon\Carbon::now()->format('Y/m/d H:i') }}
            </p>
        </footer>
    </div>

    <!-- JavaScript for interactions -->
    <script>
        // Rule tag click functionality
        document.addEventListener('DOMContentLoaded', function() {
            const ruleTags = document.querySelectorAll('.rule-tag');
            
            // Remove loading state if exists
            const loading = document.querySelector('.loading');
            if (loading) {
                loading.style.display = 'none';
            }
            
            ruleTags.forEach(tag => {
                tag.addEventListener('click', function() {
                    const ruleTitle = this.getAttribute('data-rule');
                    const categoryName = this.getAttribute('data-category');
                    
                    // Find and scroll to the rule
                    const ruleItems = document.querySelectorAll('.rule-item');
                    ruleItems.forEach(item => {
                        const title = item.querySelector('.rule-title').textContent;
                        if (title === ruleTitle) {
                            // Scroll to the rule
                            item.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            
                            // Highlight effect
                            item.style.backgroundColor = '#fff3cd';
                            setTimeout(() => {
                                item.style.backgroundColor = '';
                            }, 2000);
                        }
                    });
                });
                
                // Hover effect for tags
                tag.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#667eea';
                    this.style.color = 'white';
                    this.style.transform = 'translateY(-2px)';
                });
                
                tag.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '#f0f2ff';
                    this.style.color = '#667eea';
                    this.style.transform = 'translateY(0)';
                });
            });
        });

        // Smooth scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Add print functionality
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
        });
    </script>
</body>
</html>
