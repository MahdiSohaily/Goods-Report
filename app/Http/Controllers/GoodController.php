<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use App\Models\Good;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoodController extends Controller
{
    public function index()
    {
        $goods = DB::table('yadakshop1402.nisha')
            ->limit(10)
            ->orderBy('id', 'asc')
            ->get();

        $goods_count = DB::table('yadakshop1402.nisha')
            ->count();
        return Inertia::render('Goods/Show', ['goods' => $goods, 'count' => $goods_count]);
    }

    public function page(Request $request)
    {
        $page = $request->input('page');
        $pattern = $request->input('pattern');
        $limit = 10;
        $goods = null;
        $goods_count = DB::table('yadakshop1402.nisha')
            ->count();

        if ($pattern) {
            $goods_count = DB::table('yadakshop1402.nisha')
                ->where('partnumber', 'like', '%' . $pattern . '%')
                ->count();
            if ($goods_count > 10) {
                $goods = DB::table('yadakshop1402.nisha')
                    ->where('partnumber', 'like', '%' . $pattern . '%')
                    ->offset($limit * $page)
                    ->limit(10)
                    ->orderBy('id', 'asc')
                    ->get();
            } else {
                $goods = DB::table('yadakshop1402.nisha')
                    ->where('partnumber', 'like', '%' . $pattern . '%')
                    ->orderBy('id', 'asc')
                    ->get();
            }
        } else {
            $goods = DB::table('yadakshop1402.nisha')
                ->offset($limit * $page)
                ->limit(10)
                ->orderBy('id', 'asc')
                ->get();
        }

        return response()->json(['goods' => $goods, 'count' => $goods_count]);
    }

    public function search(Request $request)
    {
        $pattern = $request->input('pattern');
        $goods = null;
        if (strlen($pattern) > 0) {
            $goods_count = DB::table('yadakshop1402.nisha')
                ->where('partnumber', 'like', '%' . $pattern . '%')
                ->count();
            if ($goods_count > 10) {
                $goods = DB::table('yadakshop1402.nisha')
                    ->where('partnumber', 'like', '%' . $pattern . '%')
                    ->limit(10)
                    ->orderBy('id', 'asc')
                    ->get();
            } else {
                $goods = DB::table('yadakshop1402.nisha')
                    ->where('partnumber', 'like', '%' . $pattern . '%')
                    ->orderBy('id', 'asc')
                    ->get();
            }
        } else {
            $goods_count = DB::table('yadakshop1402.nisha')
                ->count();
            $goods = DB::table('yadakshop1402.nisha')
                ->limit(10)
                ->orderBy('id', 'asc')
                ->get();
        }

        return response()->json(['goods' => $goods, 'count' => $goods_count]);
    }

    public function create()
    {
        return Inertia::render('Goods/Partials/Create');
    }

    public function store(Request $request)
    {
        $serial = $request->input('serial');
        $price = $request->input('price');
        $weight = $request->input('weight');
        $mobis = $request->input('mobis');
        $korea = $request->input('korea');

        try {
            $good = new Good();
            $good->partnumber = $serial;
            $good->price = $price;
            $good->weight = $weight;
            $good->mobis = $mobis;
            $good->korea = $korea;

            $good->save();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function edit($good)
    {
        $good = DB::table('yadakshop1402.nisha')->where('id', '=', $good)->first();
        return Inertia::render('Goods/Partials/Update', ['good' => $good]);
    }

    public function Update(Request $request, $good)
    {
        $serial = $request->input('serial');
        $price = $request->input('price');
        $weight = $request->input('weight');
        $mobis = $request->input('mobis');
        $korea = $request->input('korea');

        DB::table('yadakshop1402.nisha')
            ->where('id', '=', $good)
            ->update([
                'partnumber' => $serial,
                'price' => $price,
                'weight' => $weight,
                'mobis' => $mobis,
                'korea' => $korea,
            ]);
        return Inertia::render('Goods/Partials/Update', ['good' => $good]);
    }

    public function delete($id)
    {
        DB::table('yadakshop1402.nisha')->delete($id);
    }
}
