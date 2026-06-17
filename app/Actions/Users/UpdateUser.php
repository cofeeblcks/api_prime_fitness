<?php

namespace App\Actions\Users;

use App\Models\Locus;
use App\Models\Table;
use App\Models\User;
use App\Traits\Actions\FillModelData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

final class UpdateUser
{
    use FillModelData;
    public function execute(int $userId, array $data): array
    {
        try {
            DB::beginTransaction();

            $data['tableId'] = $this->getTableId($data['locus']);

            $user = User::find($userId);
            $user->fill($this->fillData(User::class, $data));
            $user->update();

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

    private function getTableId(array $data): int|null
    {
        if( count($data) > 0 ){
            $locus = Locus::firstOrCreate([
                'name' => $data['name'],
                'address' => $data['address'],
                'city_id' => $data['cityId'],
            ]);

            if( is_string($data['tableId']) ){
                $table = Table::firstOrCreate([
                    'name' => $data['tableId'],
                    'locus_id' => $locus->id,
                ]);

                return $table->id;
            }

            return $data['tableId'];
        }
        return null;
    }
}
