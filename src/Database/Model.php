<?php

declare(strict_types=1);

namespace Tavp\Core\Database;

use Phalcon\Mvc\Model as PhalconModel;

/**
 * The TAVP base model.
 *
 * Design principle (decision 10.1 — "thin-but-ergonomic"):
 *   - The public API mirrors Laravel/Eloquent so developers feel at home.
 *   - Under the hood it extends Phalcon's native C-extension Model,
 *     so there is zero abstraction overhead on the hot path.
 *
 * Subclasses declare $table, $primaryKey, $fillable and $casts.
 */
abstract class Model extends PhalconModel
{
    /**
     * The table name. Override in subclasses.
     */
    protected string $table = '';

    /**
     * The primary key column. Phalcon uses this for identity.
     */
    protected string $primaryKey = 'id';

    /**
     * Columns that may be mass-assigned via fromArray()/create().
     */
    protected array $fillable = [];

    /**
     * Attribute casting: 'integer', 'boolean', 'datetime', 'json', 'float'.
     */
    protected array $casts = [];

    /**
     * Tell Phalcon which table this model maps to.
     */
    public function getSource(): string
    {
        return $this->table !== '' ? $this->table : parent::getSource();
    }

    /**
     * Mass-assign only the fillable columns.
     */
    public function fill(array $attributes): self
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable, true)) {
                $this->{$key} = $this->castAttribute($key, $value);
            }
        }

        return $this;
    }

    /**
     * Create and persist a model using only fillable columns.
     */
    public static function create(array $attributes): static
    {
        $model = new static();
        $model->fill($attributes);
        $model->save();

        return $model;
    }

    /**
     * Begin a query on this model (delegates to Phalcon's query builder).
     */
    public static function query(): QueryBuilder
    {
        return new QueryBuilder(new static());
    }

    /**
     * Find a model by its primary key.
     */
    public static function findById($id): ?static
    {
        return static::findFirst([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $id],
        ]);
    }

    /**
     * Apply attribute casting when filling or reading.
     */
    protected function castAttribute(string $key, mixed $value): mixed
    {
        $type = $this->casts[$key] ?? null;

        return match ($type) {
            'integer' => (int) $value,
            'float' => (float) $value,
            'boolean' => (bool) $value,
            'json' => is_string($value) ? json_decode($value, true) : $value,
            default => $value,
        };
    }
}
