<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    public function create()
    {
        return view('officer.announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateAnnouncement($request);

        // Store the optional file in storage/app/public/announcements and keep only its path
        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('announcements', 'public');
        }

        Announcement::create([
            'user_id' => $request->user()->id, // always the logged-in user, never from the form
            'title' => $validated['title'],
            'body' => $validated['body'],
            'attachment' => $path,
        ]);

        Cache::forget('officer.stats'); // the Officer dashboard counts announcements

        return redirect()->route('announcements.index')->with('success', 'Announcement posted successfully.');
    }

    public function edit(Announcement $announcement)
    {
        return view('officer.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $this->validateAnnouncement($request);

        $data = [
            'title' => $validated['title'],
            'body' => $validated['body'],
        ];

        // A new file replaces the old one: delete the old file first, then store the new one
        if ($request->hasFile('attachment')) {
            if ($announcement->attachment) {
                Storage::disk('public')->delete($announcement->attachment);
            }

            $data['attachment'] = $request->file('attachment')->store('announcements', 'public');
        }

        $announcement->update($data);

        return redirect()->route('announcements.index')->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        // Remove the file from the public disk, then the database row
        if ($announcement->attachment) {
            Storage::disk('public')->delete($announcement->attachment);
        }

        $announcement->delete();

        Cache::forget('officer.stats'); // announcement count changed

        return redirect()->route('announcements.index')->with('success', 'Announcement deleted successfully.');
    }

    private function validateAnnouncement(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            // Optional PDF or image, maximum 5120 KB (5 MB)
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }
}