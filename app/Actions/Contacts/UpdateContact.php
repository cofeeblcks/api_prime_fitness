<?php

namespace App\Actions\Contacts;

use App\Constants\StatusesConstants;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class UpdateContact
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Contact $contact, array $data): Contact
    {
        try {
            DB::beginTransaction();

            $payload = [];

            if (array_key_exists('statusId', $data)) {
                $payload['status_id'] = $data['statusId'];

                if ((int) $data['statusId'] === StatusesConstants::CONTACT_ANSWERED) {
                    $payload['responded_at'] = now();
                } elseif ((int) $data['statusId'] === StatusesConstants::CONTACT_REQUEST) {
                    $payload['responded_at'] = null;
                }
            }

            if (array_key_exists('response', $data)) {
                $payload['response'] = $data['response'];
            }

            $contact->update($payload);

            DB::commit();

            return $contact->fresh(['company', 'status']);
        } catch (\Exception $e) {
            Log::channel('ContactError')->error("Message: {$e->getMessage()}, File: {$e->getFile()}, Line: {$e->getLine()}");
            DB::rollBack();

            throw $e;
        }
    }
}
