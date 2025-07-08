<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;
use App\Services\BeneficiaryService;
use App\Helpers\ResponseHelper;

class BeneficiaryController extends AuthMiddleware
{
    private $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new BeneficiaryService();
    }

    public function list()
    {
        $userId = $_SESSION['user_id'];
        $beneficiaries = $this->service->getBeneficiaries($userId);
        return ResponseHelper::success($beneficiaries);
    }

    public function delete($id)
    {
        $userId = $_SESSION['user_id'];
        $success = $this->service->deleteBeneficiary($id, $userId);
        return $success 
            ? ResponseHelper::success("Beneficiary deleted.")
            : ResponseHelper::error("Could not delete.");
    }
}