<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::with(['room', 'user'])->orderByDesc('created_at')->paginate(20);
        return response()->json($messages);
    }

    public function show(int $id)
    {
        $message = Message::with(['room', 'user'])->findOrFail($id);
        return response()->json($message);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'user_id' => ['required', 'exists:users,id'],
            'content' => ['required', 'string'],
        ]);

        $message = Message::create($validated);

        return response()->json($message, Response::HTTP_CREATED);
    }

    public function update(Request $request, int $id)
    {
        $message = Message::findOrFail($id);

        $validated = $request->validate([
            'room_id' => ['sometimes', 'exists:rooms,id'],
            'user_id' => ['sometimes', 'exists:users,id'],
            'content' => ['sometimes', 'string'],
        ]);

        $message->update($validated);

        return response()->json($message);
    }

    public function destroy(int $id)
    {
        $message = Message::findOrFail($id);
        $message->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}


