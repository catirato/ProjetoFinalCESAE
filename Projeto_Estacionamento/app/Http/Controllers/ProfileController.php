<?php

namespace App\Http\Controllers;

use App\Models\Utilizador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth('utilizador')->user();
        $isOwnProfile = true;
        $isAdminView = false;

        return view('perfil.index', compact('user', 'isOwnProfile', 'isAdminView'));
    }

    public function listAll(Request $request)
    {
        $admin = auth('utilizador')->user();
        if ($admin->role !== 'ADMIN') {
            abort(403, 'Apenas administradores podem visualizar todos os perfis.');
        }

        $allowedSorts = ['nome', 'email', 'telemovel', 'role'];
        $sort = $request->get('sort', 'nome');
        $direction = strtolower($request->get('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'nome';
        }

        $utilizadores = Utilizador::query()
            ->orderBy($sort, $direction)
            ->paginate(20)
            ->withQueryString();

        return view('perfil.lista', compact('utilizadores'));
    }

    public function showById($id)
    {
        $admin = auth('utilizador')->user();
        if ($admin->role !== 'ADMIN') {
            abort(403, 'Apenas administradores podem visualizar todos os perfis.');
        }

        $user = Utilizador::findOrFail($id);
        $isOwnProfile = $admin->id === $user->id;
        $isAdminView = true;

        return view('perfil.index', compact('user', 'isOwnProfile', 'isAdminView'));
    }

    public function deleteUser($id)
    {
        $admin = auth('utilizador')->user();
        if ($admin->role !== 'ADMIN') {
            abort(403, 'Apenas administradores podem apagar utilizadores.');
        }

        if ((int) $admin->id === (int) $id) {
            return back()->with('error', 'Não pode apagar o seu próprio utilizador.');
        }

        $user = Utilizador::findOrFail($id);

        try {
            $user->delete(); // Soft delete
        } catch (Throwable $e) {
            return back()->with('error', 'Não foi possível apagar o utilizador.');
        }

        return back()->with('success', 'Utilizador apagado com sucesso.');
    }

    public function update(Request $request)
    {
        $user = auth('utilizador')->user();

        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:utilizador,email,' . $user->id],
            'telemovel' => ['nullable', 'string', 'max:20'],
            'foto_perfil' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('foto_perfil')) {
            if ($user->foto_perfil_path && Storage::disk('public')->exists($user->foto_perfil_path)) {
                Storage::disk('public')->delete($user->foto_perfil_path);
            }

            $validated['foto_perfil_path'] = $request->file('foto_perfil')->store('perfil', 'public');
        }

        $user->update([
            'nome' => $validated['nome'],
            'email' => $validated['email'],
            'telemovel' => $validated['telemovel'] ?? null,
            'foto_perfil_path' => $validated['foto_perfil_path'] ?? $user->foto_perfil_path,
        ]);

        return back()->with('success', 'Perfil atualizado com sucesso.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth('utilizador')->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'A senha atual está incorreta.',
            ]);
        }

        $user->update([
            'password' => $validated['password'],
            'obrigar_mudar_password' => false,
        ]);

        return back()->with('success', 'Senha alterada com sucesso.');
    }
}
