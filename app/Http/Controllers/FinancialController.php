<?php

namespace App\Http\Controllers;

use App\Models\Finance;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['transactions'] = Finance::type()->range()->month()->year()->orderBy('created_at', 'desc')->get();

        // get distinct years
        $data['years'] = Finance::type()->selectRaw('YEAR(created_at) as year')->distinct()->get();

        return view('financial.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['action'] = route('financials.store');
        return view('financial.create', $data);
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
            'title' => 'required',
            'type' => 'required',
            'amount' => 'required',
        ],[
            'title.required' => 'nama transaksi wajib di isi',
            'type.required' => 'jenis transaksi wajib di isi',
            'amount.required' => 'jumlah transaksi wajib di isi',
        ]);

        $request['receipt_uid'] = 'TRX-'.uniqid().date('YmdHis');
        Finance::create($request->all());
        return redirect()->route('financials.index')->with('success','Data berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['transaction'] = Finance::where('receipt_uid', $id)->firstOrFail();
        return view('financial.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['transaction'] = Finance::where('receipt_uid', $id)->firstOrFail();
        $data['action']      = route('financials.update', $id);
        return view('financial.create', $data);
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
            'title' => 'required',
            'type' => 'required',
            'amount' => 'required',
        ],[
            'title.required' => 'nama transaksi wajib di isi',
            'type.required' => 'jenis transaksi wajib di isi',
            'amount.required' => 'jumlah transaksi wajib di isi',
        ]);

        $transaction = Finance::where('receipt_uid', $id)->firstOrFail();
        $transaction->update($request->all());
        return redirect()->route('financials.index')->with('success','Data berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $transaction = Finance::where('receipt_uid', $id)->firstOrFail();
        $transaction->delete();
        return redirect()->route('financials.index')->with('success','Data berhasil dihapus');
    }
}
