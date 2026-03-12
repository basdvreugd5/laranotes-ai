<?php

namespace App\Http\Controllers\Api;

use App\Ai\Agents\NoteSummarizer;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $notes = Note::getFilteredForUser($request->user(), $request);

        return NoteResource::collection($notes);
    }

    public function store(StoreNoteRequest $request): NoteResource|RedirectResponse
    {
        $note = $request->user()->notes()->create($request->validated());

        if ($request->wantsJson()) {
            return new NoteResource($note);
        }

        return redirect()->route('dashboard')->with('status', 'Note created!');
    }

    public function update(UpdateNoteRequest $request, Note $note): NoteResource|RedirectResponse
    {
        $note->update($request->validated());

        if ($request->wantsJson()) {
            return new NoteResource($note);
        }

        return back()->with('status', 'Note updated!');
    }

    public function archive(Note $note): NoteResource|RedirectResponse
    {
        $this->authorize('archive', $note);

        $note->archive();

        if (request()->wantsJson()) {
            return new NoteResource($note);
        }

        return redirect()->route('dashboard')->with('status', 'Note archived!');
    }

    public function generateSummary(Note $note, NoteSummarizer $agent): JsonResponse
    {
        $this->authorize('update', $note);

        try {
            $note->generateSummary($agent);

            return response()->json([
                'message' => 'Summary appended successfully.',
                'note' => $note,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
