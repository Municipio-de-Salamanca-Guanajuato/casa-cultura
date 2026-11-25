<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function actUser(Request $request)
    {
        // dd($request);
        $request->validate([
            'passwnew' => 'required|min:8',
            'passwnewup' => 'required|min:8|same:passwnew',
        ], [
            'passwnew.required' => 'La nueva contraseña es obligatoria.',
            'passwnew.min' => 'La nueva contraseña debe tener mínimo 8 caracteres.',
            'passwnewup.required' => 'Debes confirmar la nueva contraseña.',
            'passwnewup.min' => 'La confirmación debe tener mínimo 8 caracteres.',
            'passwnewup.same' => 'Las contraseñas no coinciden.',
        ]);

        $user = Auth::user(); // Usuario autenticado
        $user->password = Hash::make($request->passwnew);
        $user->save();

        return redirect()->back()->with('success', 'Contraseña actualizada correctamente.');
    }
}
