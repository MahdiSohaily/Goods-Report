<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Rate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class RateController extends Controller
{
    public function index()
    {
        $rate = DB::table('rates')
            ->orderBy('amount', 'asc')
            ->get();
        return Inertia::render('Rates/Show', ['rates' => $rate]);
    }

    public function create()
    {
        return Inertia::render('Rates/Partials/Create');
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'amount' => 'required',
            'status' => 'required',
        ], [
            'required' => "The :attribute field can't be empty.",
        ])->validate();
        // END validation

        $amount = $request->input('amount');
        $status = $request->input('status');
        $selected = $request->input('selected');

        $rate = new Rate();
        $rate->amount = $amount;
        $rate->status = $status;
        $rate->selected = $selected;
        $rate->save();
    }

    public function edit($rate)
    {
        $rates = DB::table('rates')
            ->orderBy('amount', 'asc')
            ->get();
        $rate = Rate::find($rate);
        return Inertia::render('Rates/Partials/Update', ['rates' => $rates, 'rate' => $rate]);
    }

    public function update(Request $request, $rate)
    {
        $amount = $request->input('amount');
        $status = $request->input('status');
        $rate = Rate::find($rate);
        $rate->amount = $amount;
        $rate->status = $status;
        $rate->save();
    }

    public function toggleSelected(Request $request)
    {
        $id = $request->input('id');
        $value = $request->input('value');

        $rate = Rate::find($id);
        $rate->selected = $value;
        $rate->save();
    }

    public function delete($id)
    {
        DB::table('rates')->delete($id);
    }
}
