<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use App\Models\RuleCategory;
use Illuminate\Http\Request;

class InfoController extends Controller
{
    public function index(Request $request)
    {
        // Get active categories with their rules
        $categories = RuleCategory::where('is_active', true)
            ->with(['rules' => function($query) {
                $query->where('is_active', true)->orderBy('display_order');
            }])
            ->orderBy('display_order')
            ->get();
        
        // Filter out categories that have no rules
        $categories = $categories->filter(function($category) {
            return $category->rules->count() > 0;
        });

        return view('info.index', compact('categories'));
    }
}
