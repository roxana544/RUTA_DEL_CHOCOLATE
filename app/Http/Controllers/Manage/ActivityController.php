<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\TourStop;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ActivityController extends Controller
{
    public function index()
    {
        return view('manage.activities.index', [
            'stops' => TourStop::with(['tour', 'factory', 'activities'])->orderBy('tour_id')->orderBy('position')->get(),
        ]);
    }

    public function create()
    {
        return view('manage.activities.form', ['activity' => new Activity(['type' => 'checkpoint', 'max_points' => 10, 'is_active' => true]), 'stops' => $this->stops()]);
    }

    public function store(Request $request)
    {
        Activity::create($this->validated($request));

        return redirect()->route('manage.activities.index')->with('status', 'Actividad creada.');
    }

    public function edit(Activity $activity)
    {
        return view('manage.activities.form', ['activity' => $activity, 'stops' => $this->stops()]);
    }

    public function update(Request $request, Activity $activity)
    {
        $activity->update($this->validated($request));

        return redirect()->route('manage.activities.index')->with('status', 'Actividad actualizada.');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()->route('manage.activities.index')->with('status', 'Actividad eliminada.');
    }

    private function stops()
    {
        return TourStop::with(['tour', 'factory'])->orderBy('tour_id')->orderBy('position')->get();
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'tour_stop_id' => ['required', 'exists:factory_tour,id'],
            'name_es' => ['required', 'string', 'max:150'],
            'name_en' => ['required', 'string', 'max:150'],
            'description_es' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'type' => ['required', Rule::in(array_keys(Activity::TYPES))],
            'max_points' => ['required', 'integer', 'min:1', 'max:1000'],
            'question_es' => ['nullable', 'string', 'max:300', 'required_if:type,quiz'],
            'question_en' => ['nullable', 'string', 'max:300'],
            'options_es' => ['nullable', 'string', 'required_if:type,quiz'],
            'options_en' => ['nullable', 'string'],
            'answer' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);
        $lines = fn (?string $t) => collect(preg_split('/\R/', (string) $t))->map('trim')->filter()->values()->all();

        $config = null;
        if ($data['type'] === Activity::TYPE_QUIZ) {
            $config = [
                'question_es' => $data['question_es'], 'question_en' => $data['question_en'] ?? null,
                'options_es' => $lines($data['options_es']), 'options_en' => $lines($data['options_en'] ?? ''),
                'answer' => max(0, ((int) ($data['answer'] ?? 1)) - 1),
            ];
        }
        unset($data['question_es'], $data['question_en'], $data['options_es'], $data['options_en'], $data['answer']);
        $data['config'] = $config;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
