<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissoesTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_acessa_painel_e_recursos(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole($role);

        $this->assertTrue($admin->hasRole('super_admin'));
        $this->actingAs($admin)->get('/admin')->assertSuccessful();
        $this->actingAs($admin)->get('/admin/concursos')->assertSuccessful();
    }

    public function test_papeis_e_permissoes_existem(): void
    {
        // policies/permissions sao geradas pelo shield (migrations criam as tabelas)
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable('roles'));
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasTable('permissions'));
    }
}
