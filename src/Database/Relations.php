<?php

declare(strict_types=1);

namespace Tavp\Core\Database;

/**
 * Declarative relationship helpers for TAVP models.
 *
 * Mirrors the familiar Eloquent relationship names so developers can
 * read a model and understand its associations at a glance.
 *
 * These are thin wrappers over Phalcon's initialize() relationship
 * methods — no runtime magic, just readable configuration.
 */
trait Relations
{
    /**
     * Define a belongsTo relationship (the child side of a foreign key).
     */
    protected function belongsTo(string $relatedModel, string $foreignKey, string $localKey = 'id'): void
    {
        $this->belongsTo($foreignKey, $relatedModel, $localKey, [
            'alias' => $relatedModel,
        ]);
    }

    /**
     * Define a hasMany relationship (the parent side).
     */
    protected function hasMany(string $relatedModel, string $foreignKey, string $localKey = 'id'): void
    {
        $this->hasMany($localKey, $relatedModel, $foreignKey, [
            'alias' => $relatedModel,
        ]);
    }

    /**
     * Define a hasOne relationship.
     */
    protected function hasOne(string $relatedModel, string $foreignKey, string $localKey = 'id'): void
    {
        $this->hasOne($localKey, $relatedModel, $foreignKey, [
            'alias' => $relatedModel,
        ]);
    }

    /**
     * Define a many-to-many relationship through a pivot table.
     */
    protected function belongsToMany(string $relatedModel, string $pivotTable, string $foreignKey, string $relatedKey): void
    {
        $this->manyToMany(
            $foreignKey,
            $pivotTable,
            $relatedKey,
            $relatedModel,
            [
                'alias' => $relatedModel,
            ]
        );
    }
}
