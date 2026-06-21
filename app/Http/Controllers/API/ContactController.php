<?php

namespace App\Http\Controllers\API;

use App\Actions\Contacts\CreateContact;
use App\Actions\Contacts\UpdateContact;
use App\Constants\ApiStatuses;
use App\Constants\ErrorMessages;
use App\Helpers\MessageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactListRequest;
use App\Http\Requests\ContactRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactResource;
use App\Mail\ContactInquiryMail;
use App\Models\Company;
use App\Models\Contact;
use App\Traits\Api\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    use ApiResponse;

    private const ENTITY = 'Contacto';

    public function index(ContactListRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $query = Contact::query()
            ->with(['company', 'status'])
            ->orderByDesc('created_at');

        if (! empty($validated['status_id'])) {
            $query->where('status_id', $validated['status_id']);
        }

        if (! empty($validated['company_id'])) {
            $query->where('company_id', $validated['company_id']);
        }

        if (! empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if (! empty($validated['startDate'])) {
            $query->whereDate('created_at', '>=', $validated['startDate']);
        }

        if (! empty($validated['endDate'])) {
            $query->whereDate('created_at', '<=', $validated['endDate']);
        }

        $meta = [];

        if (isset($validated['per_page']) && ! empty($validated['per_page'])) {
            $perPage = $validated['per_page'] ?? 25;
            $contacts = $query->paginate($perPage);
            $meta = $this->paginationMeta($contacts);
        } else {
            $contacts = $query->get();
        }

        return $this->successResponse(
            ContactResource::collection($contacts),
            MessageHelper::make(self::ENTITY, __FUNCTION__, true, false),
            $meta
        );
    }

    public function store(ContactRequest $request): JsonResponse
    {
        try {
            $companyId = $request->integer('companyId', 1);

            $company = Company::query()
                ->with('emails')
                ->findOrFail($companyId);

            $recipient = $company->emails->first()?->email;

            if (! $recipient) {
                return $this->errorResponse(
                    'La empresa no tiene correos de contacto configurados.',
                    ApiStatuses::STATUS_UNPROCESSABLE_ENTITY,
                );
            }

            $contact = (new CreateContact)->execute([
                'name' => $request->string('name')->toString(),
                'email' => $request->string('email')->toString(),
                'phone' => $request->filled('phone') ? $request->string('phone')->toString() : null,
                'message' => $request->string('message')->toString(),
                'companyId' => $companyId,
            ]);

            Mail::to($recipient)->queue(new ContactInquiryMail(
                senderName: $contact->name,
                senderEmail: $contact->email,
                senderPhone: $contact->phone,
                inquiryMessage: $contact->message,
                companyName: $company->name,
            ));

            return $this->successResponse(
                [],
                'Tu mensaje ha sido enviado correctamente. Te contactaremos pronto.',
            );
        } catch (\Throwable $e) {
            return $this->errorResponse(
                ErrorMessages::SERVER_ERROR,
                ApiStatuses::STATUS_INTERNAL_SERVER_ERROR,
                $e,
            );
        }
    }

    public function show(Contact $contact): JsonResponse
    {
        return $this->successResponse(
            new ContactResource($contact->load(['company', 'status'])),
            MessageHelper::make(self::ENTITY, __FUNCTION__, false, false)
        );
    }

    public function update(ContactUpdateRequest $request, Contact $contact): JsonResponse
    {
        try {
            $updated = (new UpdateContact)->execute($contact, $request->validated());

            return $this->successResponse(
                new ContactResource($updated),
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

    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();

        return $this->successResponse(
            message: MessageHelper::make(self::ENTITY, __FUNCTION__, false, false)
        );
    }
}
