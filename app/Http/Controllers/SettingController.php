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
                $this->deleteFile($setting->image);

                $request['image'] = $this->uploadFile($request->file('photo'));
            }else{
                $request['image'] = $this->uploadFile($request->file('photo'));
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

    public function deleteFile($name)
    {
        if (file_exists($name)){
            unlink($name);
        }
    }

    public function uploadFile($file)
    {
        $newName = uniqid().'.'.$file->getClientOriginalExtension();
        $file->move('images/setting/', $newName);
        return 'images/setting/'.$newName;
    }
}
