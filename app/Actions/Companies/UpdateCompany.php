<?php

namespace App\Actions\Companies;

use App\Models\Company;
use App\Models\CompanyEmail;
use App\Models\CompanyLink;
use App\Models\CompanyPhone;
use App\Models\CompanyService;
use App\Traits\Models\FillModelData;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class UpdateCompany
{
    use FillModelData;

    public function execute(int $companyId, array $data): array
    {
        try {
            DB::beginTransaction();

            $company = Company::findOrFail($companyId);
            $company->fill($this->fillData(Company::class, $data));

            if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
                $this->deleteLogo($company->getRawOriginal('logo'));
                $company->logo = $this->storeLogo($data['logo']);
            }

            $company->save();

            if (array_key_exists('links', $data)) {
                $this->syncLinks($company, $data['links'] ?? []);
            }

            if (array_key_exists('emails', $data)) {
                $this->syncEmails($company, $data['emails'] ?? []);
            }

            if (array_key_exists('phones', $data)) {
                $this->syncPhones($company, $data['phones'] ?? []);
            }

            if (array_key_exists('services', $data)) {
                $this->syncServices($company, $data['services'] ?? []);
            }

            if (array_key_exists('coordinates', $data)) {
                $this->syncCoordinates($company, $data['coordinates']);
            }

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
        $keptIds = [];

        foreach ($links as $link) {
            if (! empty($link['id'])) {
                $companyLink = CompanyLink::query()
                    ->where('company_id', $company->id)
                    ->findOrFail($link['id']);

                $companyLink->update([
                    'username' => $link['username'],
                    'link' => $link['link'],
                    'link_type_id' => $link['linkTypeId'],
                ]);

                $keptIds[] = $companyLink->id;
            } else {
                $created = $company->links()->create([
                    'username' => $link['username'],
                    'link' => $link['link'],
                    'link_type_id' => $link['linkTypeId'],
                ]);

                $keptIds[] = $created->id;
            }
        }

        $company->links()->whereNotIn('id', $keptIds)->delete();
    }

    /**
     * @param  array<int, array<string, mixed>>  $emails
     */
    private function syncEmails(Company $company, array $emails): void
    {
        $keptIds = [];

        foreach ($emails as $email) {
            if (! empty($email['id'])) {
                $companyEmail = CompanyEmail::query()
                    ->where('company_id', $company->id)
                    ->findOrFail($email['id']);

                $companyEmail->update([
                    'email' => $email['email'],
                ]);

                $keptIds[] = $companyEmail->id;
            } else {
                $created = $company->emails()->create([
                    'email' => $email['email'],
                ]);

                $keptIds[] = $created->id;
            }
        }

        $company->emails()->whereNotIn('id', $keptIds)->delete();
    }

    /**
     * @param  array<int, array<string, mixed>>  $phones
     */
    private function syncPhones(Company $company, array $phones): void
    {
        $keptIds = [];

        foreach ($phones as $phone) {
            if (! empty($phone['id'])) {
                $companyPhone = CompanyPhone::query()
                    ->where('company_id', $company->id)
                    ->findOrFail($phone['id']);

                $companyPhone->update([
                    'phone' => $phone['phone'],
                ]);

                $keptIds[] = $companyPhone->id;
            } else {
                $created = $company->phones()->create([
                    'phone' => $phone['phone'],
                ]);

                $keptIds[] = $created->id;
            }
        }

        $company->phones()->whereNotIn('id', $keptIds)->delete();
    }

    /**
     * @param  array<int, array<string, mixed>>  $services
     */
    private function syncServices(Company $company, array $services): void
    {
        $keptIds = [];

        foreach ($services as $service) {
            if (! empty($service['id'])) {
                $companyService = CompanyService::query()
                    ->where('company_id', $company->id)
                    ->findOrFail($service['id']);

                $companyService->update([
                    'description' => $service['description'],
                ]);

                $keptIds[] = $companyService->id;
            } else {
                $created = $company->services()->create([
                    'description' => $service['description'],
                ]);

                $keptIds[] = $created->id;
            }
        }

        $company->services()->whereNotIn('id', $keptIds)->delete();
    }

    private function storeLogo(UploadedFile $logo): string
    {
        return $logo->store('companies/logos', 'public');
    }

    private function deleteLogo(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
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
            $company->coordinates()?->delete();

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
