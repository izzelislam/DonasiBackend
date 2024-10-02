<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Finance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ExperimenController extends Controller
{
    public function index()
    {
        $model  = Finance::paginate(10);
        return DataTables::of($model)->toJson();
    }
}
