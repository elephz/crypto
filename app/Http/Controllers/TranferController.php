<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WalletTranfer;
use App\Http\Resources\TranferCollection;

class TranferController extends Controller
{
    public function index()
    {
        $tranfer = WalletTranfer::with([
            'from' => function ($query) {
                $query->with([
                    'user' => function ($query) {
                        $query->select('id', 'name');
                    },
                    'currency' => function ($query) {
                        $query->select('id', 'symbol', 'name');
                    },
                ]);
            }, 
            'to.user',
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return new TranferCollection($tranfer);
    }
}
