<?php

namespace App\Http\Controllers;

use App\Models\Meet;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MeetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $meets = Meet::with('users')->orderBy('scheduled_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $meets->map(function ($meet) {
                return [
                    'id' => $meet->id,
                    'title' => $meet->title,
                    'youtube_link' => $meet->youtube_link,
                    'description' => $meet->description,
                    'scheduled_at' => $meet->scheduled_at?->format('Y-m-d H:i:s'),
                    'participants_count' => $meet->users->count(),
                    'is_participant' => Auth::check() ? $meet->isParticipant(Auth::user()) : false,
                    'created_at' => $meet->created_at->format('Y-m-d H:i:s'),
                ];
            })
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'youtube_link' => 'required|url',
            'description' => 'nullable|string',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $meet = Meet::create($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $meet,
            'message' => 'Meet created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Meet $meet): JsonResponse
    {
        $meet->load('users');
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $meet->id,
                'title' => $meet->title,
                'youtube_link' => $meet->youtube_link,
                'description' => $meet->description,
                'scheduled_at' => $meet->scheduled_at?->format('Y-m-d H:i:s'),
                'participants' => $meet->users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'joined_at' => $user->pivot->joined_at,
                    ];
                }),
                'participants_count' => $meet->users->count(),
                'is_participant' => Auth::check() ? $meet->isParticipant(Auth::user()) : false,
                'created_at' => $meet->created_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Meet $meet): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'youtube_link' => 'sometimes|required|url',
            'description' => 'nullable|string',
            'scheduled_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $meet->update($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $meet,
            'message' => 'Meet updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meet $meet): JsonResponse
    {
        $meet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Meet deleted successfully'
        ]);
    }

    /**
     * Join a meet
     */
    public function join(Meet $meet): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to join a meet'
            ], 401);
        }

        $user = Auth::user();

        if ($meet->isParticipant($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You are already a participant of this meet'
            ], 400);
        }

        $meet->users()->attach($user->id, ['joined_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully joined the meet'
        ]);
    }

    /**
     * Leave a meet
     */
    public function leave(Meet $meet): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to leave a meet'
            ], 401);
        }

        $user = Auth::user();

        if (!$meet->isParticipant($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a participant of this meet'
            ], 400);
        }

        $meet->users()->detach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Successfully left the meet'
        ]);
    }
}
