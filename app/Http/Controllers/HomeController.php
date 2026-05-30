<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() { return view('welcome'); }
    public function financing() { return view('pages.financing'); }
    public function verification() { return view('pages.verification'); }
    public function about() { return view('pages.about'); }
}
