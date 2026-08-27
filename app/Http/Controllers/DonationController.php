<?php

namespace App\Http\Controllers;

use App\Enums\BankAccount;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Setting;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;

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
        $data['recipients'] = $this->newArray(User::where('status', 'active')->get());
        $data['bankAccounts'] = BankAccount::cases();
        return view('donation.create', $data);
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
            'uuid'                   => 'required|exists:donors,uuid',
            'amount'                 => 'required',
            'recipient_id'           => 'required|exists:users,id',
            'type'                   => 'required',
            'created_at'             => 'nullable|date',
            'bank_account'           => 'nullable|string',
            'custom_bank_name'       => 'nullable|string|max:100',
            'custom_account_number'  => 'nullable|string|max:100',
            'custom_account_name'    => 'nullable|string|max:100',
            'proof_image'            => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
        ],[
            'uuid.required'         => 'Kode donatur wajib di isi',
            'uuid.exists'           => 'Kode donatur tidak ditemukan',
            'amount.required'       => 'Jumlah donasi wajib di isi',
            'recipient_id.required' => 'Nama penerima wajib di pilih',
            'recipient_id.exists'   => 'Nama penerima tidak ditemukan',
            'type.required'         => 'Tujuan / jenis donasi wajib di pilih',
            'proof_image.image'     => 'Bukti transfer harus berupa file gambar',
            'proof_image.max'       => 'Ukuran bukti transfer maksimal 5MB',
        ]);

        $payload = $request->all();
        $payload['receipt_uid'] = 'INV-'.uniqid().date('dmY');
        $payload['recipient']   = User::findOrFail($request->recipient_id)->name;
        $payload['donor_id']    = Donor::where('uuid', $request->uuid)->first()->id;

        // Process Custom Transaction Date
        if ($request->filled('created_at')) {
            $payload['created_at'] = Carbon::parse($request->created_at)->format('Y-m-d H:i:s');
        } else {
            $payload['created_at'] = now();
        }

        // Process Bank Account Selection
        if ($request->filled('bank_account')) {
            $enum = BankAccount::tryFrom($request->bank_account);
            if ($enum && $enum !== BankAccount::LAINNYA) {
                $payload['bank_account']   = $enum->value;
                $payload['bank_name']      = $enum->bankName();
                $payload['account_number'] = $enum->accountNumber();
                $payload['account_name']   = $enum->accountName();
            } elseif ($request->bank_account === BankAccount::LAINNYA->value) {
                $payload['bank_account']   = BankAccount::LAINNYA->value;
                $payload['bank_name']      = $request->custom_bank_name ?: 'Bank BSI';
                $payload['account_number'] = $request->custom_account_number ?: '-';
                $payload['account_name']   = $request->custom_account_name ?: '-';
            }
        }

        if ($request->hasFile('proof_image')) {
            $payload['proof_image'] = $this->uploadProof($request->file('proof_image'));
        }

        Donation::create($payload);
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
        $data['donation'] = Donation::with(['donor.district', 'donor.regency', 'donor.province'])->where('id', $id)->firstOrFail();
        $data['setting'] = Setting::first();
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
        $data['donation'] = Donation::with('donor')->where('id', $id)->firstOrFail();
        $data['recipients'] = $this->newArray(User::where('status', 'active')->get());
        $data['bankAccounts'] = BankAccount::cases();
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
        $donation = Donation::findOrFail($id);

        $request->validate([
            'uuid'                   => 'required|exists:donors,uuid',
            'amount'                 => 'required',
            'recipient_id'           => 'nullable|exists:users,id',
            'type'                   => 'required',
            'created_at'             => 'nullable|date',
            'bank_account'           => 'nullable|string',
            'custom_bank_name'       => 'nullable|string|max:100',
            'custom_account_number'  => 'nullable|string|max:100',
            'custom_account_name'    => 'nullable|string|max:100',
            'proof_image'            => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:5120',
        ],[
            'uuid.required'       => 'Kode donatur wajib di isi',
            'uuid.exists'         => 'Kode donatur tidak ditemukan',
            'amount.required'     => 'Jumlah donasi wajib di isi',
            'recipient_id.exists' => 'Nama penerima tidak ditemukan',
            'type.required'       => 'Tujuan / jenis donasi wajib di pilih',
            'proof_image.image'   => 'Bukti transfer harus berupa file gambar',
            'proof_image.max'     => 'Ukuran bukti transfer maksimal 5MB',
        ]);

        $payload = $request->all();

        if ($request->filled('recipient_id')) {
            $payload['recipient'] = User::findOrFail($request->recipient_id)->name;
        }
        $payload['donor_id'] = Donor::where('uuid', $request->uuid)->first()->id;

        // Process Custom Transaction Date
        if ($request->filled('created_at')) {
            $payload['created_at'] = Carbon::parse($request->created_at)->format('Y-m-d H:i:s');
        }

        // Process Bank Account Selection
        if ($request->filled('bank_account')) {
            $enum = BankAccount::tryFrom($request->bank_account);
            if ($enum && $enum !== BankAccount::LAINNYA) {
                $payload['bank_account']   = $enum->value;
                $payload['bank_name']      = $enum->bankName();
                $payload['account_number'] = $enum->accountNumber();
                $payload['account_name']   = $enum->accountName();
            } elseif ($request->bank_account === BankAccount::LAINNYA->value) {
                $payload['bank_account']   = BankAccount::LAINNYA->value;
                $payload['bank_name']      = $request->custom_bank_name ?: 'Bank BSI';
                $payload['account_number'] = $request->custom_account_number ?: '-';
                $payload['account_name']   = $request->custom_account_name ?: '-';
            }
        }

        if ($request->hasFile('proof_image')) {
            $this->deleteProof($donation->proof_image);
            $payload['proof_image'] = $this->uploadProof($request->file('proof_image'));
        }

        $donation->update($payload);
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
        $this->deleteProof($donation->proof_image);
        $donation->delete();
        return redirect()->route('donations.index')->with('success', 'Donasi berhasil dihapus');
    }

    public function receipt($uid)
    {
        $data['donation'] = Donation::with(['donor.district', 'donor.regency', 'donor.province'])->where('receipt_uid', $uid)->firstOrFail();
        $data['setting'] = Setting::first();
        return view('pages.receipt', $data);
    }

    public function printReceipt($uid)
    {
        $donation = Donation::with(['donor.district', 'donor.regency', 'donor.province'])->where('receipt_uid', $uid)->firstOrFail();
        $setting = Setting::first();
        
        $pdf = Pdf::loadView('donation.pdf', compact('donation', 'setting'))
                  ->setPaper([0, 0, 425, 900], 'portrait');
        
        return $pdf->stream("Tanda-Terima-{$donation->receipt_uid}.pdf");
    }

    public function newArray($data)
    {
        $arr = [];
        foreach ($data as $item) {
            $arr[$item->id] = $item->name;
        }
        return $arr;
    }

    public function uploadProof($file)
    {
        $newName = 'proof_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $destinationPath = public_path('images/proofs');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        $file->move($destinationPath, $newName);
        return 'images/proofs/' . $newName;
    }

    public function deleteProof($path)
    {
        if ($path && file_exists(public_path($path))) {
            @unlink(public_path($path));
        }
    }
}
