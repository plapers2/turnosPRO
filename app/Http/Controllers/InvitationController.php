<?php

namespace App\Http\Controllers;

use App\Models\CompanyInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    /** Lista de invitaciones de la empresa activa */
    public function index()
    {
        $companyId = session('active_company_id');
        abort_if(!$companyId, 403);

        $invitations = CompanyInvitation::withTrashed()
            ->where('company_id', $companyId)
            ->latest()
            ->paginate(20);

        return view('invitations.index', compact('invitations'));
    }

    /** Generar nueva invitación */
    public function store(Request $request)
    {
        $companyId = session('active_company_id');
        abort_if(!$companyId, 403);

        $request->validate([
            'email' => 'nullable|email',
        ]);

        $invitation = CompanyInvitation::create([
            'company_id' => $companyId,
            'invited_by' => auth()->id(),
            'token'      => Str::random(48),
            'email'      => $request->email,
            'status'     => 'sent',
            'expires_at' => now()->addDays(7),
        ]);

        // Opcional: enviar email si se proporcionó uno
        // Mail::to($invitation->email)->send(new InvitationMail($invitation));

        $link = route('register.invite', $invitation->token);

        return back()->with([
            'success'    => 'Invitación generada.',
            'invite_link' => $link,
        ]);
    }

    /** Revocar (soft delete) */
    public function destroy(CompanyInvitation $invitation)
    {
        abort_if($invitation->status === 'registered', 422, 'No se puede revocar una invitación ya utilizada.');

        $invitation->delete();

        return back()->with('success', 'Invitación revocada.');
    }

    /** Restaurar invitación revocada */
    public function restore(int $id)
    {
        $invitation = CompanyInvitation::withTrashed()->findOrFail($id);
        $invitation->restore();

        return back()->with('success', 'Invitación restaurada.');
    }
    public function accept(string $token)
    {
        $invitation = CompanyInvitation::where('token', $token)
            ->whereNull('deleted_at')
            ->first();

        abort_if(!$invitation || !$invitation->isUsable(), 410, 'Este enlace no es válido o ha expirado.');

        $user = auth()->user();

        // Evitar duplicados
        if (!$user->companies()->where('company_id', $invitation->company_id)->exists()) {
            $user->companies()->attach($invitation->company_id);
        }

        $invitation->update(['status' => 'registered']);

        return redirect()->route('appointment.index')
            ->with('success', 'Empresa vinculada a tu cuenta correctamente.');
    }
}
