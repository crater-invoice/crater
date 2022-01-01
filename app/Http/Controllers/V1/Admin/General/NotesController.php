<?php

namespace Crater\Http\Controllers\V1\Admin\General;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\NotesRequest;
use Crater\Http\Resources\NoteResource;
use Crater\Models\Note;
use Illuminate\Http\Request;

class NotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view notes');

        $limit = $request->limit ?? 10;

        $notes = Note::latest()
            ->whereCompany()
            ->applyFilters($request->all())
            ->paginate($limit);

        return NoteResource::collection($notes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NotesRequest $request)
    {
        $this->authorize('manage notes');

        $note = Note::create($request->getNotesPayload());

        return new NoteResource($note);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function show(Note $note)
    {
        $this->authorize('view notes');

        return new NoteResource($note);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function update(NotesRequest $request, Note $note)
    {
        $this->authorize('manage notes');

        $note->update($request->getNotesPayload());

        return new NoteResource($note);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function destroy(Note $note)
    {
        $this->authorize('manage notes');

        $note->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
