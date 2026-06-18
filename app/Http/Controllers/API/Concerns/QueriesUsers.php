<?php

namespace App\Http\Controllers\API\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait QueriesUsers
{
    protected function buildUsersQuery(array $validated, ?int $roleId = null): Builder
    {
        $query = User::query()
            ->with(['role.modules', 'identificationType', 'status']);

        if ($roleId !== null) {
            $query->where('role_id', $roleId);
        } elseif (isset($validated['roles']) && is_array($validated['roles'])) {
            $query->whereIn('role_id', $validated['roles']);
        }

        if (! empty($validated['search'])) {
            $terms = preg_split('/\s+/', trim($validated['search']));

            $query->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
                    $q->where(function ($subQuery) use ($term) {
                        $subQuery->where('first_name', 'like', "%{$term}%")
                            ->orWhere('last_name', 'like', "%{$term}%")
                            ->orWhere('identification', 'like', "%{$term}%");
                    });
                }
            });
        }

        return $query
            ->orderBy('first_name')
            ->orderBy('last_name');
    }

    /**
     * @return array{0: LengthAwarePaginator|Collection, 1: array}
     */
    protected function paginateOrGetUsers(Builder $query, array $validated): array
    {
        $meta = [];

        if (isset($validated['per_page']) && ! empty($validated['per_page'])) {
            $perPage = $validated['per_page'] ?? 25;
            $users = $query->paginate($perPage);
            $meta = $this->paginationMeta($users);
        } else {
            $users = $query->get();
        }

        return [$users, $meta];
    }
}
