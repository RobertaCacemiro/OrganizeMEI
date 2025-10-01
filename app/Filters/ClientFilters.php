<?php

namespace App\Filters;

use Illuminate\Http\Request;

class ClientFilters
{
    protected $request;
    protected $builder;
    protected $filters = ['name', 'cpf_cnpj', 'email'];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply($builder)
    {
        $this->builder = $builder;

        foreach ($this->filters as $filter) {
            if (method_exists($this, $filter) && $this->request->filled($filter)) {
                $this->$filter($this->request->input($filter));
            }
        }

        return $this->builder;
    }

    public function name($value)
    {
        $this->builder->where('name', 'like', "%$value%");
    }

    public function cpf_cnpj($value)
    {
        $this->builder->where('cpf_cnpj', 'like', "%$value%");
    }

    public function email($value)
    {
        $this->builder->where('email', 'like', "%$value%");
    }
}
