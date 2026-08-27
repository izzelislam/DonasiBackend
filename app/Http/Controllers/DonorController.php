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
                ->generate($param, public_path('/qr/'.$param.'.svg'));

        return 'qr/'.$param.'.svg';
    }

    public function printQr()
    {
        $donors =  \App\Models\Donor::whereBetween('id', [request('start') - 1, request('end') + 1])->get();
        // $donors = $data->chunk(4);

        return view('donor.export-code', compact('donors'));

        // dd($donors);
        // dd(request('start'), request('end'));
        // $pdf = PDF::loadView('donor.pdf');
        // return $pdf->stream('qr_donatur_'.request('start').'_'.request('end').'.pdf');
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

    public function ajaxSearch(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (strlen($q) < 1) {
            return response()->json([]);
        }

        // Split multi-word query (tokenized search)
        $keywords = array_filter(preg_split('/\s+/', $q));
        if (empty($keywords)) {
            return response()->json([]);
        }

        $query = Donor::with(['province', 'regency', 'district']);

        foreach ($keywords as $keyword) {
            $cleanDigits = preg_replace('/[^0-9]/', '', $keyword);
            $query->where(function($sub) use ($keyword, $cleanDigits) {
                $sub->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('uuid', 'LIKE', "%{$keyword}%")
                    ->orWhere('phone_number', 'LIKE', "%{$keyword}%")
                    ->orWhere('address', 'LIKE', "%{$keyword}%")
                    ->orWhereHas('regency', function($r) use ($keyword) {
                        $r->where('name', 'LIKE', "%{$keyword}%");
                    })
                    ->orWhereHas('district', function($d) use ($keyword) {
                        $d->where('name', 'LIKE', "%{$keyword}%");
                    })
                    ->orWhereHas('province', function($p) use ($keyword) {
                        $p->where('name', 'LIKE', "%{$keyword}%");
                    });

                if (strlen($cleanDigits) >= 3) {
                    $sub->orWhereRaw("REPLACE(REPLACE(REPLACE(phone_number, '-', ''), ' ', ''), '+', '') LIKE ?", ["%{$cleanDigits}%"]);
                }
            });
        }

        $rawResults = $query->limit(30)->get();

        // Calculate relevance scores
        $lowerQ = strtolower($q);
        $scored = $rawResults->map(function($donor) use ($lowerQ, $keywords) {
            $score = 0;
            $lowerName = strtolower($donor->name ?? '');
            $lowerUuid = strtolower($donor->uuid ?? '');
            $lowerPhone = preg_replace('/[^0-9]/', '', $donor->phone_number ?? '');

            // Exact or prefix name match gets highest score
            if ($lowerName === $lowerQ) {
                $score += 100;
            } elseif (str_starts_with($lowerName, $lowerQ)) {
                $score += 60;
            } elseif (str_contains($lowerName, $lowerQ)) {
                $score += 40;
            }

            // Exact or prefix UUID
            if ($lowerUuid === $lowerQ || str_starts_with($lowerUuid, $lowerQ)) {
                $score += 80;
            } elseif (str_contains($lowerUuid, $lowerQ)) {
                $score += 35;
            }

            // Keyword matches in name / uuid
            foreach ($keywords as $kw) {
                $lkw = strtolower($kw);
                if (str_starts_with($lowerName, $lkw)) {
                    $score += 20;
                } elseif (str_contains($lowerName, $lkw)) {
                    $score += 10;
                }
                if (str_contains($lowerUuid, $lkw)) {
                    $score += 10;
                }
            }

            $donor->search_score = $score;
            return $donor;
        })->sortByDesc('search_score')->values()->take(15);

        // Format for frontend response
        $results = $scored->map(function($donor) {
            $locationParts = array_filter([
                $donor->district ? ucwords(strtolower($donor->district->name)) : null,
                $donor->regency ? ucwords(strtolower($donor->regency->name)) : null,
                $donor->province ? ucwords(strtolower($donor->province->name)) : null,
            ]);
            $locationStr = implode(', ', $locationParts);

            return [
                'id'           => $donor->id,
                'uuid'         => $donor->uuid,
                'name'         => $donor->name,
                'phone_number' => $donor->phone_number,
                'address'      => $donor->address,
                'location'     => $locationStr,
                'formatted_label' => $donor->name . ' (' . $donor->uuid . ')' . ($locationStr ? ' - ' . $locationStr : ''),
            ];
        });

        return response()->json($results);
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
