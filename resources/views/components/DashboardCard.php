<?php
// app/View/Components/DashboardCard.php

namespace App\View\Components;

use Illuminate\View\Component;

class DashboardCard extends Component
{
    // KHAI BÁO TẤT CẢ BIẾN
    public $title;
    public $value;
    public $change;
    public $icon;
    public $color;

    public function __construct(
        $title,
        $value,           // ← BẮT BUỘC
        $change = null,   // ← TÙY CHỌN
        $icon = '',       // ← DEFAULT RỖNG
        $color = 'blue'   // ← DEFAULT
    ) {
        $this->title = $title;
        $this->value = $value;     // ← GÁN GIÁ TRỊ
        $this->change = $change;
        $this->icon = $icon;
        $this->color = $color;
    }

    public function render()
    {
        return view('components.dashboard-card');
    }
}