<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FundriserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['fundrisers'] = User::where('role', 'fundriser')->with('team')->get();

        // dd($data['fundrisers']->toArray());

        return view('fundriser.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['action'] = route('fundrisers.store');
        $data['teams']  = $this->newArray(Team::all());
        return view('fundriser.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'team_id' => 'required',
            'name' => 'required',
            'phone_number' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        if ($request->file('photo')) {
            $image = $request->file('photo');
            $request['image'] = $this->uploadFile($image);
        }
        
        $request['role'] = 'fundriser';
        $request['password'] = bcrypt($request->password);
        $request['status'] = 'active';

        User::create($request->all());
        return redirect()->route('fundrisers.index')->with('success', 'Fundriser created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['action'] = route('fundrisers.update', $id);
        $data['model'] = User::find($id);
        $data['teams']  = $this->newArray(Team::all());
        return view('fundriser.create', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // $request->validate([
        //     'teams_id' => 'required',
        //     'name' => 'required',
        //     'phone_number' => 'required',
        //     'email' => 'required'
        // ],[
        //     'email' => 'email wajib di isi',
        //     'name' => 'Nama wajib di isi',
        //     'phone_number' => 'Nomo telepon wajib di isi',
        //     'teams_id' => 'Tim wajib di isi',

        // ]);

        
        $fundriser = User::find($id);
        
        if ($request->hasFile('photo')) {
            
            $this->deleteFile($fundriser->image);
            
            $image = $request->file('photo');
            $request['image'] = $this->uploadFile($image);
        }
        
        if ($request->password) {
            $request['password'] = bcrypt($request->password);
        } else {
            $request['password'] = $fundriser->password;
        }

        $request['status'] = $fundriser->status;
        $request['role'] = $fundriser->role;

        $fundriser->update($request->all());
        return redirect()->route('fundrisers.index')->with('success', 'Fundriser updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $fundriser = User::find($id);
        $this->deleteFile($fundriser->image);
        $fundriser->delete();
        return redirect()->route('fundrisers.index')->with('success', 'Fundriser deleted successfully');
    }

    public function newArray($array)
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            $newArray[$value['id']] = $value['name'];
        }
        return $newArray;
    }

    public function updateStatus($id)
    {
        $fundriser = User::findOrFail($id);
        $fundriser->status = $fundriser->status == 'active' ? 'inactive' : 'active';
        $fundriser->save();
        return redirect()->route('fundrisers.index')->with('success', 'Status berhasil diupdate');
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
        $file->move('images/profile/', $newName);
        return 'images/profile/'.$newName;
    }
    
}
