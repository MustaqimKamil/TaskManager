<?php

namespace App\Http\Controllers;

use App\Models\Lists;
use App\Models\Tasks;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Get all lists for the user
        $lists = Lists::where('user_id', $user->id)->get();
        
        // Get all tasks through the user's lists
        $tasks = Tasks::whereHas('list', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();
        
        $stats = [
            'totalLists' => $lists->count(),
            'totalTasks' => $tasks->count(),
            'completedTasks' => $tasks->where('is_completed', true)->count(),
            'pendingTasks' => $tasks->where('is_completed', false)->count(),
        ];

        return Inertia::render('dashboard', [
            'stats' => $stats,
            'lists' => $lists,
            'tasks' => $tasks,
            'flash' => [
                'success' => session('success'),
                'error' => session('error')
            ]
        ]);
    }
}
