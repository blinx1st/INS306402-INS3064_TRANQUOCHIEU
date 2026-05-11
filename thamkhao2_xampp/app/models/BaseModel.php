<?php
class BaseModel
{
    protected Repository $repo;
    protected string $resource;

    public function __construct()
    {
        $this->repo = new Repository();
    }

    public function all(): array
    {
        return $this->repo->all($this->resource);
    }

    public function find(array $keys): ?array
    {
        return $this->repo->find($this->resource, $keys);
    }
}
