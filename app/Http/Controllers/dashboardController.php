<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class dashboardController extends Controller
{
    public function index()
    {
        $data = DB::table('absensici as a')
            ->join(DB::raw('(SELECT npk, tanggal, MIN(waktuci) as waktuci FROM absensici WHERE waktuci > "07:00:00" GROUP BY npk, tanggal) as first_checkin'), function ($join) {
                $join->on('a.npk', '=', 'first_checkin.npk')
                    ->on('a.tanggal', '=', 'first_checkin.tanggal')
                    ->on('a.waktuci', '=', 'first_checkin.waktuci');
            })
            ->select(
                DB::raw('DATE_FORMAT(first_checkin.tanggal, "%b") as month'),
                DB::raw('COUNT(DISTINCT first_checkin.npk) as total_keterlambatan')
            )
            ->groupBy(
                DB::raw('DATE_FORMAT(first_checkin.tanggal, "%b")'),
                DB::raw('DATE_FORMAT(first_checkin.tanggal, "%m")')
            )
            ->orderBy(DB::raw('DATE_FORMAT(first_checkin.tanggal, "%m")'))
            ->get();


        $labels = $data->pluck('month');
        $totals = $data->pluck('total_keterlambatan');

        //         SELECT 
        //     DATE_FORMAT(first_checkin.tanggal, '%b') AS month,
        //     COUNT(DISTINCT first_checkin.npk) AS total_keterlambatan
        // FROM 
        //     absensici AS a
        // JOIN 
        //     (
        //         SELECT 
        //             npk, 
        //             tanggal, 
        //             MIN(waktuci) AS waktuci
        //         FROM 
        //             absensici
        //         WHERE 
        //             waktuci > '07:00:00'
        //         GROUP BY 
        //             npk, 
        //             tanggal
        //     ) AS first_checkin
        //     ON a.npk = first_checkin.npk
        //     AND a.tanggal = first_checkin.tanggal
        //     AND a.waktuci = first_checkin.waktuci
        // GROUP BY 
        //     DATE_FORMAT(first_checkin.tanggal, '%b'),
        //     DATE_FORMAT(first_checkin.tanggal, '%m')
        // ORDER BY 
        //     DATE_FORMAT(first_checkin.tanggal, '%m');


        return view('dashboard.dashboard', compact('labels', 'totals'));
    }


    public function getTable1Data(Request $request)
    {
        $bulan = $request->query('bulan');

        Log::info('Received bulan parameter:', ['bulan' => $bulan]);

        $query = DB::table('absensici as a')
            ->join(DB::raw('(SELECT npk, tanggal, MIN(waktuci) as waktuci FROM absensici WHERE waktuci > "07:00:00" GROUP BY npk, tanggal) as first_checkin'), function ($join) {
                $join->on('a.npk', '=', 'first_checkin.npk')
                    ->on('a.tanggal', '=', 'first_checkin.tanggal')
                    ->on('a.waktuci', '=', 'first_checkin.waktuci');
            })
            ->select(
                'a.nama',
                'a.npk',
                DB::raw('DATE_FORMAT(a.tanggal, "%Y-%m") as bulan'),
                DB::raw('COUNT(*) as total_keterlambatan')
            );

        if ($bulan) {
            $query->where(DB::raw('DATE_FORMAT(a.tanggal, "%Y-%m")'), $bulan);
        }

        $data = $query->groupBy('a.nama', 'a.npk', DB::raw('DATE_FORMAT(a.tanggal, "%Y-%m")'))
            ->orderByDesc('total_keterlambatan')
            ->get();

        Log::info('Query results:', ['data' => $data]);

        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }

    // public function getTable2data(Request $request)
    // {
    //     $bulan = $request->query('bulan');

    //     Log::info('Received bulan parameter:', ['bulan' => $bulan]);

    //     $query = DB::table('absensici as a')
    //         ->join(DB::raw('(SELECT npk, tanggal, MIN(waktuci) as waktuci 
    //                      FROM absensici 
    //                      WHERE waktuci > "07:00:00" 
    //                      GROUP BY npk, tanggal) as first_checkin'), function ($join) {
    //             $join->on('a.npk', '=', 'first_checkin.npk')
    //                 ->on('a.tanggal', '=', 'first_checkin.tanggal')
    //                 ->on('a.waktuci', '=', 'first_checkin.waktuci');
    //         })
    //         ->select(
    //             'a.nama',
    //             'a.npk',
    //             DB::raw('DATE_FORMAT(a.tanggal, "%Y-%m") as bulan'),
    //             DB::raw('TIMESTAMPDIFF(MINUTE, "07:00:00", a.waktuci) as total_keterlambatan')
    //         )
    //         ->orderBy(DB::raw('TIMESTAMPDIFF(MINUTE, "07:00:00", a.waktuci)'), 'desc');

    //     if ($bulan) {
    //         $query->where(DB::raw('DATE_FORMAT(a.tanggal, "%Y-%m")'), $bulan);
    //     }

    //     $data = $query->get();

    //     Log::info('Query results:', ['data' => $data]);

    //     return DataTables::of($data)
    //         ->addIndexColumn()
    //         ->make(true);
    // }
}
