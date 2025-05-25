<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
class VotingSessionActive {
    public function handle(Request $request, Closure $next) {
        if (!Session::has('current_voting_election_id') || !Session::has('current_voting_student_id')) {
            // Podrías redirigir a la página de inicio del token, o a una página de error general.
        //    return redirect('/')->withErrors(['session_expired' => 'Su sesión de votación ha expirado o no es válida. Por favor, inicie de nuevo.']);
        }
        // También podrías re-verificar aquí si la elección sigue activa, y si el estudiante no ha votado aún.
        return $next($request);
    }
}