<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Resources\CurrencyCollection;

class CurrencyController extends Controller
{
    public function index(){
        return new CurrencyCollection(Currency::orderBy("id", "desc")->paginate());
    }
}
