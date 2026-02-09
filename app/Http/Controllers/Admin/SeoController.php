<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdsSeo;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    public function index()
    {
        $seos = AdsSeo::latest()->paginate(20);
        return view('admin.seo.index', compact('seos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'path' => 'required|unique:ads_seo,path',
            'meta_title' => 'required',
            'meta_description' => 'required',
        ]);

        AdsSeo::create($request->all());

        return back()->with('success', 'SEO settings saved!');
    }

    public function update(Request $request, AdsSeo $seo)
    {
        $request->validate([
            'meta_title' => 'required',
            'meta_description' => 'required',
        ]);

        $seo->update($request->all());

        return back()->with('success', 'SEO settings updated!');
    }

    public function destroy(AdsSeo $seo)
    {
        $seo->delete();
        return back()->with('success', 'SEO settings removed.');
    }
}
