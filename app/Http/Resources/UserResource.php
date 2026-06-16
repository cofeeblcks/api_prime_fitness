<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'identification' => $this->identification,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'birthDate' => $this->birth_date,
            'quantity' => count($this->users),
            'parents' => UserParentResource::collection($this->allParents()),
            'electoralStatus' => new ElectoralStatusResource($this->whenLoaded('electoralStatus')),
            'neighborhood' => new NeighborhoodResource($this->whenLoaded('neighborhood')),
            'identificationType' => new IdentificationTypeResource($this->whenLoaded('identificationType')),
            'role' => new RoleResource($this->whenLoaded('role')),
            'pollingStation' => new PollingStationResource($this->whenLoaded('table')),
            'observations' => ObservationResource::collection($this->whenLoaded('observations')),
            'hasNationalRegistry' => $this->has_national_registry,
            'callsReceived' => $this->calls_received_count,
            'statusCall' => $this->last_call_received ? $this->last_call_received->status->name : 'Pendiente',
            'usersCount' => $this->users_count,
            'surveyResponses' => BasicDataSurveyResponseResource::collection(
                $this->latestSurveyResponsesByElection()->load(['candidate', 'electionType'])
            ),
            'hasElectoralCertificate' => $this->electoral_certificates_count > 0,
            'electoralCertificatesCount' => $this->electoral_certificates_count,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}