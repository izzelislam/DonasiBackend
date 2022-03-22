<?php

namespace App\Http\Controllers;

use App\Exports\DonorExportort;
use App\Models\District;
use App\Models\Donor;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class DonorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['donors'] = Donor::with('regency')->forRegency()->forDistrict()->forTeam()->forProvince()->orderBy('created_at', 'desc')->get();
        $data['regencies'] = Regency::all();
        $data['provinces'] = Province::all();
        $data['districts'] = District::all();
        $data['teams'] = Team::all();
        return view('donor.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['action']    = route('donors.store');
        $data['teams']     = $this->newArray(Team::active()->get());
        $data['provinces'] = Province::all();
        $data['regencies'] = Regency::all();
        $data['districts'] = District::all();
        return view('donor.create', $data);
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
            'team_id'     => 'required',
            'province_id' => 'required',
            'regency_id'  => 'required',
            'district_id' => 'required',
            'name'        => 'required',
            'phone_number'=> 'required',
            'address'     => 'required',
        ]);
        
        $request['uuid']   = 'DNR-' . uniqid();
        $request['status'] = 'active';
        $request['qr']     = $this->generateQrCode($request['uuid']);

        Donor::create($request->all());

        return redirect()->route('donors.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['model'] = Donor::with(['regency', 'district', 'province', 'team'])->withSum('donations','amount')->where('id', $id)->first();
        return view('donor.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['action']    = route('donors.update', $id);
        $data['model']     = Donor::with(['regency', 'district', 'province', 'team'])->where('id', $id)->first();
        $data['teams']     = $this->newArray(Team::active()->get());
        $data['provinces'] = Province::all();
        $data['regencies'] = Regency::all();
        $data['districts'] = District::all();
        return view('donor.create', $data)->with('success', 'data has been inserted');
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
        $request->validate([
            'team_id'     => 'required',
            'province_id' => 'required',
            'regency_id'  => 'required',
            'district_id' => 'required',
            'name'        => 'required',
            'phone_number'=> 'required',
            'address'     => 'required',
        ]);

        $donor = Donor::find($id);
        $donor->update($request->all());
        return redirect()->route('donors.index')->with('success', 'Data has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $donor = Donor::find($id);

            // check if file exists in public/qr
            if (file_exists(public_path('/' . $donor->qr))) {
                unlink(public_path('/' . $donor->qr));
            }

            // delete data
            $donor->delete();

            // redirect to index
            return redirect()->route('donors.index')->with('success', 'Data has been deleted');
        } catch (Exception $err) {
            return redirect()->route('donors.index')->with('error', 'donatur tidak bisa di hapus, ada data donation yang terkait');
        }
        
    }

    public function newArray($array)
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            $newArray[$value['id']] = $value['name'];
        }
        return $newArray;
    }

    public function generateQrCode($param) 
    {
        QrCode::size(500)
                ->errorCorrection('H')
                ->format('svg')
                ->style('round')
                ->generate($param, public_path('/qr/'.$param.'.png'));

        return 'qr/'.$param.'.png';
    }

    public function printQr()
    {
        $pdf = PDF::loadView('donor.pdf');
        return $pdf->stream('qr_donatur.pdf');
    }

    public function searchPerson(Request $request)
    {
        $request->validate([
            'uuid' => 'exists:donors,uuid',
        ],[
            'uuid.exists' => 'Donatur tidak di temukan',
        ]);

        $data['donor'] = Donor::where('uuid', $request->uuid )->first();
        $data['recipients'] = $this->newArray(User::where('status', 'active')->get());

        return view('donation.create', $data);
    }

    public function exportExcel()
    {
        return (new DonorExportort)->download('data_donatur.xlsx');
    }

    public function updateStatus($id)
    {
        $donor = Donor::find($id);
        $donor->status = $donor->status == 'active' ? 'inactive' : 'active';
        $donor->save();

        return redirect()->route('donors.index')->with('success', 'Status berhasil di update');
    }
    
}
