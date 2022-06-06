<?php

namespace App\Services;

use App\Repositories\AdminEloquentRepository;

class AdminService
{
    public $adminRepository;

    public function __construct(AdminEloquentRepository $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function getPaginate(int $perPage = 10)
    {
        return $this->adminRepository->paginate($perPage);
    }
}
