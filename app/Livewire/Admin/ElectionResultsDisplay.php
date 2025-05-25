<?php

namespace App\Livewire\Admin;

use App\Models\Election;
use App\Models\RecordedVote;
use App\Models\Candidate;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ElectionResultsDisplay extends Component
{
    public Election $election;

    public $results = [];
    public $blankVotesCount = 0;
    public $totalVotesProcessed = 0;
    public $grandTotalVotesInDb = 0;

    public $minVotesThreshold;
    public $refreshIntervalMs;

    public $showResults = false;
    public $lastUpdatedAt;
    public $thresholdMessage = '';

    public $winners = [];
    public $otherParticipants = [];

    /**
     * Inicializa el componente, carga configuración y resultados iniciales.
     */
    public function mount(Election $election)
    {
        $this->election = $election;
        $this->refreshIntervalMs = (int) app_setting('results_refresh_interval', 60) * 1000;
        $this->minVotesThreshold = (int) app_setting('results_min_votes_threshold', 10);
        $this->loadResults();
    }

    /**
     * Carga y calcula los resultados de la elección, ganadores y participantes.
     */
    public function loadResults()
    {
        $this->grandTotalVotesInDb = RecordedVote::where('election_id', $this->election->id)->count();

        if ($this->grandTotalVotesInDb < $this->minVotesThreshold && $this->election->status !== 'completed') {
            $this->showResults = false;
            $this->thresholdMessage = "Los resultados se mostrarán después de que se hayan emitido al menos {$this->minVotesThreshold} votos. Votos actuales: {$this->grandTotalVotesInDb}.";
            $this->results = [];
            $this->winners = [];
            $this->otherParticipants = [];
            $this->blankVotesCount = 0;
            $this->totalVotesProcessed = $this->grandTotalVotesInDb;
            $this->lastUpdatedAt = now();
            return;
        }
        
        $this->showResults = true;
        $this->thresholdMessage = '';
        $this->totalVotesProcessed = $this->grandTotalVotesInDb;

        $voteCounts = RecordedVote::where('election_id', $this->election->id)
            ->whereNotNull('candidate_id')
            ->select('candidate_id', DB::raw('count(*) as votes'))
            ->groupBy('candidate_id')
            ->pluck('votes', 'candidate_id');

        $candidates = Candidate::where('election_id', $this->election->id)
                               ->with('student:id,full_name')
                               ->get();
        
        $tempResults = [];
        foreach ($candidates as $candidate) {
            $votes = $voteCounts->get($candidate->id, 0);
            $tempResults[] = [
                'id' => $candidate->id,
                'candidate_name' => $candidate->student->full_name ?? 'Candidato Desconocido',
                'photo_path' => $candidate->photo_path,
                'list_number' => $candidate->list_number,
                'votes' => $votes,
                'percentage' => $this->totalVotesProcessed > 0 ? round(($votes / $this->totalVotesProcessed) * 100, 1) : 0,
            ];
        }

        $this->blankVotesCount = RecordedVote::where('election_id', $this->election->id)
            ->where('is_blank_vote', true)
            ->count();

        if ($this->election->status === 'completed' && $this->totalVotesProcessed > 0) {
            usort($tempResults, function ($a, $b) {
                return $b['votes'] <=> $a['votes'];
            });

            $this->winners = [];
            $this->otherParticipants = [];
            
            if (!empty($tempResults)) {
                $maxVotes = $tempResults[0]['votes'];
                if ($maxVotes > 0) {
                    foreach ($tempResults as $result) {
                        if ($result['votes'] === $maxVotes) {
                            $this->winners[] = $result;
                        } else {
                            $this->otherParticipants[] = $result;
                        }
                    }
                } else {
                    $this->otherParticipants = $tempResults;
                }
            }
            $this->results = [];
        } else {
            usort($tempResults, function ($a, $b) {
                return ($a['list_number'] ?? $a['candidate_name']) <=> ($b['list_number'] ?? $b['candidate_name']);
            });
            $this->results = $tempResults;
            $this->winners = [];
            $this->otherParticipants = [];
        }

        $this->lastUpdatedAt = now();
    }

    /**
     * Renderiza la vista de resultados de la elección.
     */
    public function render()
    {
        return view('livewire.admin.election-results-display')
               ->layout('components.layouts.app', ['header' => 'Resultados Parciales: ' . $this->election->name]);
    }
}