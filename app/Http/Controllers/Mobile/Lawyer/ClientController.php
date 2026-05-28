<?php

namespace App\Http\Controllers\Mobile\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $clients = Client::withCount('cases')
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhereJsonContains('name->ar', $search);
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('mobile.lawyer.clients.index', compact('clients'));
    }

    public function show(int $id): View
    {
        $client = Client::with(['cases' => fn($q) => $q->latest()->limit(20)])->findOrFail($id);

        return view('mobile.lawyer.clients.show', compact('client'));
    }
}
