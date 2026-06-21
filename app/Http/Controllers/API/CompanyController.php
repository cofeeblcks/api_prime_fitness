<?php

namespace App\Http\Controllers\API;

use App\Actions\Companies\CreateCompany;
use App\Actions\Companies\UpdateCompany;
use App\Constants\ApiStatuses;
use App\Constants\ErrorMessages;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    use ApiResponse;

    private const ENTITY = 'Empresa';

    public function index(): JsonResponse
    {
        $companies = Company::query()
            ->with(['links.linkType', 'emails', 'phones', 'services', 'coordinates'])
            ->orderBy('name')
            ->get();

        return $this->successResponse(
            CompanyResource::collection($companies),
            MessageHelper::make(self::ENTITY, __FUNCTION__, true, false)
        );
    }

    public function store(CompanyRequest $request): JsonResponse
    {
        try {
            $response = (new CreateCompany)->execute($request->all());

            return $this->successResponse(
                new CompanyResource($response['company']),
                MessageHelper::make(self::ENTITY, __FUNCTION__, false, false)
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                ErrorMessages::SERVER_ERROR,
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function show(Company $company): JsonResponse
    {
        return $this->successResponse(
            new CompanyResource($company->load(['links.linkType', 'emails', 'phones', 'services', 'coordinates'])),
            MessageHelper::make(self::ENTITY, __FUNCTION__, false, false)
        );
    }

    public function update(CompanyRequest $request, Company $company): JsonResponse
    {
        try {
            $response = (new UpdateCompany)->execute($company->id, $request->all());

            return $this->successResponse(
                new CompanyResource($response['company']),
                MessageHelper::make(self::ENTITY, __FUNCTION__, false, false)
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                ErrorMessages::SERVER_ERROR,
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e
            );
        }
    }

    public function destroy(Company $company): JsonResponse
    {
        $company->delete();

        return $this->successResponse(
            message: MessageHelper::make(self::ENTITY, __FUNCTION__, false, false)
        );
    }
}
