<?php

namespace App\Http\Controllers;

use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    private $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Get SMS credit as JSON for AJAX requests
     */
    public function getCredit(Request $request)
    {
        $creditData = $this->smsService->getCreditWithColor();
        
        return response()->json([
            'success' => true,
            'credit' => $creditData
        ]);
    }

    /**
     * Refresh SMS credit cache
     */
    public function refreshCredit(Request $request)
    {
        // Clear credentials cache to get fresh credentials
        app(SmsService::class)->clearCredentialsCache();
        
        // Get fresh data (no SMS credit cache to clear since we removed it)
        $creditData = $this->smsService->getCreditWithColor();
        
        return response()->json([
            'success' => true,
            'credit' => $creditData,
            'message' => 'اعتبار پیامک با موفقیت به‌روزرسانی شد'
        ]);
    }
}
