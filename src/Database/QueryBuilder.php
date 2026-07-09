<?php

declare(strict_types=1);

namespace Tavp\Core\Database;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\ResultsetInterface;

/**
 * A thin, ergonomic query builder that wraps Phalcon's Criteria.
 *
 * It exposes the familiar Eloquent-style chainable methods
 * (where, orderBy, limit, paginate) while delegating all real
 * work to Phalcon — keeping the hot path fast.
 */
class QueryBuilder
{
    private Criteria $criteria;

    public function __construct(private Model $model)
    {
        $this->criteria = $model::query();
    }

    /**
     * Add a where clause. Accepts either:
     *   where('name', 'John')              -> equals
     *   where('age', '>', 18)              -> operator
     *   where(['status' => 'active'])      -> key/value map
     */
    public function where(string|array $column, mixed $operator = null, mixed $value = null): self
    {
        if (is_array($column)) {
            foreach ($column as $key => $val) {
                $this->criteria->andWhere("{$key} = :{$key}:", [$key => $val]);
            }

            return $this;
        }

        if ($operator === null) {
            $this->criteria->andWhere("{$column} = :{$column}:", [$column => $operator]);

            return $this;
        }

        if ($value === null) {
            // Two-argument form: where('name', 'John')
            $this->criteria->andWhere("{$column} = :{$column}:", [$column => $operator]);

            return $this;
        }

        // Three-argument form: where('age', '>', 18)
        $param = $column . '_' . uniqid();
        $this->criteria->andWhere("{$column} {$operator} :{$param}:", [$param => $value]);

        return $this;
    }

    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->criteria->orderBy("{$column} {$direction}");

        return $this;
    }

    public function limit(int $count, int $offset = 0): self
    {
        $this->criteria->limit($count, $offset);

        return $this;
    }

    /**
     * Execute and return all matching models.
     */
    public function get(): ResultsetInterface
    {
        return $this->criteria->execute();
    }

    /**
     * Execute and return the first matching model, or null.
     */
    public function first(): ?Model
    {
        return $this->criteria->execute()->getFirst() ?: null;
    }

    /**
     * Return a paginated result with metadata.
     */
    public function paginate(int $perPage = 15, int $page = 1): array
    {
        $total = $this->model::count($this->criteria->getParams());
        $items = $this->limit($perPage, ($page - 1) * $perPage)->get();

        return [
            'data' => $items,
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => max(1, (int) ceil($total / $perPage)),
        ];
    }
}
