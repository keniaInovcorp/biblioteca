<?php

namespace App\Livewire\Admin;

use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ShowLogs extends Component
{
    use WithPagination;

    public function render()
    {
        $logs = ActivityLog::with('user')->latest()->paginate(10);
        return view('livewire.admin.show-logs', ['logs' => $logs]);
    }
}
