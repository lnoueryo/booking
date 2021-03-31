<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ShopPlan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Rules\Duration;
use Illuminate\Support\Facades\Validator;

class ShopPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($sid)
    {
        $shop_plan = ShopPlan::where('shop_id', $sid)->get();
        return $shop_plan;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $sid)
    {
        $data = $request->validate([
            'title' => 'required|string|max:25',
            'types' => 'required|array',
            'price' => 'required|integer',
            'duration' => new Duration($request),
            'description' => 'required|string|max:120',
            'new_image' => 'required',
            ]);

        // $shop_plan = ShopPlan::where('shop_id', $sid);
        $image = $request->new_image;
        if(strpos($image,'data:image/png;') !== false){
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = Str::random(20).'.'.'png';
        } else {
            $image = str_replace('data:image/jpeg;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = Str::random(20).'.'.'jpg';
        }
        Storage::disk('plan')->put($imageName, base64_decode($image));
        // $request->types = json_encode($request->types);
        // dd($sid);
        return ShopPlan::create([
            'shop_id' => $sid,
            'title' => $data['title'],
            'types' => json_encode($data['types']),
            'price' => $data['price'],
            'duration' => $data['duration'],
            'description' => $data['description'],
            'image' => $imageName,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $sid, $id){
        $shop_plan = ShopPlan::where('shop_id', $sid)->find($id);
        
        DB::transaction(function () use ($request, $shop_plan) {
            if (!$request->new_image) {
                $request->types = json_encode($request->types);
                $shop_plan->fill($request->all())->update();
            } else {
                $image = $request->new_image;
                if(strpos($image,'data:image/png;') !== false){
                    $image = str_replace('data:image/png;base64,', '', $image);
                    $image = str_replace(' ', '+', $image);
                    $imageName = Str::random(20).'.'.'png';
                } else {
                    $image = str_replace('data:image/jpeg;base64,', '', $image);
                    $image = str_replace(' ', '+', $image);
                    $imageName = Str::random(20).'.'.'jpg';
                }
                Storage::disk('plan')->put($imageName, base64_decode($image));
                Storage::disk('plan')->delete($request->image);
                $request->types = json_encode($request->types);
                $shop_plan->fill($request->all())->update();
                $shop_plan->image = $imageName;
                $shop_plan->save();
            }
        });
        
        return $shop_plan;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
