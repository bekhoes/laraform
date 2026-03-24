<?php

namespace Laraform\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laraform\Process\AutoProcess;
use Storage;

class FormController extends Controller
{
    public function process(Request $request)
    {
        return (new AutoProcess())->process($request);
    }

    public function trixAttachment(Request $request) {
        // sleep(1);
        
        $file = $request->file('file');

        $disk = config('laraform.trix.disk');

        $filename = $file->store(config('laraform.trix.folder'), $disk);

        return str_replace(env('APP_URL'), '', Storage::disk($disk)->url($filename));
    }

    public function csrf() {
        return response()->json([
            'X-CSRF-TOKEN' => csrf_token(),
        ]);
    }
}