<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        // Use join to get the institute code
        $query = DB::table('programs')
            ->join('institutes', 'programs.institute_id', '=', 'institutes.id')
            ->select('programs.*', 'institutes.code as institute_code');
        
        if (!blank($search)) {
            $query->where('programs.name', 'like', "%{$search}%")
                  ->orWhere('programs.code', 'like', "%{$search}%");
        }
        
        $programs = $query->paginate(10)->withQueryString();
        
        return view('admin.programs.index', compact('programs', 'search'));
    }

    public function create()
    {
        $institutes = DB::table('institutes')->orderBy('name')->get();
        return view('admin.programs.create', compact('institutes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'institute_id' => 'required|exists:institutes,id',
            'code' => 'required|string|max:255|unique:programs,code',
            'name' => 'required|string|max:255',
        ]);

        DB::table('programs')->insert([
            'institute_id' => $validated['institute_id'],
            'code' => $validated['code'],
            'name' => $validated['name'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.programs.index')->with('success', 'Program created successfully.');
    }

    public function edit($id)
    {
        $program = DB::table('programs')->find($id);
        
        if (!$program) {
            return redirect()->route('admin.programs.index')->with('error', 'Program not found.');
        }

        $institutes = DB::table('institutes')->orderBy('name')->get();

        return view('admin.programs.edit', compact('program', 'institutes'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'institute_id' => 'required|exists:institutes,id',
            'code' => ['required', 'string', 'max:255', Rule::unique('programs')->ignore($id)],
            'name' => 'required|string|max:255',
        ]);

        DB::table('programs')->where('id', $id)->update([
            'institute_id' => $validated['institute_id'],
            'code' => $validated['code'],
            'name' => $validated['name'],
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.programs.index')->with('success', 'Program updated successfully.');
    }

    public function destroy($id)
    {
        // Check if program has students before deleting
        $hasStudents = DB::table('students')->where('program_id', $id)->exists();

        if ($hasStudents) {
            return redirect()->route('admin.programs.index')->with('error', 'Cannot delete program. It has students enrolled in it.');
        }

        DB::table('programs')->where('id', $id)->delete();

        return redirect()->route('admin.programs.index')->with('success', 'Program deleted successfully.');
    }
}