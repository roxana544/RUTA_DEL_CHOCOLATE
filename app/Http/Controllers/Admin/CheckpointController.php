<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Checkpoint;
use App\Models\ChocolateFactory;

class CheckpointController extends Controller
{
    public function index()
    {
        return view('admin.checkpoints.index', [
            'stats' => ChocolateFactory::orderBy('route_order')->withCount([
                'checkpoints',
                'checkpoints as verified_count' => fn ($q) => $q->where('gps_verified', true),
            ])->get(),
            'checkpoints' => Checkpoint::with(['user', 'factory'])->latest()->paginate(20),
        ]);
    }
}
