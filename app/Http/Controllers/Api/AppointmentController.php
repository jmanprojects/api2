<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AppointmentService;


class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    public function index()
    {
        return response()->json($this->appointmentService->getAll());
    }

    public function store(Request $request)
    {
        $appointment = $this->appointmentService->create($request->all(), $request->user());
        return response()->json($appointment, 201);
    }

    public function show($id)
    {
        return response()->json($this->appointmentService->find($id));
    }

    public function update(Request $request, $id)
    {
        $appointment = $this->appointmentService->update($id, $request->all());
        return response()->json($appointment);
    }

    public function destroy($id)
    {
        $this->appointmentService->delete($id);
        return response()->noContent();
    }
}




//class AppointmentController extends Controller
//{
    /**
     * Display a listing of the resource.
     */
  //  public function index()
   // {
        //
    //}

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
      
    // }

    /**
     * Display the specified resource.
     */
    // public function show(string $id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     */
//     public function destroy(string $id)
//     {
//         //
//     }
// }

