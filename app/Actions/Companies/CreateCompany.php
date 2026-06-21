<?php

namespace App\Actions\Companies;

use App\Models\Company;
use App\Traits\Models\FillModelData;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class CreateCompany
{
    use FillModelData;

    public function execute(array $data): array
    {
        try {
            DB::beginTransaction();

            $company = new Company;
            $company->fill($this->fillData(Company::class, $data));

            if (isset($data['logo'])) {
                $company->logo = $this->storeLogo($data['logo']);
            }

            $company->save();

            $this->syncLinks($company, $data['links'] ?? []);
            $this->syncEmails($company, $data['emails'] ?? []);
            $this->syncPhones($company, $data['phones'] ?? []);
            $this->syncServices($company, $data['services'] ?? []);
            $this->syncCoordinates($company, $data['coordinates'] ?? null);

            DB::commit();

            return [
                'success' => true,
                'company' => $company->load($this->relations()),
            ];
        } catch (\Exception $e) {
            Log::channel('CompanyError')->error("Message: {$e->getMessage()}, File: {$e->getFile()}, Line: {$e->getLine()}");
            DB::rollBack();

            throw $e;
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $links
     */
    private function syncLinks(Company $company, array $links): void
    {
        foreach ($links as $link) {
            $company->links()->create([
                'username' => $link['username'],
                'link' => $link['link'],
                'link_type_id' => $link['linkTypeId'],
            ]);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $emails
     */
    private function syncEmails(Company $company, array $emails): void
    {
        foreach ($emails as $email) {
            $company->emails()->create([
                'email' => $email['email'],
            ]);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $phones
     */
    private function syncPhones(Company $company, array $phones): void
    {
        foreach ($phones as $phone) {
            $company->phones()->create([
                'phone' => $phone['phone'],
            ]);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $services
     */
    private function syncServices(Company $company, array $services): void
    {
        foreach ($services as $service) {
            $company->services()->create([
                'description' => $service['description'],
            ]);
        }
    }

    private function storeLogo(UploadedFile $logo): string
    {
        return $logo->store('companies/logos', 'public');
    }

    /**
     * @return array<int, string>
     */
    private function relations(): array
    {
        return ['links.linkType', 'emails', 'phones', 'services', 'coordinates'];
    }

    /**
     * @param  array<string, mixed>|null  $coordinates
     */
    private function syncCoordinates(Company $company, ?array $coordinates): void
    {
        if ($coordinates === null) {
            return;
        }

        $latitude = $coordinates['latitude'] ?? null;
        $longitude = $coordinates['longitude'] ?? null;

        if ($latitude === null || $latitude === '' || $longitude === null || $longitude === '') {
            return;
        }

        $company->coordinates()->updateOrCreate(
            ['company_id' => $company->id],
            [
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]
        );
    }
}
