<?php
namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Candidato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin() { return view('portal.auth.login'); }
    public function showRegister() { return view('portal.auth.register'); }

    public function login(Request $request)
    {
        $data = $request->validate(['email' => 'required|email', 'password' => 'required']);
        if (Auth::guard('candidato')->attempt($data, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('portal.painel'));
        }
        return back()->withErrors(['email' => 'Credenciais inválidas.'])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);
        $data['cpf'] = preg_replace('/\D/', '', $data['cpf']);
        $cand = Candidato::create($data);
        Auth::guard('candidato')->login($cand);
        return redirect()->route('portal.perfil')->with('ok', 'Conta criada! Complete seu cadastro.');
    }

    public function showPerfil()
    {
        return view('portal.auth.perfil', ['candidato' => Auth::guard('candidato')->user()]);
    }

    public function savePerfil(Request $request)
    {
        $data = $request->validate([
            'data_nascimento' => 'required|date',
            'telefone' => 'nullable|string|max:20',
            'sexo' => 'required|string',
            'nome_social' => 'nullable|string|max:255',
            'nome_mae' => 'required|string|max:255',
            'rg' => 'nullable|string|max:20',
            'cep' => 'required|string|max:9',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'required|string|max:255',
            'uf' => 'required|string|max:2',
            'pcd' => 'nullable|boolean',
            'tipo_deficiencia' => 'nullable|string|max:255',
        ]);
        $data['pcd'] = $request->boolean('pcd');
        Auth::guard('candidato')->user()->update($data);
        return redirect()->route('portal.painel')->with('ok', 'Cadastro completo!');
    }

    public function logout(Request $request)
    {
        Auth::guard('candidato')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('portal.home');
    }
}
