<?php

namespace App\Repository;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface EloquentRepositoryInterface
{
    /**
     * @return Model
     */
    public function all(): Model;

    /**
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): Model;

    /**
     * @param array $attributes
     * @return Model
     */
    public function where(array $attributes): Model;

    /**
     * @param $id
     * @return Model|null
     */
    public function find($id): ?Model;

    /**
     * @param array $where
     * @param string[] $columns
     * @return mixed
     */
    public function firstWhere(array $where): ?Model;

    /**
     * @param array $attributes
     * @return Model
     */
    public function first(array $attributes): Model;

    /**
     * @param array $attributes
     * @return Model
     */
    public function firstOrFail(array $attributes): Model;

    /**
     * @param array $attributes
     * @return mixed
     */
    public function firstOrCreate(array $attributes): Model;

    /**
     * @return mixed
     */
    public function save();

}
