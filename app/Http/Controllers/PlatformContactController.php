<?php

namespace App\Http\Controllers;

use App\Models\PlatformLead;
use Illuminate\Http\Request;

class PlatformContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'max:150'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        PlatformLead::create($validated + ['status' => 'new']);

        return response()->json([
            'success' => true,
            'message' => __('messages.contact_success'),
        ]);
    }
}
