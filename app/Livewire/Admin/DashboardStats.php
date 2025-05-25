<?php

namespace App\Livewire\Admin;

use App\Models\Candidate;
use App\Models\Election;
use App\Models\Student;
use Livewire\Component;

class DashboardStats extends Component
{
    public $totalElections;
    public $activeElections;
    public $completedElections;
    public $totalStudents;
    public $totalCandidates;

    /**
     * Inicializa los contadores de estadísticas del dashboard.
     */
    public function mount()
    {
        $this->totalElections = Election::count();
        $this->activeElections = Election::where('status', 'active')->count();
        $this->completedElections = Election::where('status', 'completed')->count();
        $this->totalStudents = Student::where('is_active', true)->count();
        $this->totalCandidates = Candidate::distinct('student_id')->count('student_id');
    }

    /**
     * Renderiza la vista del dashboard con las estadísticas.
     */
    public function render()
    {
        return view('livewire.admin.dashboard-stats')
               ->layout('components.layouts.app', ['title' => __('Dashboard')]);
    }
}