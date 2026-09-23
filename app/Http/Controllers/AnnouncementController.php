<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Region;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Announcement::class);

        $authUser = auth()->user();

        // Admin sees all announcements; Regional users see Global (null) + local region announcements
        $announcements = Announcement::with(['user', 'region'])
            ->when($authUser->role !== 'Admin', function (Builder $query) use ($authUser) {
                $query->where(function (Builder $sub) use ($authUser) {
                    $sub->whereNull('region_id')
                        ->orWhere('region_id', $authUser->region_id);
                });
            })
            ->latest()
            ->paginate(10);

        $regions = $authUser->role === 'Admin'
            ? Region::all()
            : Region::where('id', $authUser->region_id)->get();

        return view('announcements.index', compact('announcements', 'regions'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Announcement::class);

        $authUser = auth()->user();

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'content'       => 'required|string',
            'external_link' => 'nullable|url',
            'pdf_file'      => 'nullable|mimes:pdf|max:5120', // Limit to 5MB
            'region_id'     => $authUser->role === 'Admin' ? 'nullable|exists:regions,id' : 'nullable',
        ]);

        $validated['user_id'] = $authUser->id;

        // Non-admins can only post to their assigned region
        if ($authUser->role !== 'Admin') {
            $validated['region_id'] = $authUser->region_id;
        }

        if ($request->hasFile('pdf_file')) {
            $validated['pdf_path'] = $request->file('pdf_file')->store('memos', 'public');
        }

        Announcement::create($validated);

        return back()->with('success', 'Announcement posted successfully!');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $this->authorize('update', $announcement);

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'content'       => 'required|string',
            'external_link' => 'nullable|url',
            'pdf_file'      => 'nullable|mimes:pdf|max:5120',
        ]);

        if ($request->hasFile('pdf_file')) {
            // Cleanup existing PDF
            if ($announcement->pdf_path && Storage::disk('public')->exists($announcement->pdf_path)) {
                Storage::disk('public')->delete($announcement->pdf_path);
            }
            $validated['pdf_path'] = $request->file('pdf_file')->store('memos', 'public');
        }

        $announcement->update($validated);

        return back()->with('success', 'Announcement updated successfully!');
    }

    public function destroy(Announcement $announcement)
    {
        $this->authorize('delete', $announcement);

        if ($announcement->pdf_path && Storage::disk('public')->exists($announcement->pdf_path)) {
            Storage::disk('public')->delete($announcement->pdf_path);
        }

        $announcement->delete();

        return back()->with('success', 'Announcement deleted successfully.');
    }
}