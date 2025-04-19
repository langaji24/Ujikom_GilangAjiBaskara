<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Member;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $userCount = null;

        if (auth()->user()->role == 'superadmin') {
            $userCount = User::count();
        }

        $productCount = Product::count();
        $salesCount = Sale::count();
        $memberCount = Member::count();

        $salesMemberCount = Sale::whereNotNull('member_id')->count();

        $salesNonMemberCount = Sale::whereNull('member_id')->count();

        return view('home', compact('userCount', 'productCount', 'salesCount', 'memberCount', 'salesMemberCount', 'salesNonMemberCount'));
    }

    public function blank()
    {
        return view('layouts.blank-page');
    }
}
