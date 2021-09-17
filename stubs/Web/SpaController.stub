<?php

namespace Ikoncept\Fabriq\Http\Controllers;

class SpaController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        if (request()->wantsJson()) {
            return response()->json('Get outta here!', 404);
        }

        return view('index');
    }
}
