<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomParticipant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('host')->withCount('participants')->paginate(15);
        return response()->json($rooms);
    }

    public function show(int $id)
    {
        $room = Room::with(['host', 'participants.user'])->findOrFail($id);
        return response()->json($room);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'host_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level' => ['nullable', Rule::in(['Beginner','Intermediate','Advanced'])],
            'topic' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
        ]);

        $room = Room::create($validated);
        RoomParticipant::create([
            'room_id' => $room->id,
            'user_id' => Auth::id(),
            'joined_at' => now(),
        ]);

        return response()->json($room, Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id)
    {
        $room = Room::findOrFail($id);

        $validated = $request->validate([
            'host_id' => ['sometimes', 'exists:users,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'level' => ['nullable', Rule::in(['Beginner','Intermediate','Advanced'])],
            'topic' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'max_participants' => ['nullable', 'integer', 'min:1'],
        ]);

        $room->update($validated);

        return response()->json($room);
    }

    public function destroy(int $id)
    {
        $room = Room::findOrFail($id);
        $room->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    
    public function join(Request $request, $roomId)
    {
        $room = Room::findOrFail($roomId);

        if ($room->password !== $request->password) {
            return response()->json(['error' => 'Wrong password'], 403);
        }

        RoomParticipant::firstOrCreate(
            ['room_id' => $room->id, 'user_id' => Auth::id()],
            ['joined_at' => now()]
        );

        return response()->json(['message' => 'Joined successfully']);
    }
}


