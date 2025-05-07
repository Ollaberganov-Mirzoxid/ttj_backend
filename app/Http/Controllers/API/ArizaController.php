<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ariza;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArizaController extends Controller
{
    protected $telegramService;

    // TelegramService'ni controllerga inject qilish
    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    // Ariza yaratish
    public function store(Request $request)
    {
        $request->validate([
            'jshshir' => 'required|string',
            'passport' => 'required|string',
            'passport_date' => 'required|date',
            'region' => 'required|string',
            'district' => 'required|string',
            'address' => 'required|string',
            'university' => 'required|string',
            'has_sibling' => 'required|boolean',
            'sibling_relation' => 'nullable|string',
            'sibling_jshshir' => 'nullable|string',
            'privilege' => 'nullable|string',
            'phone' => 'required|string',
            'email' => 'required|email',
        ]);

        $ariza = Ariza::create([
            'user_id' => Auth::id(),
            'jshshir' => $request->jshshir,
            'passport' => $request->passport,
            'passport_date' => $request->passport_date,
            'region' => $request->region,
            'district' => $request->district,
            'address' => $request->address,
            'university' => $request->university,
            'has_sibling' => $request->has_sibling,
            'sibling_relation' => $request->sibling_relation,
            'sibling_jshshir' => $request->sibling_jshshir,
            'privilege' => $request->privilege,
            'phone' => $request->phone,
            'email' => $request->email,
        ]);

        // Telegramga xabar yuborish
        $message = "Yangi ariza kelib tushdi!\n" .
            "Talaba F.I.O: " . auth()->user()->name . "\n" .
            "Holat: Jarayonda";  // xabar matni

        // Mas'ul shaxs va Super Admin'ga xabar yuborish
        $this->telegramService->sendMessage(env('TELEGRAM_RESPONSIBLE_CHAT_ID'), $message);
        $this->telegramService->sendMessage(env('TELEGRAM_SUPER_ADMIN_CHAT_ID'), $message);

        return response()->json(['message' => 'Ariza yaratildi', 'ariza' => $ariza]);
    }

    // Arizalarni ko‘rish (faqat super admin va masullar)
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('super_admin') || $user->hasRole('masul')) {
            $arizalar = Ariza::all();
            $arizalar = Ariza::with('user')->orderBy('created_at', 'desc')->paginate(10); // 'user' bilan birga olish
            return response()->json($arizalar);
        } else if ($user->hasRole('talaba')) {
            // Hozirgi foydalanuvchiga tegishli arizalarni olish
            $applications = Ariza::where('user_id', auth()->id())->orderBy('created_at', 'desc')->paginate(10);
            return response()->json($applications);
        }

        return response()->json(['message' => 'Ruxsat yo‘q'], 403);
    }

    // Arizani tasdiqlash
    public function approve(Request $request, $id)
    {
        $ariza = Ariza::findOrFail($id);

        if ($ariza->status !== 'pending') {
            return response()->json(['message' => 'Ariza allaqachon tasdiqlangan yoki rad etilgan'], 400);
        }

        $ariza->status = 'approved';
        $ariza->save();

        return response()->json(['message' => 'Ariza tasdiqlandi', 'ariza' => $ariza]);
    }

    // Arizani rad etish
    public function reject(Request $request, $id)
    {
        $ariza = Ariza::findOrFail($id);

        if ($ariza->status !== 'pending') {
            return response()->json(['message' => 'Ariza allaqachon tasdiqlangan yoki rad etilgan'], 400);
        }

        $ariza->status = 'rejected';
        $ariza->reason = $request->reason;
        $ariza->save();

        return response()->json(['message' => 'Ariza rad etildi', 'ariza' => $ariza]);
    }

    public function myApplications(Request $request)
    {
        // Foydalanuvchining o‘z arizalarini olish
        $user = $request->user();
        // Arizalarni yangi qo'shilganidan boshida ko'rsatilishi uchun
        $arizalar = $user->arizalar()->orderBy('created_at', 'desc')->get();

        return response()->json($arizalar);
    }
}
