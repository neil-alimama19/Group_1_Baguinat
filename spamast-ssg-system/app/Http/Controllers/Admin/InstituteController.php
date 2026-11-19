<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InstituteController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $query = DB::table('institutes');
        
        if (!blank($search)) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }
        
        $institutes = $query->paginate(10)->withQueryString();
        
        return view('admin.institutes.index', compact('institutes', 'search'));
    }

    public function create()
    {
        return view('admin.institutes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:institutes,code',
            'name' => 'required|string|max:255',
        ]);

        DB::table('institutes')->insert([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.institutes.index')->with('success', 'Institute created successfully.');
    }

    public function edit($id)
    {
        $institute = DB::table('institutes')->find($id);
        
        if (!$institute) {
            return redirect()->route('admin.institutes.index')->with('error', 'Institute not found.');
        }

        return view('admin.institutes.edit', compact('institute'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:255', Rule::unique('institutes')->ignore($id)],
            'name' => 'required|string|max:255',
        ]);

        DB::table('institutes')->where('id', $id)->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.institutes.index')->with('success', 'Institute updated successfully.');
    }

    public function destroy($id)
    {
        // Check if institute has programs before deleting
        $hasPrograms = DB::table('programs')->where('institute_id', $id)->exists();

        if ($hasPrograms) {
            return redirect()->route('admin.institutes.index')->with('error', 'Cannot delete institute. It has programs assigned to it.');
        }

        DB::table('institutes')->where('id', $id)->delete();

        return redirect()->route('admin.institutes.index')->with('success', 'Institute deleted successfully.');
    }
}