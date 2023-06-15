<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PriceController extends Controller
{
    public function index()
    {
        return Inertia::render('Price/Show');
    }

    public function load(Request $request)
    {

        if ($request->input('customer')) {
            $this->validateRequest($request->all());
            $customer = $request->input('customer');
            $completeCode = $request->input('code');
            $notification_id = $request->input('id') ? $request->input('id') : null;
            return $this->setup_loading($customer, $completeCode, $notification_id);
        } else {
            return Inertia::render('Price/Show');
        }
    }

    public function setup_loading($customer, $completeCode, $notification = null)
    {
        $explodedCodes = explode("\n", $completeCode);

        $results_arry = [
            'not_exist' => [],
            'existing' => [],
        ];


        $explodedCodes = array_map(function ($code) {
            return preg_replace('/[^a-z0-9]/i', '', $code);;
        }, $explodedCodes);


        foreach ($explodedCodes as $code) {

            $good = DB::table('nisha')->select('id', 'partnumber')->where('partNumber', 'like', "$code%")->get();
            if (count($good)) {
                $existing_code[$code] = $good;
            } else {
                array_push($results_arry['not_exist'], $code);
            }
        }

        $existing_code = [];
        foreach ($explodedCodes as $code) {

            $good = DB::table('nisha')->select('id', 'partnumber')->where('partNumber', 'like', "$code%")->get();
            if (count($good)) {
                $existing_code[$code] = $good;
            } else {
                array_push($results_arry['not_exist'], $code);
            }
        }

        $data = [];
        $relation_id = [];

        foreach ($explodedCodes as $code) {
            if (!in_array($code, $results_arry['not_exist'])) {
                $data[$code] = [];
                foreach ($existing_code[$code] as $item) {
                    $relation_exist = $this->isInRelation($item->id);
                    if ($relation_exist) {
                        if (!in_array($relation_exist, $relation_id)) {
                            array_push($relation_id, $relation_exist);
                            $data[$code][$item->partnumber]['information'] = $this->info($item->id);
                            $data[$code][$item->partnumber]['relation'] = $this->relations($item->id);
                            $data[$code][$item->partnumber]['givenPrice'] = $this->givenPrice($item->partnumber, $relation_exist);
                        }
                    } else {
                        $data[$code][$item->partnumber]['information'] = $this->info($item->id);
                        $data[$code][$item->partnumber]['relation'] = $this->relations($item->id);
                        $data[$code][$item->partnumber]['givenPrice'] = $this->givenPrice($item->partnumber);
                    }
                }
            }
        }

        //return $data;

        return Inertia::render('Price/Partials/Load', [
            'explodedCodes' => $explodedCodes,
            'not_exist' => $results_arry['not_exist'],
            'existing' => $data,
            'customer' => $customer,
            'completeCode' => $completeCode,
            'notification' => $notification,
            'rates' => $this->getSelectedRates()
        ]);
    }

    public function validateRequest($all_data)
    {
        Validator::make($all_data, [
            'customer' => 'required|exists:customers,id',
            'code' => 'required|string',

        ], [
            'required' => "وارد کردن :attribute الزامی می باشد.",
        ], [
            'customer' => 'مشتری',
            'code' => 'کد'
        ])->validate();
    }

    public function getSelectedRates()
    {
        $rates = DB::table('rates')
            ->select('amount', 'status')
            ->where('selected', '1')
            ->get();
        return $rates;
    }

    public function isInRelation($id)
    {
        $relation_id = DB::table('similars')->select('pattern_id')->where('nisha_id', $id)
            ->first();
        if ($relation_id) {
            return $relation_id->pattern_id;
        }
        return false;
    }

    public function info($id)
    {
        $isInRelation = DB::table('similars')->select('pattern_id')->where('nisha_id', $id)->first();
        $info = false;
        $cars = null;
        if ($isInRelation) {
            $info = DB::table('patterns')
                ->where('patterns.id', $isInRelation->pattern_id)
                ->first();

            if ($info->status_id) {
                $info = DB::table('patterns')
                    ->join('status', 'status.id', '=', 'patterns.status_id')
                    ->select('patterns.*', 'status.name AS  status_name')
                    ->where('patterns.id', $isInRelation->pattern_id)
                    ->first();
            }

            $cars = DB::table('patterncars')
                ->join('cars', 'cars.id', '=', 'patterncars.car_id')
                ->select('cars.name')
                ->where('patterncars.pattern_id', $isInRelation->pattern_id)
                ->get();
        }

        return $info ? ['relationInfo' => $info, 'cars' => $cars] : false;
    }

    public function relations($id)
    {
        $isInRelation = DB::table('similars')->select('pattern_id')->where('nisha_id', $id)->first();
        $relations = false;

        if ($isInRelation) {

            $relations = DB::table('nisha')
                ->join('similars', 'nisha.id', '=', 'similars.nisha_id')
                ->select('nisha.*')
                ->where('similars.pattern_id', $isInRelation->pattern_id)
                ->get();
        } else {
            $relations = DB::table('nisha')->where('id', $id)->get();
        }

        $existing = [];
        $stockinfo = [];
        $sortedGoods = [];
        foreach ($relations as $relation) {
            $existing[$relation->partnumber] = $this->exist($relation->id)['final'];
            $stockinfo[$relation->partnumber] = $this->exist($relation->id)['stockInfo'];
            $sortedGoods[$relation->partnumber] = $relation;
        }

        arsort($existing);
        $sorted = [];
        foreach ($existing as $key => $value) {
            $sorted[$key] = $this->getMax($value);
        }

        arsort($sorted);

        return ['goods' => $sortedGoods, 'existing' => $existing, 'sorted' => $sorted, 'stockInfo' => $stockinfo];
    }

    public function givenPrice($code, $relation_exist = null)
    {
        $ordared_price = null;
        if ($relation_exist) {
            $ordared_price = DB::table('patterns')
                ->select('patterns.price', 'patterns.created_at')
                ->where('id', $relation_exist)->first();
            $ordared_price->ordered = true;
        }

        $givenPrices = DB::table('prices')
            ->join('customers', 'customers.id', '=', 'prices.customer_id')
            ->where('partnumber', 'like', "$code%")
            ->orderBy('created_at', 'desc')
            ->limit(7)->get();


        $unsortedData = [...$givenPrices, $ordared_price];
        if ($relation_exist) {
            usort($unsortedData, function ($a, $b) {
                return $a->created_at < $b->created_at;
            });
        }
        $final_data = $relation_exist ? $unsortedData : $givenPrices;

        return  $final_data;
    }

    public function out($id)
    {
        $result =
            DB::table('exitrecord')
            ->select('qty')
            ->where('qtyid', $id)
            ->first();
        return $result;
    }

    public function stockInfo($id, $brand)
    {

        $brand_id = DB::table('brand')->select('id')->where('brand.name', '=', $brand)
            ->first();

        $result =
            DB::table('qtybank')
            ->select('qtybank.id', 'qtybank.qty', 'seller.name')
            ->join('seller', 'qtybank.seller', '=', 'seller.id')
            ->where('codeid', $id)
            ->where('brand', $brand_id->id)
            ->get();

        $existing_record = [];
        $customers = [];
        foreach ($result as $key => $item) {
            $out = $this->out($item->id) ? (int) $this->out($item->id)->qty : 0;
            $item->qty = (int)($item->qty) - $out;

            array_push($existing_record, $item);
            array_push($customers, $item->name);
        }

        $customers = array_unique($customers);

        $final_result = [];

        foreach ($customers as $customer) {
            $total = 0;
            foreach ($existing_record as $record) {
                if ($customer === $record->name) {
                    $total += $record->qty;
                }
            }

            $final_result[$customer] = $total;
        }

        return $final_result;
    }

    public function exist($id)
    {
        $result =
            DB::table('qtybank')
            ->join('brand', 'brand.id', '=', 'qtybank.brand')
            ->select('qtybank.id', 'codeid', 'brand.name', 'qty')
            ->where('codeid', $id)
            ->get();
        $brands = [];
        $amount = [];
        $stockInfo = [];

        foreach ($result as $key => $value) {
            $out = $this->out($value->id) ? (int) $this->out($value->id)->qty : 0;
            $value->qty = (int)($value->qty) - $out;

            array_push($brands, $value->name);
        }
        $brands = array_unique($brands);

        foreach ($brands as $key => $value) {
            $item = $value;
            $total = 0;
            foreach ($result as $key => $value) {
                if ($item == $value->name) {
                    $total += $value->qty;
                }
                $stockInfo[$value->name] =  $this->stockInfo($id, $value->name);
            }

            array_push($amount, $total);
        }
        $final = array_combine($brands, $amount);
        arsort($final);
        return ['stockInfo' => $stockInfo, 'final' => $final];
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'partnumber' => 'required|string|exists:nisha,partnumber'

        ], [
            'required' => "The :attribute field can't be empty.",
        ])->validate();


        $notification = $request->input('notification');

        DB::table('prices')->insert([
            'partnumber' => $request->input('partnumber'),
            'price' => $request->input('price'),
            'customer_id' => $request->input('customer'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        if ($notification) {
            DB::table('ask_price')
                ->where('id', $notification)
                ->update(['status' => 'done', 'notify' => 'received', 'price' => $request->input('price')]);
        }

        return $this->setup_loading($request->input('customer'), $request->input('completeCode'));
    }

    function getMax($array)
    {
        $max = 0;
        foreach ($array as $k => $v) {
            $max = $max < $v ? $v : $max;
        }
        return $max;
    }

    public function test($id = '277776', $brand = 'GEN')
    {
        $brand_id = DB::table('brand')->select('id')->where('brand.name', '=', $brand)
            ->first();

        $result =
            DB::table('qtybank')
            ->select('qtybank.id', 'qtybank.qty', 'seller.name')
            ->join('seller', 'qtybank.seller', '=', 'seller.id')
            ->where('codeid', $id)
            ->where('brand', $brand_id->id)
            ->get();

        $existing_record = [];
        $customers = [];
        foreach ($result as $key => $item) {
            $out = $this->out($item->id) ? (int) $this->out($item->id)->qty : 0;
            $item->qty = (int)($item->qty) - $out;

            array_push($existing_record, $item);
            array_push($customers, $item->name);
        }

        $customers = array_unique($customers);

        $final_result = [];

        foreach ($customers as $customer) {
            $total = 0;
            foreach ($existing_record as $record) {
                if ($customer === $record->name) {
                    $total += $record->qty;
                }
            }

            $final_result[$customer] = $total;
        }

        return $final_result;
    }

    public function getCustomerName(Request $request)
    {
        $id = $request->input('id');

        $customer = DB::table('customers')
            ->select('name')
            ->where('id', $id)
            ->first();
        return $customer->name;
    }

    function askPrice(Request $request)
    {
        $customer = $request->input('customer');
        $partNumber = $request->input('partNumber');

        DB::table('ask_price')->insert([
            'customer_id' => $customer,
            'user_id' => Auth::user()->id,
            'code' =>  $partNumber,
            'status' =>  'pending',
            'notify' =>  'send',
            'created_at' => Carbon::now(),
        ]);
    }

    function pricesetup(Request $request)
    {

        $customer = $request->input('customer');
        $completeCode = $request->input('code');

        return $this->setup_loading($customer, $completeCode);
    }
}
