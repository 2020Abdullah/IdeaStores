<?php

namespace App\View\Components;

use App\Models\App;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LogoComponent extends Component
{
    public $logo;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->logo = App::latest()->pluck('logo')->first();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.logo-component');
    }
}
