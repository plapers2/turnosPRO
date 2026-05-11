<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\TypeCompany;
use App\Models\User;
use App\Mail\AdminCredentialsMail;
use App\Mail\AdminCompanyAssignedMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class MasterController extends Controller
{
    // ─────────────────────────────────────────────
    // EMPRESAS
    // ─────────────────────────────────────────────

    public function index(Request $request): View
    {
        $companies = Company::withTrashed()
            ->with(['typeCompany', 'users' => fn($q) => $q->role('admin')])
            ->latest()
            ->paginate(15);

        return view('master.index', compact('companies'))
            ->with('i', ($request->input('page', 1) - 1) * $companies->perPage());
    }

    public function create(): View
    {
        $typeCompanies = TypeCompany::all();
        $admins        = User::role('admin')->get();

        return view('master.create', compact('typeCompanies', 'admins'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|max:255',
            'address'         => 'required|string|max:255',
            'phone'           => 'required|string|min:7|max:20|regex:/^\+?[\d\s\-\(\)]+$/',
            'type_company_id' => 'required|exists:type_companies,id',
            'logo'            => 'nullable|image|mimes:png,jpg,jpeg|max:10240',
            // Admin: puede asignar uno existente o crear uno nuevo
            'admin_type'      => 'required|in:existing,new',
            'admin_id'        => 'required_if:admin_type,existing|nullable|exists:users,id',
            'admin_name'      => 'required_if:admin_type,new|nullable|string|max:255',
            'admin_email'     => 'required_if:admin_type,new|nullable|email|unique:users,email',
        ], [
            'phone.regex'           => 'El teléfono solo puede contener números, +, -, espacios y paréntesis.',
            'admin_email.unique'    => 'Ya existe un usuario con ese correo.',
            'admin_id.required_if'  => 'Debes seleccionar un administrador.',
            'admin_name.required_if' => 'El nombre del administrador es obligatorio.',
            'admin_email.required_if' => 'El correo del administrador es obligatorio.',
        ]);

        // Logo
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $company = Company::create([
            'name'            => $data['name'],
            'email'           => $data['email'],
            'address'         => $data['address'],
            'phone'           => $data['phone'],
            'type_company_id' => $data['type_company_id'],
            'logo'            => $data['logo'] ?? null,
        ]);

        // Asignar o crear admin
        if ($data['admin_type'] === 'existing') {
            $admin = User::findOrFail($data['admin_id']);
            $company->users()->syncWithoutDetaching([$admin->id]);

            Mail::to($admin->email)->send(new AdminCompanyAssignedMail($admin, $company));
        } else {
            $tempPassword = \Illuminate\Support\Str::random(10);

            $admin = User::create([
                'name'                => $data['admin_name'],
                'email'               => $data['admin_email'],
                'password'            => Hash::make($tempPassword),
                'phone'               => '',
                'must_change_password' => true,
            ]);
            $admin->assignRole('admin');
            $company->users()->attach($admin->id);

            Mail::to($admin->email)->send(new AdminCredentialsMail($admin, $company, $tempPassword));
        }

        return redirect()->route('master.index')
            ->with('success', 'Empresa creada y administrador asignado correctamente.');
    }

    public function edit(Company $company): View
    {
        $company->load(['typeCompany', 'users' => fn($q) => $q->role('admin')]);
        $typeCompanies = TypeCompany::all();
        $admins        = User::role('admin')->get();

        return view('master.edit', compact('company', 'typeCompanies', 'admins'));
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|max:255',
            'address'         => 'required|string|max:255',
            'phone'           => 'required|string|min:7|max:20|regex:/^\+?[\d\s\-\(\)]+$/',
            'type_company_id' => 'required|exists:type_companies,id',
            'logo'            => 'nullable|image|mimes:png,jpg,jpeg|max:10240',
        ], [
            'phone.regex' => 'El teléfono solo puede contener números, +, -, espacios y paréntesis.',
        ]);

        if ($request->hasFile('logo')) {
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        } else {
            unset($data['logo']);
        }

        $company->update($data);

        return redirect()->route('master.index')
            ->with('success', 'Empresa actualizada correctamente.');
    }

    // ─────────────────────────────────────────────
    // ACTIVAR / DESACTIVAR (soft delete / restore)
    // ─────────────────────────────────────────────

    public function destroy(Company $company): RedirectResponse
    {
        $company->delete();

        return redirect()->route('master.index')
            ->with('success', 'Empresa desactivada. Sus datos se conservan.');
    }

    public function restore(int $id): RedirectResponse
    {
        $company = Company::withTrashed()->findOrFail($id);
        $company->restore();

        return redirect()->route('master.index')
            ->with('success', 'Empresa reactivada correctamente.');
    }

    // ─────────────────────────────────────────────
    // ASIGNAR ADMIN EXISTENTE A EMPRESA (TP-MOD0-006)
    // ─────────────────────────────────────────────

    public function assignAdmin(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'admin_id' => 'required|exists:users,id',
        ]);

        $admin = User::findOrFail($data['admin_id']);
        abort_if(!$admin->hasRole('admin'), 403, 'El usuario seleccionado no tiene rol de administrador.');

        $company->users()->syncWithoutDetaching([$admin->id]);

        Mail::to($admin->email)->send(new AdminCompanyAssignedMail($admin, $company));

        return redirect()->route('master.index')
            ->with('success', 'Administrador asignado a la empresa correctamente.');
    }
}
