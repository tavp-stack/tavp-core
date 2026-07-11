<?php

declare(strict_types=1);

namespace Tavp\Core\Database;

/**
 * Declarative relationship helpers for TAVP models.
 *
 * Thin wrappers over Phalcon's relationship methods.
 * Define relationships in initialize() using Phalcon's native API.
 */
trait Relations
{
    /**
     * Define a belongsTo relationship.
     */
    protected function defineBelongsTo(string $relatedModel, string $foreignKey, string $localKey = 'id'): void
    {
        $this->belongsTo($foreignKey, [
            'reusable' => true,
            'alias' => $relatedModel,
            'foreignKey' => [
                'fields' => [$foreignKey],
                'referencedFields' => [$localKey],
            ],
        ]);
    }

    /**
     * Define a hasMany relationship.
     */
    protected function defineHasMany(string $relatedModel, string $foreignKey, string $localKey = 'id'): void
    {
        $this->hasMany($localKey, [
            'reusable' => true,
            'alias' => $relatedModel,
            'foreignKey' => [
                'fields' => [$foreignKey],
                'referencedFields' => ['id'],
            ],
        ]);
    }

    /**
     * Define a hasOne relationship.
     */
    protected function defineHasOne(string $relatedModel, string $foreignKey, string $localKey = 'id'): void
    {
        $this->hasOne($localKey, [
            'reusable' => true,
            'alias' => $relatedModel,
            'foreignKey' => [
                'fields' => [$foreignKey],
                'referencedFields' => ['id'],
            ],
        ]);
    }
}
