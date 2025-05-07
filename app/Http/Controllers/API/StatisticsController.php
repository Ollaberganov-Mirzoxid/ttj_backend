<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ariza;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function index()
    {
        $rejectedCount = Ariza::where('status', 'rejected')->count();
        $approvedCount = Ariza::where('status', 'approved')->count();
        $pendingCount  = Ariza::where('status', 'pending')->count();
        $totalCount    = $rejectedCount + $approvedCount + $pendingCount;

        $totalStudentCapacity = 208;
        $vacantSeats = $totalStudentCapacity - $approvedCount;
        $reservedSeats = $approvedCount;

        $occupancyRate = ($reservedSeats / $totalStudentCapacity) * 100;
        $occupancyRate = round($occupancyRate, 2);

        // Agar son butun bo‘lsa faqat butun qismini chiqaradi, aks holda ikki kasr raqam bilan
        $formattedRate = (intval($occupancyRate) == $occupancyRate)
            ? intval($occupancyRate)
            : number_format($occupancyRate, 2);

        $arizalar = Ariza::all();

        return response()->json([
            //Arizalar Statistikasi
            'total' => $totalCount,
            'rejected' => $rejectedCount,
            'approved' => $approvedCount,
            'pending' => $pendingCount,

            //Talabalar Sig'imi Statistikasi
            'totalStudentCapacity' => $totalStudentCapacity,
            'vacantSeats' => $vacantSeats,
            'reservedSeats' => $reservedSeats,
            'occupancyRate' => $formattedRate,
            'data' => $arizalar
        ]);
    }
}
