<?php

/**
 * Asset Downloader for Laravel Project
 * Downloads all external CSS/JS libraries and saves them locally
 */

// External assets to download
$assets = [
    'css' => [
        'bootstrap' => [
            'url' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css',
            'local' => 'assets/css/bootstrap.min.css'
        ],
        'fontawesome' => [
            'url' => 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css',
            'local' => 'assets/css/fontawesome.min.css'
        ],
        'jalali-datepicker' => [
            'url' => 'https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.css',
            'local' => 'assets/css/jalalidatepicker.min.css'
        ]
    ],
    'js' => [
        'bootstrap' => [
            'url' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js',
            'local' => 'assets/js/bootstrap.bundle.min.js'
        ],
        'sweetalert2' => [
            'url' => 'https://cdn.jsdelivr.net/npm/sweetalert2@11',
            'local' => 'assets/js/sweetalert2.min.js'
        ],
        'chartjs' => [
            'url' => 'https://cdn.jsdelivr.net/npm/chart.js',
            'local' => 'assets/js/chart.min.js'
        ],
        'jquery' => [
            'url' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js',
            'local' => 'assets/js/jquery.min.js'
        ],
        'jalali-datepicker' => [
            'url' => 'https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.js',
            'local' => 'assets/js/jalalidatepicker.min.js'
        ]
    ],
    'fonts' => [
        'vazir' => [
            'url' => 'https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;700;900&display=swap',
            'local' => 'assets/css/vazir-font.css'
        ]
    ]
];

// Create directories if they don't exist
function ensureDirectoryExists($path) {
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "Created directory: $dir\n";
    }
}

// Download file with curl
function downloadFile($url, $localPath) {
    ensureDirectoryExists($localPath);
    
    echo "Downloading: $url\n";
    echo "Saving to: $localPath\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $data) {
        file_put_contents($localPath, $data);
        echo "✓ Successfully downloaded: $localPath\n";
        return true;
    } else {
        echo "✗ Failed to download: $url (HTTP: $httpCode)\n";
        return false;
    }
}

// Process Google Fonts CSS to download font files
function processGoogleFonts($cssContent, $cssPath) {
    // Extract font URLs from Google Fonts CSS
    preg_match_all('/url\(([^)]+)\)/', $cssContent, $matches);
    
    if (!empty($matches[1])) {
        $fontDir = 'assets/fonts/vazir/';
        if (!is_dir($fontDir)) {
            mkdir($fontDir, 0755, true);
        }
        
        foreach ($matches[1] as $fontUrl) {
            $fontUrl = trim($fontUrl, '"\'');
            if (strpos($fontUrl, 'http') === 0) {
                $fileName = basename($fontUrl);
                $localFontPath = $fontDir . $fileName;
                
                echo "Downloading font: $fontUrl\n";
                downloadFile($fontUrl, $localFontPath);
                
                // Replace URL in CSS with local path
                $cssContent = str_replace($fontUrl, '../' . $localFontPath, $cssContent);
            }
        }
    }
    
    return $cssContent;
}

// Download all assets
echo "Starting asset download...\n\n";

$successCount = 0;
$totalCount = 0;

foreach ($assets as $type => $files) {
    echo "\n=== Downloading $type ===\n";
    
    foreach ($files as $name => $asset) {
        $totalCount++;
        
        if ($type === 'fonts' && $name === 'vazir') {
            // Special handling for Google Fonts
            echo "Processing Google Fonts...\n";
            $cssContent = file_get_contents($asset['url']);
            if ($cssContent) {
                $processedContent = processGoogleFonts($cssContent, $asset['local']);
                ensureDirectoryExists($asset['local']);
                file_put_contents($asset['local'], $processedContent);
                echo "✓ Successfully processed Google Fonts: {$asset['local']}\n";
                $successCount++;
            }
        } else {
            if (downloadFile($asset['url'], $asset['local'])) {
                $successCount++;
            }
        }
    }
}

echo "\n=== Download Summary ===\n";
echo "Successfully downloaded: $successCount/$totalCount files\n";

if ($successCount === $totalCount) {
    echo "✓ All assets downloaded successfully!\n";
    echo "\nNext steps:\n";
    echo "1. Run 'npm run build' to rebuild your Vite assets\n";
    echo "2. Update your blade templates to use local assets\n";
    echo "3. Test your application offline\n";
} else {
    echo "✗ Some downloads failed. Please check the errors above.\n";
}

echo "\nDone!\n";
