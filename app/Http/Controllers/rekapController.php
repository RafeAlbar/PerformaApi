<?php

namespace App\Http\Controllers;

use App\Models\absensici;
use Illuminate\Support\Facades\DB;
use App\Models\absensico;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class rekapController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('rekap.rekapAbsensi');
    }

    public function getData()
    {
        $data = DB::table('absensici')
            ->join(DB::raw('(SELECT npk, tanggal, MIN(waktuci) as waktuci FROM absensici GROUP BY npk, tanggal) as first_checkin'), function ($join) {
                $join->on('absensici.npk', '=', 'first_checkin.npk')
                    ->on('absensici.tanggal', '=', 'first_checkin.tanggal')
                    ->on('absensici.waktuci', '=', 'first_checkin.waktuci');
            })
            ->leftJoin(DB::raw('(SELECT npk, tanggal, MAX(waktuco) as waktuco FROM absensico GROUP BY npk, tanggal) as last_checkout_today'), function ($join) {
                $join->on('absensici.npk', '=', 'last_checkout_today.npk')
                    ->on('absensici.tanggal', '=', 'last_checkout_today.tanggal');
            })
            ->leftJoin(DB::raw('(SELECT npk, tanggal, MAX(waktuco) as waktuco FROM absensico GROUP BY npk, tanggal) as last_checkout_tomorrow'), function ($join) {
                $join->on('absensici.npk', '=', 'last_checkout_tomorrow.npk')
                    ->on(DB::raw('DATE_ADD(absensici.tanggal, INTERVAL 1 DAY)'), '=', 'last_checkout_tomorrow.tanggal')
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('absensici as next_checkin')
                            ->whereRaw('next_checkin.npk = absensici.npk')
                            ->whereRaw('next_checkin.tanggal = DATE_ADD(absensici.tanggal, INTERVAL 1 DAY)')
                            ->whereRaw('next_checkin.waktuci < last_checkout_tomorrow.waktuco');
                    });
            })
            ->select(
                'absensici.nama',
                'absensici.npk',
                'absensici.tanggal',
                'first_checkin.waktuci as waktuci',
                DB::raw('COALESCE(last_checkout_tomorrow.waktuco, last_checkout_today.waktuco) as waktuco')
            )
            ->groupBy('absensici.npk', 'absensici.tanggal', 'absensici.nama', 'first_checkin.waktuci', 'last_checkout_today.waktuco', 'last_checkout_tomorrow.waktuco')
            ->orderBy('absensici.tanggal', 'desc')
            ->get();


        return DataTables::of($data)
            ->addIndexColumn()
            ->make(true);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function storeCheckin(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'npk' => 'required|string|max:50',
            'tanggal' => 'required|date',
            'waktuci' => 'required|date_format:H:i',
        ]);

        absensici::create([
            'nama' => $request->nama,
            'npk' => $request->npk,
            'tanggal' => $request->tanggal,
            'waktuci' => $request->waktuci,
        ]);

        return response()->json(['success' => 'Check-in berhasil ditambahkan!']);
    }

    public function storeCheckout(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'npk' => 'required|string|max:50',
            'tanggal' => 'required|date',
            'waktuco' => 'required|date_format:H:i',
        ]);

        absensico::create([
            'nama' => $request->nama,
            'npk' => $request->npk,
            'tanggal' => $request->tanggal,
            'waktuco' => $request->waktuco,
        ]);

        return response()->json(['success' => 'Check-in berhasil ditambahkan!']);
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
                DB::raw('TIMESTAMPDIFF(MINUTE, "07:00:00", a.waktuci) as keterlambatan_menit'),
                DB::raw('SUM(TIMESTAMPDIFF(MINUTE, "07:00:00", a.waktuci)) as total_keterlambatan')
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
}
