<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $report = Report::create($request->all());
        return response()->json($report);
    }

    public function validar($id)
    {
        $report = Report::find($id);
        $report->estado = 'VALIDADO';
        $report->save();

        return response()->json($report);
    }
}
