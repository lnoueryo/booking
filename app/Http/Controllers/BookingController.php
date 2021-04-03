<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Booking;
use App\Plan;
use App\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\NewBooking;
use App\Events\ChangeBooking;
use App\User;

class BookingController extends Controller
{
    public $booking;
    public $user;
    public function create(Request $request, $sid){
        DB::transaction(function () use ($request, $sid) {
            if($request->user['id']==''){
                $this->user = User::create([
                    'first_name' => $request->user['first_name'],
                    'last_name' => $request->user['last_name'],
                    'email' => $request->user['email'],
                    'phone' => $request->user['phone'],
                    'memo' => $request->user['memo'],
                ]);
            } else {
                $this->user = $request->user;
            }
            $this->booking = new Booking;
            $this->booking->fill($request->all());
            $this->booking->user_id = $this->user['id'];
            $this->booking->shop_id = $sid;
            $this->booking->status = 1;
            $this->booking->save();
            $payment = new Payment;
            $payment->price = $request->price;
            $payment->paid = false;
            $payment->method = 'cash';
            $payment->booking_id = $this->booking->id;
            $payment->save();
        });
        $booking = Booking::with(['user', 'payment'])->find($this->booking['id']);
        event(new NewBooking($booking));
        return $booking;
    }

    public function store(Request $request){
        DB::transaction(function () use ($request) {
            $this->booking = new Booking;
            $this->booking->fill($request->all());
            $this->booking->from = $request->from;
            $this->booking->to = $request->to;
            $this->booking->save();
            // if (is_array($request->title)) {
            //     for ($i=0; $i < count($request->title); $i++) { 
            //         $plan = new Plan;
            //         $plan->title = $request->title[$i];
            //         $plan->user_id = $request->user_id;
            //         $plan->booking_id = $this->booking->id;
            //         $plan->save();
            //     }
            // } else {
            //     $plan = new Plan;
            //     $plan->title = $request->title;
            //     $plan->user_id = $request->user_id;
            //     $plan->booking_id = $this->booking->id;
            //     $plan->save();
            // }
            $payment = new Payment;
            $payment->price = $request->price;
            $payment->paid = false;
            $payment->method = 'cash';
            $payment->booking_id = $this->booking->id;
            $payment->save();
        });
        $booking = Booking::with(['user', 'payment'])->find($this->booking->id);
        event(new NewBooking($booking));
        return $booking;
    }
    
    public function index($sid){
        $time = Carbon::today('Asia/Tokyo')->getTimestamp()*1000;
        $bookings = Booking::with(['user', 'payment'])->where('shop_id', $sid)->where('from', '>=', $time)->where('cancelled', false)->get();
        return $bookings;
    }
    
    public function show($sid, $id){
        $booking = Booking::with(['user', 'payment'])->find($id);
        return $booking;
    }
    
    public function update(Request $request, $sid, $id){
        $booking = Booking::with(['user', 'payment'])->where('shop_id', $sid)->find($id);
        $booking->fill($request->all())->update();
        event(new ChangeBooking($booking));
        return $booking;
    }
    
    public function destroy($id){
        $booking = Booking::find($id);
        $booking->delete();
        return $booking;
    }
}
