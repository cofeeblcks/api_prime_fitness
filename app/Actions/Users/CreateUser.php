<?php

namespace App\Actions\Users;

use App\Actions\QrCodes\CreateQrCode;
use App\Models\User;
use App\Traits\Models\FillModelData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class CreateUser
{
    use FillModelData;

    public function execute(array $data): array
    {
        try {
            DB::beginTransaction();

            $user = new User;
            if( !isset($data['password']) ){
                $data['password'] = uniqid();
            }

            if (isset($data['photo']) && ! is_null($data['photo']) && ! is_string($data['photo'])) {
                $data['photo'] = $data['photo']->store('images/profiles', ['disk' => config('filesystems.default')]);
            }

            $user->fill($this->fillData(User::class, $data));
            $user->save();

            if( $user->qrCodes->isEmpty() ){
                (new CreateQrCode($user))->execute();
            }

            DB::commit();

            return [
                'success' => true,
                'title' => 'Creación de usuario',
                'message' => 'Usuario creado exitosamente.',
                'user' => $user,
            ];
        } catch (\Exception $e) {
            Log::channel('UserError')->error("Message: {$e->getMessage()}, File: {$e->getFile()}, Line: {$e->getLine()}");
            DB::rollBack();

            return [
                'success' => false,
                'title' => 'Creación de usuario',
                'message' => 'Error al crear el usuario. Algunos de los datos no son validos o faltan datos.',
            ];
        }
    }
}
