<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Throwable;

class TodoController extends Controller
{
    /**
     * Validate the request
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return array validated
     */
    private function validateRequest(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|max:255',
                'description' => 'required|max:255',
            ]);

            return $validated;
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Return Error Message for invalid request
     * 
     * @return \Illuminate\Http\Response
     */
    private function returnInvalidRequestError()
    {
        return response()->json([
            "success" => false,
            "message" => "Invalid Request",
        ], 400);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $todos = Todo::all();

        return response()->json([
            "success" => true,
            "message" => "Todo List",
            "data" => $todos
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validated = $this->validateRequest($request);
        if (!$validated) {
            return $this->returnInvalidRequestError();
        }

        try {
            $newTodo = Todo::create($validated);
        } catch (Throwable $e) {
            return response()->json([
                "success" => false,
                "message" => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            "success" => true,
            "message" => "Todo created successfully.",
            "data" => $newTodo
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $todo = Todo::find($id);
        if (is_null($todo)) {
            return response()->json([
                "success" => false,
                "message" => "Todo not found.",
            ], 404);
        }
        return response()->json([
            "success" => true,
            "message" => "Todo retrieved successfully.",
            "data" => $todo
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $this->validateRequest($request);
        if (!$validated) {
            return $this->returnInvalidRequestError();
        }

        $todo = Todo::find($id);
        if (is_null($todo)) {
            return response()->json([
                "success" => false,
                "message" => "Todo not found.",
            ], 404);
        }

        $todo->title = $validated['title'];
        $todo->description = $validated['description'];
        $todo->save();

        return response()->json([
            "success" => true,
            "message" => "Todo updated successfully.",
            "data" => $todo
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Todo $todo)
    {
        $todo->delete();
        return response()->json([
            "success" => true,
            "message" => "Todo deleted successfully.",
            "data" => $todo
        ]);
    }
}
