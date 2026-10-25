<?php

namespace App\Shared\Traits;

trait HasCompositePrimaryKey
{
    /**
     * Indica si las claves primarias son autoincrementables.
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    /**
     * Configura las claves para las consultas de guardado/actualización.
     */
    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (! is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getAttribute($keyName));
        }

        return $query;
    }

    /**
     * Búsqueda estática por clave compuesta.
     */
    public static function findComposite(array $ids)
    {
        $instance = new static;
        $query = static::query();
        $keys = $instance->getKeyName();

        if (is_array($keys)) {
            foreach ($keys as $key) {
                if (isset($ids[$key])) {
                    $query->where($key, $ids[$key]);
                }
            }

            return $query->first();
        }

        return static::find($ids);
    }
}
