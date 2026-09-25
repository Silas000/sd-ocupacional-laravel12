<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuditController extends Controller
{
    private const IGNORED_FIELDS = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $audits = Audit::query()
            ->when($request->filled('auditable_type'), fn ($query) => $query->where('auditable_type', $request->input('auditable_type')))
            ->when($request->filled('event'), fn ($query) => $query->where('event', $request->input('event')))
            ->when($request->filled('user_id'), fn ($query) => $query->where('user_id', $request->input('user_id')))
            ->latest('id')
            ->limit(200)
            ->get()
            ->map(fn (Audit $audit) => [
                'registro' => $this->describeRecord($audit),
                'quando' => $audit->created_at?->format('d/m/Y H:i:s'),
                'autor' => $audit->user_name ?? 'Sistema',
                'event' => $audit->event,
                'ip' => $audit->ip_address ?? '-',
                'campos' => $this->diff($audit),
            ]);

        $types = Audit::query()
            ->distinct()
            ->orderBy('auditable_type')
            ->pluck('auditable_type')
            ->mapWithKeys(fn (string $type) => [$type => class_basename($type)]);

        return view('audits.index', [
            'audits' => $audits,
            'types' => $types,
        ]);
    }

    /**
     * Campos alterados, com o valor anterior e o novo.
     *
     * @return array<int, array{campo: string, antes: string, depois: string}>
     */
    private function diff(Audit $audit): array
    {
        $old = $audit->old_values ?? [];
        $new = $audit->new_values ?? [];

        $fields = collect(array_unique(array_merge(array_keys($old), array_keys($new))))
            ->reject(fn (string $field) => in_array($field, self::IGNORED_FIELDS, true))
            ->map(fn (string $field) => [
                'campo' => $field,
                'antes' => $this->stringify($old[$field] ?? null),
                'depois' => $this->stringify($new[$field] ?? null),
            ])
            ->reject(fn (array $linha) => $linha['antes'] === $linha['depois'])
            ->values()
            ->all();

        return $fields;
    }

    private function stringify(mixed $value): string
    {
        if ($value === null) {
            return '—';
        }

        if (is_bool($value)) {
            return $value ? 'sim' : 'não';
        }

        if (is_array($value)) {
            return (string) json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return Str::limit((string) $value, 120);
    }

    private function describeRecord(Audit $audit): string
    {
        if ($audit->auditable_type === null) {
            return 'Registro removido';
        }

        return class_basename($audit->auditable_type).' #'.$audit->auditable_id;
    }
}
