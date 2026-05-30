<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function index(Request $request) {
        return view('marketplace.index', ['type' => $request->type]);
    }
    public function show($id) {
        return view('marketplace.show', ['id' => $id]);
    }
}
