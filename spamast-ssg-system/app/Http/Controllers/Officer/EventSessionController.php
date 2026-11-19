<?php

namespace App\Http\Controllers\Officer;

use App\Http\Controllers\Controller;
use App\Models\EventSession;
use Illuminate\Http\Request;

class EventSessionController extends Controller
{
    public function toggle(Request $request, EventSession $session)
    {
        $type = $request->input('type'); // 'check_in' or 'check_out'

        if (!in_array($type, ['check_in', 'check_out'])) {
            return back()->with('error', 'Invalid session type.');
        }

        // Ensure the event is actually set to manual availability
        if ($session->event->availability !== 'manual') {
            return back()->with('error', 'Only manual events can be toggled.');
        }

        $field = "{$type}_open";
        $session->update([
            $field => !$session->$field
        ]);

        $status = $session->$field ? 'opened' : 'closed';
        $action = $type === 'check_in' ? 'Check-in' : 'Check-out';

        return back()->with('success', "{$action} {$status} successfully.");
    }
}