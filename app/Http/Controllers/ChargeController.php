<?php

namespace App\Http\Controllers;

use App\Models\Charge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;

class ChargeController extends Controller
{

    public function index()
    {
       $charges = Charge::where('user_id', Auth::id())->get();
        return response()->json($charges);
    }

    public function create()
    {
        //
    }

 
    public function store(Request $request)
    {
        $validated = $request->validate([
           'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
        ]);

        $dataVencimento = Carbon::parse($validated['due_date'])->setTimeFrom(Carbon::now('America/Sao_Paulo'));

        $charge = Charge::create([
            'user_id' => Auth::id(),
            'amount' => $validated['amount'],
            'due_date' => $dataVencimento,
            'status' => 'pending',
        ]);

         return response()->json($charge, 201);
    }

    public function show($id)
{
    $charge = Charge::where('id', $id)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();

    return response()->json($charge);
}


    public function edit(Charge $charge)
    {
        //
    }

    public function update(Request $request, Charge $charge, $id)
    {
        $charge = Charge::where('id', $id)
                        ->where('user_id', Auth::id())
                        ->firstOrFail();

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(['paid', 'canceled']),
            ],
        ]);

        if ($charge->status !== 'pending') {
            return response()->json(['error' => 'Cobrança já foi paga ou cancelada.'], 400);
        }

        $charge->status = $validated['status'];
        $charge->save();

        return response()->json($charge);
    }

    public function destroy(Charge $charge, $id)
    {
        $charge = Charge::where('id', $id)
                        ->where('user_id', Auth::id())
                        ->firstOrFail();

        if ($charge->status !== 'pending') {
            return response()->json(['error' => 'Apenas cobranças pendentes podem ser excluídas.'], 400);
        }

        $charge->delete();

        return response()->json(null, 204);
    }
}
