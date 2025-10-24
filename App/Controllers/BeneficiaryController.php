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

    // GET /api/beneficiaries/{userId}
    public function list($userId)
    {
        $beneficiaries = $this->service->getBeneficiaries($userId);
        return ResponseHelper::success($beneficiaries, "Beneficiaries fetched successfully");
    }

    // POST /api/beneficiaries/{userId}
    public function add($userId, $data)
    {
        $res = $this->service->saveBeneficiary(
            $userId,
            $data['account_number'] ?? '',
            $data['account_name'] ?? '',
            $data['bank'] ?? null
        );

        if (!empty($res['success']) && $res['success'] === false) {
            return ResponseHelper::error([], $res['message'] ?? 'Could not add beneficiary.');
        }

        return ResponseHelper::success([], "Beneficiary added successfully");
    }

    // PUT /api/beneficiaries/{userId}/{beneficiaryId}
    public function update($userId, $beneficiaryId, $data)
    {
        $res = $this->service->updateBeneficiary(
            $userId,
            $beneficiaryId,
            $data['account_name'] ?? '',
            $data['bank'] ?? null
        );

        return $res
            ? ResponseHelper::success([], "Beneficiary updated successfully.")
            : ResponseHelper::error([], "Could not update beneficiary.");
    }

    // DELETE /api/beneficiary/{userId}/{beneficiaryId}
    public function delete($userId, $beneficiaryId)
    {
        $success = $this->service->deleteBeneficiary($beneficiaryId, $userId);

        return $success 
            ? ResponseHelper::success([], "Beneficiary deleted successfully.")
            : ResponseHelper::error([], "Could not delete beneficiary.");
    }
}
