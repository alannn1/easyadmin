<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    private $title;
    private $generalUri;

    public function __construct()
    {
        $this->title = 'Dashboard';
        $this->generalUri = 'dashboard';
    }


    public function index()
    {
        $data['title'] = $this->title;

        $layout = (request('from_ajax') && request('from_ajax') == true) ? 'backend.idev.dashboard_ajax' : 'backend.idev.dashboard';

        $data['transactionRupiah'] = Transaction::sum('total_price');
        $data['transactionQuantity'] = Transaction::sum('quantity');
        $data['quantityChart'] = Transaction::select('product_name', DB::raw('SUM(quantity) as quantity'))
            ->groupBy(groups: 'product_name')
            ->get();
        $data['priceChart'] = Transaction::select('product_name', DB::raw('SUM(total_price) as total_price'))
            ->groupBy('product_name')
            ->get();
        
        return view($layout, $data);
    }

}