<?php

namespace App\Actions\Users;

use App\Models\User;
use App\Traits\Models\FillModelData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class UpdateUser
{
    use FillModelData;

    public function execute(int $userId, array $data): array
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($userId);
            $user->fill($this->fillData(User::class, $data));
            $user->save();

            DB::commit();

            return [
                'success' => true,
                'title' => 'Actualización de usuario',
                'message' => 'Usuario actualizado exitosamente.',
                'user' => $user,
            ];
        } catch (\Exception $e) {
            Log::channel('UserError')->error("Message: {$e->getMessage()}, File: {$e->getFile()}, Line: {$e->getLine()}");
            DB::rollBack();

            return [
                'success' => false,
                'title' => 'Actualización de usuario',
                'message' => 'Error al actualizar los datos del usuario.',
            ];
        }
    }
}
