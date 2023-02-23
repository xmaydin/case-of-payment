<?php

namespace App\Repository\Eloquent;

use App\Repository\EloquentRepositoryInterface;
use App\Traits\ResponseTrait;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements EloquentRepositoryInterface
{
    use ResponseTrait;

    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(): Model
    {
        return $this->model->all();
    }

    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    public function find($id): ?Model
    {
        return $this->model->first($id);
    }

    public function firstWhere(array $where): ?Model
    {
        return $this->model->where($where)->first();
    }

    public function first(array $attributes): Model
    {
        return $this->model->first($attributes);
    }

    public function firstOrFail(array $attributes): Model
    {
        // TODO: Implement firstOrFail() method.
    }

    public function firstOrCreate(array $attributes): Model
    {
        // TODO: Implement firstOrCreate() method.
    }

    public function save()
    {
        // TODO: Implement save() method.
    }

    public function where(array $attributes): Model
    {
        // TODO: Implement where() method.
    }

    public function relationWhere($relation, $attr)
    {
        return $this->model->with([$relation => function ($query) use ($attr){
            $query->where($attr);
        }])->first();
    }

    public function relationFirstWhere($relation, $attr)
    {
        return $this->model->with([$relation => function ($query) use ($attr){
                return $query->where($attr)->first();
            }])->first();
    }
}
