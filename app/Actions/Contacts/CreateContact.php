<?php

namespace App\Actions\Contacts;

use App\Constants\StatusesConstants;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class CreateContact
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(array $data): Contact
    {
        try {
            DB::beginTransaction();

            $contact = Contact::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'message' => $data['message'],
                'company_id' => $data['companyId'] ?? 1,
                'status_id' => StatusesConstants::CONTACT_REQUEST,
            ]);

            DB::commit();

            return $contact->load(['company', 'status']);
        } catch (\Exception $e) {
            Log::channel('ContactError')->error("Message: {$e->getMessage()}, File: {$e->getFile()}, Line: {$e->getLine()}");
            DB::rollBack();

            throw $e;
        }
    }
}
