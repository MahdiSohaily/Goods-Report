<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index()
    {
        $rates = DB::table('rates')
            ->select('amount', 'status')
            ->orderBy('amount', 'asc')
            ->get();
        return Inertia::render('Dashboard', ['rates' => $rates]);
    }

    public function search(Request $request)
    {
        $pattern = $request->input('pattern');
        $result = DB::table('nisha')->where('partnumber', 'like', "$pattern%")->get();
        return response()->json([$result]);
    }

    public function mobis(string $mobis = null)
    {
        $rates = DB::table('rates')
            ->select('amount', 'status')
            ->orderBy('amount', 'asc')
            ->get();

        $good = DB::table('nisha')->where('partnumber', '=', "$mobis")->first();

        $result = $this->checkMobis($mobis, $good);

        return Inertia::render('Mobis/Mobis', ['rates' => $rates, 'result' => $result]);
    }

    /** ================================== Helper functions section =============================================== */

    // A function to get the destination page response from it's response header (get_http_response_code)
    public function get_http_response_code($url)
    {
        ini_set('user_agent', 'Mozilla/5.0');
        $headers = get_headers($url);
        return substr($headers[0], 9, 3);
    }


    // A function to check for mobis label in the destination page
    public function checkMobis($mobis, $good)
    {
        $context = stream_context_create(array("http" => array("header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36")));

        $item = [];

        if ($this->get_http_response_code("https://partsmotors.com/products/$mobis") != "200") {

            DB::update(
                "update nisha set mobis = '-' where partnumber = ?",
                [$mobis]
            );
            return $item;
        } else {
            require_once 'simple_html_dom.php'; // A php file which converts the response text to HTML DOM

            $html = file_get_contents("https://partsmotors.com/products/$mobis", false, $context);

            $html = str_get_html($html);
            $price = null;
            foreach ($html->find('meta[property=og:price:amount]') as $element) {
                $price = $element->content;
            }

            $price = str_replace(",", "", $price);
            // Updating the current item mobis
            DB::update(
                "update nisha set mobis = ? where partnumber = ?",
                [$price, $mobis]
            );

            $item = [
                'id' => $good->id,
                'partNumber' => $good->partnumber,
                'price' => $price,
                'avgPrice' => round($price * 100 / 243.5 * 1.1),
            ];
        }
        return $item;
    }
}
