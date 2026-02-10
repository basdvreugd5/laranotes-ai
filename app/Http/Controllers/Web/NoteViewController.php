<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;

class NoteViewController extends Controller
{
    public function index(Request $request)
    {
        $notes = Note::getFilteredForUser($request->user(), $request);

        return view('notes.index', compact('notes'));
    }
}
