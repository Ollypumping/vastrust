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

    public function list($userId)
    {
        $beneficiaries = $this->service->getBeneficiaries($userId);
        return ResponseHelper::success($beneficiaries, "Beneficiaries fetched successfully");
    }

    public function delete($id)
    {
        // You can optionally pass userId if your logic checks ownership
        // For now, only the ID is passed
        $success = $this->service->deleteBeneficiary($id);

        return $success 
            ? ResponseHelper::success([], "Beneficiary deleted successfully.")
            : ResponseHelper::error([], "Could not delete beneficiary.");
    }
}