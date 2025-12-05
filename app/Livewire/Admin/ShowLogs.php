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

    public $sortField = 'created_at';
    public $sortDir = 'desc';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDir = 'asc';
        }
    }

    public function render()
    {
        $query = ActivityLog::with('user');

        // Custom sorting logic
        if ($this->sortField === 'user_name') {
             $query->join('users', 'activity_logs.user_id', '=', 'users.id')
                   ->orderBy('users.name', $this->sortDir)
                   ->select('activity_logs.*');
        } else {
            $query->orderBy($this->sortField, $this->sortDir);
        }

        $logs = $query->paginate(10);

        return view('livewire.admin.show-logs', ['logs' => $logs]);
    }
}
