<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CkeditorEditor extends Component
{
    public $content;

    public function __construct($content = '')
    {
        $this->content = $content;
    }

    public function render(): View|Closure|string
    {
        return view('components.forms.ckeditor-editor');
    }
}
