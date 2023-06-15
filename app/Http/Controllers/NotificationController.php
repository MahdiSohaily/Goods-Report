<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $adminNotification = [];
        if (Auth::user()->name == 'admin') {

            $adminNotification = DB::table('ask_price')
                ->where('status', '=', 'pending')->get();
        }

        $hasNotification = DB::table('ask_price')
            ->where('user_id', '=', Auth::user()->id)
            ->where('notify', '=', 'received')
            ->get();

        return count([...$hasNotification, ...$adminNotification]);
    }

    public function getNotification()
    {
        $adminNotification = [];
        if (Auth::user()->name === 'نیایش') {
            $adminNotification = DB::table('ask_price')
                ->join('users', 'users.id', '=', 'ask_price.user_id')
                ->join('customers', 'customers.id', '=', 'ask_price.customer_id')
                ->select('ask_price.*', 'users.id AS user_id', 'customers.id AS customer_id', 'customers.name AS customer_name', 'users.name AS user_name')
                ->where('status', '=', 'pending')->get();
        }

        $answeredNotifications = DB::table('ask_price')
            ->join('users', 'users.id', '=', 'ask_price.user_id')
            ->join('customers', 'customers.id', '=', 'ask_price.customer_id')
            ->select('ask_price.*', 'users.id AS user_id', 'customers.id AS customer_id', 'customers.name AS customer_name', 'users.name AS user_name')
            ->where('notify', '=', 'received')
            ->where('user_id', '=', Auth::user()->id)
            ->get();

        $previousNotifications = DB::table('ask_price')
            ->join('users', 'users.id', '=', 'ask_price.user_id')
            ->join('customers', 'customers.id', '=', 'ask_price.customer_id')
            ->select('ask_price.*', 'users.id AS user_id', 'customers.id AS customer_id', 'customers.name AS customer_name', 'users.name AS user_name')
            ->where('user_id', '=', Auth::user()->id)
            ->where('notify', '=', 'done')->get();

        return Inertia::render('Notifications/Show', [
            'answeredNotifications' => $answeredNotifications,
            'adminNotification' => $adminNotification,
            'previousNotifications' => $previousNotifications,
            'admin' => Auth::user()->name === 'نیایش' ? true : false,
        ]);
    }

    function clearNotification(Request $request)
    {
        $code = $request->input('code');
        $customer = $request->input('customer');
        $id = $request->input('id');

        if ($id) {
            DB::table('ask_price')
                ->where('id', $id)
                ->update(['status' => 'done', 'notify' => 'received', 'price' => 'نداریم']);
        }

        if ($customer) {
            DB::table('prices')->insert([
                'partnumber' => $code,
                'price' => 'نداریم',
                'customer_id' => $customer,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        return  redirect()->route('notification.get');
    }


    function readNotification(Request $request)
    {
        $id = $request->input('id');

        if ($id) {
            DB::table('ask_price')
                ->where('id', $id)
                ->update(['status' => 'done', 'notify' => 'done']);
        }

        return  redirect()->route('notification.get');
    }
}
