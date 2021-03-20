<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Booking;
class CalendarController extends Controller
{
    public function show($id)
    {

        $url = "https://lnoueryo98.sakura.ne.jp/seasons/api/booking/show/" . $id;//url次第
        $method = "GET";

        //接続
        $client = new Client();

        $response = $client->request($method, $url);
        $booking = $response->getBody();
        return $booking;
    }
}
