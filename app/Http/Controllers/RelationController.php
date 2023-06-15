<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pattern;
use App\Models\Similar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Carbon\Carbon;

class RelationController extends Controller
{
    public function index()
    {
        $status = DB::table('status')->get(); //get all the existing status from database
        $cars = DB::table('cars')->get(); //get all the registered cars in the database

        // return the component to be displayed with it's required data
        return Inertia::render('Relation/Show', ['status' => $status, 'cars' => $cars]);
    }

    public function search(Request $request)
    {
        $pattern = $request->input('pattern');
        $nisha = DB::table('nisha')
            ->where('partnumber', 'like', "$pattern%")->get();


        $searched_ids = [];

        foreach ($nisha as $key => $value) {
            array_push($searched_ids, $value->id);
        }

        $similar = DB::table('similars')->select('nisha_id', 'pattern_id')
            ->whereIn('nisha_id', $searched_ids)->get();

        $similar_ids = [];

        foreach ($similar as $key => $value) {
            array_push($similar_ids, ['pattern_id' => $value->pattern_id, 'nisha_id' => $value->nisha_id]);
        }

        $final_result = [];

        foreach ($nisha as $key => $value) {
            $id = $value->id;

            $get_nisha = null;

            foreach ($similar_ids as $item) {
                if ($item['nisha_id'] == $id) {
                    $get_nisha = $item['pattern_id'];
                }
            }
            array_push($final_result, ['id' => $id, 'partNumber' => $value->partnumber, 'pattern' => $get_nisha]);
        }

        return $final_result;
    }

    public function store(Request $request)
    {
        // START Validation the request
        Validator::make($request->all(), [
            'values' => 'required',
            'serial' => 'required',
            'name' => 'required',
            'serial' => 'required',
        ], [
            'required' => "مقدار :attribute خالی بوده نمی تواند.",
        ], [
            'name' => 'نام',
            'values' => 'اجناس انتخاب شده',
        ])->validate();
        // END validation

        $selected_index = $this->extract_id($request->input('values'));

        try {
            $selectedCars = $request->input('car_id');
            // create the pattern record
            $pattern = new Pattern();
            $pattern->name = $request->input('name');
            $pattern->price = $request->input('price');
            $pattern->serial = $request->input('serial');
            $pattern->status_id = $request->input('status_id');
            $pattern->save();

            $id = $pattern->id;

            foreach ($selected_index as $value) {
                $similar = new Similar();
                $similar->pattern_id = $id;
                $similar->nisha_id  = $value;
                $similar->save();
            }

            foreach ($selectedCars as $car) {
                DB::insert('insert into patterncars (pattern_id , car_id ) values (?, ?)', [$id, $car]);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update(Request $request)
    {
        // START Validation the request
        Validator::make($request->all(), [
            'serial' => 'required',
        ], [
            'required' => "The selected items section can't be empty.",
        ])->validate();
        // END validation


        $pattern_id = $request->input('pattern_id');

        $similar = DB::table('similars')->select('nisha_id')->where('pattern_id', $pattern_id)->get();

        $selected_index = $this->extract_id($request->input('values'));

        $current = [];
        foreach ($similar as $item) {
            array_push($current, $item->nisha_id);
        }

        $db_cars = DB::table('patterncars')->select('car_id')->where('pattern_id', $pattern_id)->get();

        $current_cars = [];
        foreach ($db_cars as $item) {
            array_push($current_cars, $item->car_id);
        }

        $toAdd = $this->toBeAdded($current, $selected_index);
        $toDelete = $this->toBeDeleted($current, $selected_index);

        $selectedCars = $request->input('car_id');
        $carsToAdd = $this->toBeAdded($current_cars, $selectedCars);
        $carsToDelete = $this->toBeDeleted($current_cars, $selectedCars);

        try {
            // create the pattern record
            $pattern = Pattern::find($pattern_id);
            $pattern->name = $request->input('name');
            $pattern->price = $request->input('price');
            $pattern->serial = $request->input('serial');
            $pattern->status_id = $request->input('status_id');
            $pattern->created_at = Carbon::now();
            $pattern->save();
            if (count($toAdd) > 0) {
                foreach ($toAdd as $value) {
                    $similar = new Similar();
                    $similar->pattern_id = $pattern_id;
                    $similar->nisha_id  = $value;
                    $similar->save();
                }
            }
            if (count($toDelete)) {
                foreach ($toDelete as $value) {
                    DB::table('similars')->where('nisha_id', $value)->delete();
                }
            }

            if (count($carsToAdd) > 0) {
                foreach ($carsToAdd as $value) {
                    DB::insert('insert into patterncars (pattern_id , car_id) values (?, ?)', [$pattern_id, $value]);
                }
            }
            if (count($carsToDelete)) {
                foreach ($carsToDelete as $value) {
                    DB::table('patterncars')->where('car_id', $value)->delete();
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function load(Request $request)
    {
        $pattern = $request->input('pattern');

        $result = DB::table('similars')->select('nisha_id')->where('pattern_id', $pattern)->get();
        $final_result = [];

        foreach ($result as $key => $value) {
            $item = DB::table('nisha')->select('id', 'partnumber')->where('id', $value->nisha_id)->first();

            array_push($final_result, ['id' =>  $item->id, 'partNumber' => $item->partnumber, 'pattern' => $value->nisha_id]);
        }

        return $final_result;
    }

    public function pattern(Request $request)
    {
        $id = $request->input('id');
        $cars = DB::table('patterncars')->select('car_id')->where('pattern_id', $id)->get();

        $cars_id = [];

        foreach ($cars as $key => $value) {
            array_push($cars_id, $value->car_id);
        }

        return ['pattern' => DB::table('patterns')->where('id', $id)->first(), 'cars' => $cars_id];
    }

    public function extract_id($array)
    {
        $selected_index = [];
        foreach ($array as $value) {
            array_push($selected_index, $value['id']);
        }
        $selected_index = array_unique($selected_index);
        return $selected_index;
    }

    public function toBeAdded($existing, $newComer)
    {
        $result = [];
        foreach ($newComer as $item) {
            if (!in_array($item, $existing)) {
                array_push($result, $item);
            }
        }
        return $result;
    }
    public function toBeDeleted($existing, $newComer)
    {
        $result = [];
        foreach ($existing as $item) {
            if (!in_array($item, $newComer)) {
                array_push($result, $item);
            }
        }
        return $result;
    }
}
