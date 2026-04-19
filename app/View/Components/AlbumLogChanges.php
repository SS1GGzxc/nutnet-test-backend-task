<?php

namespace App\View\Components;

use App\Models\AlbumLog;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AlbumLogChanges extends Component
{
    public AlbumLog $log;

    public function __construct(AlbumLog $log)
    {
        $this->log = $log;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.album-log-changes');
    }
}
