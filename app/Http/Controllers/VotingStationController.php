<?php
namespace App\Http\Controllers;
use App\Models\Election;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class VotingStationController extends Controller
{
    public function showLoginScreen(Request $request, $token)
    {
     

        $election = Election::where('voting_session_token', $token)
                            // ->where('token_expires_at', '>', now()) // Si implementas expiración
                            ->where('status', 'active')
                            ->first();

        if (!$election) {
            return redirect('/')->withErrors(['token_error' => 'El enlace de votación no es válido, ha expirado o la elección no está activa.']);
        }

        Session::put('current_voting_election_id', $election->id);
        Session::forget('current_voting_student_id');

        return view('voting.identification-screen', ['election' => $election]);
    }
}