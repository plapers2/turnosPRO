<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\NotificationLog;

class NotificationLogController extends Controller
{
    public function index(Request $request)
    {
        $companyId = session('active_company_id');

        $query = NotificationLog::with('appointment.company')
            ->when($companyId, fn($q) => $q->whereHas(
                'appointment',
                fn($a) => $a->where('company_id', $companyId)
            ))
            ->latest();

        // Filtro por fecha
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        // Filtro por cita
        if ($request->filled('appointment_id')) {
            $query->where('appointment_id', $request->appointment_id);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('notification-logs.index', compact('logs'));
    }
}
