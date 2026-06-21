<?php

namespace App\Actions\QrCodes;

use App\Models\QrCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

final class CreateQrCode
{
    public function __construct(
        private Model $model,
    ) {}

    public function execute(): array
    {
        try {
            $code = uniqid();

            $qrCode = QrCode::create([
                'code' => $code,
            ]);

            $this->model->qrCodes()->attach($qrCode);

            return [
                'success' => true,
                'title' => 'Creación de código QR',
                'message' => 'Código QR creado exitosamente.',
                'qrCode' => $qrCode,
            ];
        } catch (\Exception $e) {
            Log::channel('QrCodeError')->error("Message: {$e->getMessage()}, File: {$e->getFile()}, Line: {$e->getLine()}");

            return [
                'success' => false,
                'title' => 'Creación de código QR',
                'message' => 'Error al crear el código QR. Algunos de los datos no son validos o faltan datos.',
            ];
        }
    }
}
