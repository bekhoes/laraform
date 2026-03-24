<?php

namespace Laraform\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ValidatorController extends Controller
{
    public function activeUrl(Request $request) {
        $validator = Validator::make($request->all(), [
            'url' => 'active_url',
        ]);

        return response()->json($validator->fails() ? false : true);
    }
}