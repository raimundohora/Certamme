<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use OwenIt\Auditing\Models\Audit as BaseAudit;

/**
 * Audit do Certamme: estende o model do owen-it/laravel-auditing e aplica o
 * isolamento multi-tenant (BelongsToTenant) — cada tenant só enxerga a própria
 * trilha. A trilha continua imutável (sem edição/remoção pela aplicação); esta
 * classe só acrescenta o escopo de tenant e o preenchimento automático de
 * tenant_id na criação do registro de auditoria.
 */
class Audit extends BaseAudit
{
    use BelongsToTenant;
}
