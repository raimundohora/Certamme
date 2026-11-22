<?php

namespace App\Filament\Resources\Audits;

use App\Filament\Resources\Audits\Pages\ListAudits;
use App\Filament\Resources\Audits\Tables\AuditsTable;
use App\Models\Audit;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

/**
 * Trilha de Auditoria (somente leitura). Mostra a trilha imutável gravada pelo
 * owen-it/laravel-auditing (Nota, Resultado, Item de gabarito, Pagamento,
 * Recurso) para defesa jurídica. Não cria, edita nem apaga — a trilha é imutável.
 */
class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string | \UnitEnum | null $navigationGroup = 'Sistema';

    protected static ?int $navigationSort = 90;

    protected static ?string $modelLabel = 'registro de auditoria';

    protected static ?string $pluralModelLabel = 'Trilha de Auditoria';

    protected static ?string $navigationLabel = 'Trilha de Auditoria';

    /** Trilha imutável: a aplicação nunca cria/edita/apaga pela UI. */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    /** Rótulo amigável do tipo de registro auditado. */
    public static function modeloLabel(?string $classe): string
    {
        return match ($classe) {
            'App\\Models\\Nota'         => 'Nota',
            'App\\Models\\Resultado'    => 'Resultado',
            'App\\Models\\GabaritoItem' => 'Item de gabarito',
            'App\\Models\\Pagamento'    => 'Pagamento',
            'App\\Models\\Recurso'      => 'Recurso',
            default                     => class_basename((string) $classe),
        };
    }

    /** Rótulo amigável do evento. */
    public static function eventoLabel(?string $evento): string
    {
        return match ($evento) {
            'created'  => 'Criação',
            'updated'  => 'Alteração',
            'deleted'  => 'Exclusão',
            'restored' => 'Restauração',
            default    => (string) $evento,
        };
    }

    /** Monta a tabela antes → depois (HTML seguro) para o modal de detalhes. */
    public static function diffHtml(Audit $record): HtmlString
    {
        $old = (array) $record->old_values;
        $new = (array) $record->new_values;
        $campos = array_keys($old + $new);

        if (empty($campos)) {
            return new HtmlString('<em>Sem alterações de campos registradas.</em>');
        }

        $linhas = '';
        foreach ($campos as $campo) {
            $antes  = array_key_exists($campo, $old) ? e((string) static::escalar($old[$campo])) : '<span style="color:#9ca3af">—</span>';
            $depois = array_key_exists($campo, $new) ? e((string) static::escalar($new[$campo])) : '<span style="color:#9ca3af">—</span>';
            $linhas .= "<tr>
                <td style=\"padding:4px 10px;border-bottom:1px solid #e5e7eb;font-weight:600\">{$campo}</td>
                <td style=\"padding:4px 10px;border-bottom:1px solid #e5e7eb;color:#b91c1c\">{$antes}</td>
                <td style=\"padding:4px 10px;border-bottom:1px solid #e5e7eb;color:#047857\">{$depois}</td>
            </tr>";
        }

        return new HtmlString(
            '<table style="width:100%;border-collapse:collapse;font-size:13px">
                <thead><tr style="text-align:left">
                    <th style="padding:4px 10px;border-bottom:2px solid #d1d5db">Campo</th>
                    <th style="padding:4px 10px;border-bottom:2px solid #d1d5db">Antes</th>
                    <th style="padding:4px 10px;border-bottom:2px solid #d1d5db">Depois</th>
                </tr></thead>
                <tbody>'.$linhas.'</tbody>
            </table>'
        );
    }

    private static function escalar(mixed $v): string
    {
        if (is_array($v)) {
            return json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        if (is_bool($v)) {
            return $v ? 'true' : 'false';
        }
        return (string) ($v ?? '');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Evento')
                ->columns(3)
                ->schema([
                    TextEntry::make('created_at')
                        ->label('Data/hora')
                        ->dateTime('d/m/Y H:i:s'),
                    TextEntry::make('event')
                        ->label('Ação')
                        ->badge()
                        ->formatStateUsing(fn (?string $state) => static::eventoLabel($state))
                        ->color(fn (?string $state) => match ($state) {
                            'created'  => 'success',
                            'updated'  => 'warning',
                            'deleted'  => 'danger',
                            default    => 'gray',
                        }),
                    TextEntry::make('auditable_type')
                        ->label('Registro')
                        ->formatStateUsing(fn (?string $state, $record) => static::modeloLabel($state).' #'.$record->auditable_id),
                    TextEntry::make('user_id')
                        ->label('Autor')
                        ->placeholder('Sistema / automação')
                        ->formatStateUsing(function ($state) {
                            $nome = optional(\App\Models\User::find($state))->name;
                            return $nome ? "{$nome} (#{$state})" : "Usuário #{$state}";
                        }),
                    TextEntry::make('ip_address')
                        ->label('IP')
                        ->placeholder('—'),
                    TextEntry::make('tags')
                        ->label('Tags')
                        ->placeholder('—'),
                ]),
            Section::make('Alterações')
                ->schema([
                    TextEntry::make('diff')
                        ->hiddenLabel()
                        ->getStateUsing(fn (Audit $record) => static::diffHtml($record))
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return AuditsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAudits::route('/'),
        ];
    }
}
