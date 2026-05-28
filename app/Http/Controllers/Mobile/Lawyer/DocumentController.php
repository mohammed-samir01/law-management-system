<?php

namespace App\Http\Controllers\Mobile\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\LegalCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(int $id): View
    {
        $case = LegalCase::with('documents')->findOrFail($id);

        return view('mobile.lawyer.cases.documents', compact('case'));
    }

    public function upload(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'file'  => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:20480',
            'title' => 'nullable|string|max:255',
        ]);

        $case = LegalCase::findOrFail($id);

        $document = $case->documents()->create([
            'office_id'   => auth()->user()->office_id,
            'title'       => ['ar' => $request->title ?: $request->file('file')->getClientOriginalName()],
            'type'        => 'attachment',
            'status'      => 'approved',
            'uploaded_by' => auth()->id(),
        ]);

        $document->addMediaFromRequest('file')
            ->toMediaCollection('files');

        return redirect()
            ->route('mobile.lawyer.cases.documents', $case->id)
            ->with('success', 'تم رفع المستند بنجاح');
    }
}
