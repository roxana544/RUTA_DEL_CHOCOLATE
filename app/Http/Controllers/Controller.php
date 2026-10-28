<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

abstract class Controller
{
    /**
     * Replace $data['image'] with the stored path of an uploaded file, deleting the old one.
     */
    protected function withImage(Request $request, array $data, ?Model $model, string $folder): array
    {
        unset($data['image']);

        if ($request->hasFile('image')) {
            if ($model?->image) {
                Storage::disk('public')->delete($model->image);
            }
            $data['image'] = $request->file('image')->store($folder, 'public');
        }

        return $data;
    }
}
