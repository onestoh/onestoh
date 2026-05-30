<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() { return redirect('/dashboard/landlord'); }
    public function admin() { return view('dashboard.admin.index'); }
    public function landlord() { return view('dashboard.landlord.index'); }
    public function broker() { return view('dashboard.broker-licensed.index'); }
    public function promoter() { return view('dashboard.broker-unlicensed.index'); }
    public function tenant() { return view('dashboard.tenant.index'); }
    public function developer() { return view('dashboard.developer.index'); }
    public function valuer() { return view('dashboard.valuer.index'); }
    public function surveyor() { return view('dashboard.surveyor.index'); }
    public function auctioneer() { return view('dashboard.auctioneer.index'); }
    public function investor() { return view('dashboard.investor.index'); }
    public function corporate() { return view('dashboard.corporate.index'); }
    public function propertyManager() { return view('dashboard.property-manager.index'); }
    public function finance() { return view('dashboard.finance.index'); }
    public function messages() { return view('dashboard.shared.messages'); }
    public function notifications() { return view('dashboard.shared.notifications'); }
    public function settings() { return view('dashboard.shared.settings'); }
    public function profile() { return view('dashboard.shared.profile'); }
    public function verification() { return view('dashboard.shared.verification'); }
}
