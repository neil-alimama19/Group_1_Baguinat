<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            $path = $this->storeAttachment($request, $validated['title']); // readable file name
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

            $data['attachment'] = $this->storeAttachment($request, $validated['title']);
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

    // Saves the file as "<title-slug>-<8 random characters>.<extension>" and returns its path.
    // Example: "Midterm Schedule" -> announcements/midterm-schedule-k3f9a2xq.pdf
    private function storeAttachment(Request $request, string $title): string
    {
        $file = $request->file('attachment');

        // Str::slug makes the title URL-safe; limit it so the path always fits the 255-character column
        $slug = Str::slug(Str::limit($title, 50, '')) ?: 'announcement';

        // The random part keeps two files with the same title from overwriting each other
        $name = $slug . '-' . Str::lower(Str::random(8)) . '.' . $file->extension();

        return $file->storeAs('announcements', $name, 'public');
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