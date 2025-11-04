<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);
        return view('admins.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', User::class);
        return view('admins.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminRequest $request)
    {
        $this->authorize('create', User::class);

        // Create user sem password
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make(uniqid()), // Password temporária
        ]);

        // Atribuir role admin
        $user->assignRole('admin');

        // Limpar tokens antigos
        DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->delete();

        // Enviar email de reset
        $status = Password::broker('users')->sendResetLink(
            ['email' => $user->email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            return redirect()
                ->route('admins.index')
                ->with('success', 'Administrador criado com sucesso. Um email foi enviado para definir a password.');
        } else {
            return redirect()
                ->route('admins.index')
                ->with('error', 'Administrador criado, mas houve um erro ao enviar o email de redefinição da password.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $admin)
    {
        $this->authorize('view', $admin);
        return view('admins.show', compact('admin'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $admin)
    {
        $this->authorize('delete', $admin);

        // You cannot eliminate yourself.
        if (Auth::id() === $admin->id) {
            return back()
                ->with('error', 'Não pode eliminar o seu próprio utilizador.');
        }

        $admin->delete();

        return redirect()
            ->route('admins.index')
            ->with('success', 'Administrador eliminado com sucesso.');
    }
}
