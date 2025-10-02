<?php

namespace App\Http\Controllers;

use App\Models\RoomParticipant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoomParticipantController extends Controller
{
    public function index()
    {
        $participants = RoomParticipant::with(['room', 'user'])->paginate(15);
        return response()->json($participants);
    }

    public function show(int $id)
    {
        $participant = RoomParticipant::with(['room', 'user'])->findOrFail($id);
        return response()->json($participant);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'user_id' => ['required', 'exists:users,id'],
            'joined_at' => ['nullable', 'date'],
        ]);

        $participant = RoomParticipant::create($validated);

        return response()->json($participant, Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id)
    {
        $participant = RoomParticipant::findOrFail($id);

        $validated = $request->validate([
            'room_id' => ['sometimes', 'exists:rooms,id'],
            'user_id' => ['sometimes', 'exists:users,id'],
            'joined_at' => ['nullable', 'date'],
        ]);

        $participant->update($validated);

        return response()->json($participant);
    }

    public function destroy(int $id)
    {
        $participant = RoomParticipant::findOrFail($id);
        $participant->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}


