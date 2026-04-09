<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('roles');
        if($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
            $q->where('email', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%{$search}%"); // optional
            });
        }
         // Pagination (customizable)
        $perPage = $request->get('per_page', 5);
        $result = $query->latest()->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'message' => 'Fetch user data',
            'result' => $result
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
    public function store(StoreUserRequest $request)
    {
        try {
            $data = $request->validated();
            $data['password'] = bcrypt($data['password']);
            $user = User::create($data);
            return response()->json($user);
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(StoreUserRequest $request, User $user)
    {
         try {
            $data = $request->validated();
            if ($request->password) {
                $data['password'] = bcrypt($request->password);
            }
            $user->update($data);
            return response()->json($user);
         } catch (\Exceptions $e){
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
         }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        if (auth()->id() === $user->id) {
            return response()->json([
                'message' => 'Cannot delete yourself'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'Deleted successfully'
        ]);
    }

    /**
     * toggle
     */

    public function toggle($id){
        $user = User::findOrFail($id);
        $user->status = !$user->status;
        $user->save();
        return response()->json($user);
    }
}
