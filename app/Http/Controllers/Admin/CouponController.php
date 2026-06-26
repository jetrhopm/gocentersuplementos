<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $coupons = Coupon::query()
            ->when($request->filled('q'), fn ($query) => $query->where('code', 'like', '%'.$request->string('q').'%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.form', ['coupon' => new Coupon(['type' => 'percent', 'active' => true])]);
    }

    public function store(CouponRequest $request)
    {
        Coupon::create($request->validated());

        return redirect()->route('admin.coupons.index')->with('status', 'Cupon creado.');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.form', compact('coupon'));
    }

    public function update(CouponRequest $request, Coupon $coupon)
    {
        $coupon->update($request->validated());

        return redirect()->route('admin.coupons.index')->with('status', 'Cupon actualizado.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return back()->with('status', 'Cupon eliminado.');
    }
}
