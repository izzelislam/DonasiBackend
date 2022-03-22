<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Setting;
use App\Models\Team;
use App\Models\User;

class DonationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['donations'] = Donation::with('donor.regency')->month()->year()->team()->range()->startDate()->endDate()->orderBy('created_at', 'desc')->get();
        $data['teams'] = Team::all();
        $data['years'] = Donation::selectRaw('YEAR(created_at) as year')->groupBy('year')->get();

        return view('donation.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('donation.create');
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
            'uuid' => 'required|exists:donors,uuid',
            'amount'   => 'required',
            'recipient_id' => 'required|exists:users,id',
            'type'   => 'required',
        ],[
            'uuid.required' => 'Kode donatur wajib di isi',
            'uuid.exists' => 'Kode donatur tidak ditemukan',
            'amount.required'   => 'Jumlah donasi wajib di isi',
            'recipient_id.required' => 'Name penerima wajib di pilih',
            'recipient_id.exists' => 'Nama penerima tidak ditemukan',
            'type.required'   => 'Jenis donasi wajib di pilih',
        ]);

        $request['receipt_uid'] = 'INV-'.uniqid().date('dmY');
        $request['recipient'] = User::findOrFail($request->recipient_id)->name;
        $request['donor_id']     = Donor::where('uuid', $request->uuid)->first()->id;

        Donation::create($request->all());
        return redirect()->route('donations.index')->with('success', 'Donasi berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['donation'] = Donation::with(['donor.district', 'donor.regency', 'donor.province'])->where('id', $id)->first();
        // dd($data['donation']->toArray());
        return view('donation.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['action'] = route('donations.update', $id);
        $data['donation'] = Donation::with('donor')->where('id', $id)->first();
        $data['recipients'] = $this->newArray(User::where('status', 'active')->get());
        return view('donation.edit', $data);
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
            'uuid' => 'required|exists:donors,uuid',
            'amount'   => 'required',
            'recipient_id' => 'exists:users,id',
            'type'   => 'required',
        ],[
            'uuid.required' => 'Kode donatur wajib di isi',
            'uuid.exists' => 'Kode donatur tidak ditemukan',
            'amount.required'   => 'Jumlah donasi wajib di isi',
            'recipient_id.exists' => 'Nama penerima tidak ditemukan',
            'type.required'   => 'Jenis donasi wajib di pilih',
        ]);

        if ($request->recipient_id) {
            $request['recipient'] = User::findOrFail($request->recipient_id)->name;
        }
        $request['donor_id']     = Donor::where('uuid', $request->uuid)->first()->id;

        Donation::findOrFail($id)->update($request->all());
        return redirect()->route('donations.index')->with('success', 'Donasi berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $donation = Donation::findOrFail($id);
        $donation->delete();
        return redirect()->route('donations.index')->with('success', 'Donasi berhasil dihapus');
    }

    public function receipt($id)
    {
        $data['donation'] = Donation::with('donor')->where('receipt_uid', $id)->first();
        $data['setting'] = Setting::first();
        return view('pages.receipt', $data);
    }

    public function printReceipt()
    {
        $id = request()->uid;
        $data = Donation::where('receipt_uid', $id)->firstOrFail();

        $pdf = PDF::setPaper('a4', 'landscape')->loadView('donation.pdf');
        return $pdf->stream($data->receipt_uid.'.pdf');
    }

    public function newArray($array)
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            $newArray[$value['id']] = $value['name'];
        }
        return $newArray;
    }

}
