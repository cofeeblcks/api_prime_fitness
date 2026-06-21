<?php

namespace App\Actions\Users;

use App\Models\User;
use App\Traits\Models\FillModelData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

final class UpdateUser
{
    use FillModelData;

    public function execute(int $userId, array $data): array
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($userId);
            if (isset($data['photo']) && ! is_string($data['photo'])) {
                if ($user->photo) {
                    File::exists(storage_path('app/public/'.$user->photo)) ? Storage::delete($user->photo) : null;
                }

                $data['photo'] = $data['photo']->store('images/profiles', ['disk' => env('FILESYSTEM_DISK')]);
            }
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
