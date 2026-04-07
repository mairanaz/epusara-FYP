<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;

class AdminKhairatFeeController extends Controller
{
    public function index()
    {
        $fees = Payment::with('user')->latest()->paginate(10);

        return view('admin.khairat.fees.index', compact('fees'));
    }
}