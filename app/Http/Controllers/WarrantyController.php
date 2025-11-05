<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WarrantyRequest;
use Illuminate\Support\Facades\Validator;

class WarrantyController extends Controller
{
    public function form()
    {
        return view('Warranty'); 
    }

    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'product' => 'required',
            'serial_numbers' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        WarrantyRequest::create($request->only([
            'phone', 'product', 'serial_numbers', 'note'
        ]));

        return redirect()->route('warranty')->with('success', 'Yêu cầu bảo hành đã được gửi!');
    }
}
