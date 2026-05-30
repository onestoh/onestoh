<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    public function index() { return view('auctions.index'); }
    public function show($id) { return view('auctions.show', ['id' => $id]); }
}
