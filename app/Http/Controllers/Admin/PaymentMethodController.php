<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paymentMethods = PaymentMethod::all();
        return view('admin.payment_methods.index', compact('paymentMethods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.payment_methods.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:payment_methods,name',
            'api_key' => 'nullable|string|max:255',
            'is_enabled' => 'boolean'
        ]);

        PaymentMethod::create([
            'name' => $request->name,
            'api_key' => $request->api_key,
            'is_enabled' => $request->has('is_enabled') ? $request->is_enabled : false,
        ]);

        return redirect()->route('admin.payment_methods.index')->with('success', 'Payment method created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.payment_methods.edit', compact('paymentMethod'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:payment_methods,name,' . $paymentMethod->id,
            'api_key' => 'nullable|string|max:255',
            'is_enabled' => 'boolean'
        ]);

        $paymentMethod->update([
            'name' => $request->name,
            'api_key' => $request->api_key,
            'is_enabled' => $request->has('is_enabled') ? $request->is_enabled : false,
        ]);

        return redirect()->route('admin.payment_methods.index')->with('success', 'Payment method updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();
        return redirect()->route('admin.payment_methods.index')->with('success', 'Payment method deleted successfully.');
    }
}