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
     * Enable automatic created_at/updated_at timestamps.
     */
    protected bool $timestamps = true;

    /**
     * Enable soft deletes (adds deleted_at column).
     */
    protected bool $softDeletes = false;

    /**
     * Reusable query scopes.
     * @var array<string, callable>
     */
    protected array $scopes = [];

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
     * Fill mass-assignable attributes.
     */
    public function fill(array $attributes): static
    {
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable, true)) {
                $this->$key = $value;
            }
        }

        return $this;
    }

    /**
     * Begin a query on this model (delegates to Phalcon's query builder).
     */
    public function query(): QueryBuilder
    {
        return new QueryBuilder(new static());
    }

    /**
     * Find a model by its primary key.
     */
    public function findById($id): ?static
    {
        return static::findFirst([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $id],
        ]);
    }

    /**
     * Find first record matching conditions.
     */
    public function first(): ?static
    {
        return static::findFirst([
            'order' => static::getPrimaryKey() . ' DESC',
        ]);
    }

    /**
     * Get the primary key name.
     */
    public function getPrimaryKey(): string
    {
        $model = new static();

        return $model->primaryKey;
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

    /**
     * Auto-set timestamps on create/update.
     */
    public function beforeCreate(): void
    {
        if ($this->timestamps) {
            $now = date('Y-m-d H:i:s');
            $this->created_at = $now;
            $this->updated_at = $now;
        }
    }

    public function beforeUpdate(): void
    {
        if ($this->timestamps) {
            $this->updated_at = date('Y-m-d H:i:s');
        }
    }

    /**
     * Soft delete: set deleted_at instead of removing the row.
     */
    public function delete(): bool
    {
        if ($this->softDeletes) {
            $this->deleted_at = date('Y-m-d H:i:s');

            return $this->update();
        }

        return parent::delete();
    }

    /**
     * Scope: exclude soft-deleted records.
     */
    public function scopeWithoutSoftDeletes(QueryBuilder $query): QueryBuilder
    {
        if ($this->softDeletes) {
            $query->where('deleted_at', '=', null);
        }

        return $query;
    }

    /**
     * Scope: include only soft-deleted records.
     */
    public function scopeOnlySoftDeletes(QueryBuilder $query): QueryBuilder
    {
        if ($this->softDeletes) {
            $query->where('deleted_at', '!=', null);
        }

        return $query;
    }

    /**
     * Restore a soft-deleted record.
     */
    public function restore(): bool
    {
        if ($this->softDeletes) {
            $this->deleted_at = null;

            return $this->update();
        }

        return true;
    }

    /**
     * Check if model is soft-deleted.
     */
    public function trashed(): bool
    {
        return $this->softDeletes && $this->deleted_at !== null;
    }
}
