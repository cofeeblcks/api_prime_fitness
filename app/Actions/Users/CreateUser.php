<?php

namespace App\Actions\Users;

use App\Models\Locus;
use App\Models\Table;
use App\Models\User;
use App\Traits\Models\FillModelData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
            $user->fill($this->fillData(User::class, $data));
            $user->save();

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
