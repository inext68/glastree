<?php

namespace App\Http\Controllers;

use App\Models\Individuo;
use App\Models\Gruppo;
use App\Models\Notifica;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $stats = [
            'individui' => Individuo::count(),
            'gruppi' => Gruppo::count(),
            'notifiche' => $user->unreadNotificheCount(),
        ];

        $notifiche = $user->notifiche()->take(5)->get();

        return view('home.dashboard', compact('stats', 'notifiche'));
    }
}