<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $data['setting'] = Setting::first();
        return view('setting.index', $data);
    }

    public function store(Request $request)
    {
        $setting = Setting::first();
        
        if (!empty($request->file('photo'))) {
            // check existing image
            if (isset($setting->image)) {
                // delete existing image
                if (Storage::disk('public')->exists($setting->image)) {
                    Storage::disk('public')->delete($setting->image);
                }
                $request['image'] = $request->file('photo')->store('images/setting', 'public');
            }else{
                $request['image'] = $request->file('photo')->store('images/setting', 'public');
            }
        }else{
            $request['image'] = $setting->image;
        }

        // check if setting exist

        if ($setting) {
            $setting->update($request->all());
        } else {
            Setting::create($request->all());
        }
        
        return redirect()->route('settings.index')->with('success', 'Berhasil menyimpan pengaturan');

    }
}
