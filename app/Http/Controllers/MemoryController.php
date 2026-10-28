<?php

namespace App\Http\Controllers;

use App\Models\Memory;
use App\Models\Photo;

class MemoryController extends Controller
{
    /** El recuerdo se comparte por enlace con token no adivinable. */
    public function show(Memory $memory)
    {
        $memory->load(['event.tour', 'user']);

        return view('memories.show', [
            'memory' => $memory,
            'photos' => Photo::with('stop.factory')->where('event_id', $memory->event_id)->where('user_id', $memory->user_id)->oldest()->get(),
        ]);
    }
}
