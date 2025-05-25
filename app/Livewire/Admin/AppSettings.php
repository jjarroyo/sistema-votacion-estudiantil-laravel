<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class AppSettings extends Component
{
    const KEY_REFRESH_INTERVAL = 'results_refresh_interval';
    const KEY_MIN_VOTES_THRESHOLD = 'results_min_votes_threshold';

    public $resultsRefreshInterval;
    public $resultsMinVotesThreshold;

    protected $defaultRefreshInterval = 60;
    protected $defaultMinVotesThreshold = 10;

    /**
     * Inicializa los valores del formulario con los datos actuales o por defecto.
     */
    public function mount()
    {
        $this->resultsRefreshInterval = Setting::find(self::KEY_REFRESH_INTERVAL)->value ?? $this->defaultRefreshInterval;
        $this->resultsMinVotesThreshold = Setting::find(self::KEY_MIN_VOTES_THRESHOLD)->value ?? $this->defaultMinVotesThreshold;
    }

    /**
     * Reglas de validación para los campos de configuración.
     */
    protected function rules()
    {
        return [
            'resultsRefreshInterval' => ['required', 'integer', 'min:5'],
            'resultsMinVotesThreshold' => ['required', 'integer', 'min:1'],
        ];
    }

    protected $messages = [
        'resultsRefreshInterval.required' => 'El intervalo de actualización es obligatorio.',
        'resultsRefreshInterval.integer' => 'El intervalo debe ser un número entero (segundos).',
        'resultsRefreshInterval.min' => 'El intervalo debe ser de al menos 5 segundos.',
        'resultsMinVotesThreshold.required' => 'El umbral mínimo de votos es obligatorio.',
        'resultsMinVotesThreshold.integer' => 'El umbral debe ser un número entero.',
        'resultsMinVotesThreshold.min' => 'El umbral debe ser de al menos 1 voto.',
    ];

    /**
     * Valida y guarda las configuraciones generales en la base de datos.
     */
    public function saveSettings()
    {
        $this->validate();

        try {
            Setting::updateOrCreate(
                ['key' => self::KEY_REFRESH_INTERVAL],
                ['value' => $this->resultsRefreshInterval]
            );
            Setting::updateOrCreate(
                ['key' => self::KEY_MIN_VOTES_THRESHOLD],
                ['value' => $this->resultsMinVotesThreshold]
            );

            session()->flash('message', 'Configuraciones guardadas exitosamente.');
            Log::info('Configuraciones de la aplicación actualizadas por el usuario: ' . auth()->id());

        } catch (\Exception $e) {
            Log::error('Error al guardar configuraciones: ' . $e->getMessage());
            session()->flash('message_type', 'error');
            session()->flash('message', 'Ocurrió un error al guardar las configuraciones.');
        }
    }

    /**
     * Renderiza la vista de configuración general.
     */
    public function render()
    {
        return view('livewire.admin.app-settings')
               ->layout('components.layouts.app', ['header' => 'Configuraciones Generales de Votación']);
    }
}