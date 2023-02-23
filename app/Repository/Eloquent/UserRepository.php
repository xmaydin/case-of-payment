<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository
{

    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Kullanıcı giriş.
     * Eski tokenları sil.
     * Yeni token yarat.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $user = $this->firstWhere([
            'email' => $request->email
        ]);

        if (!$user || !Hash::check($request->password, $user->password))
            return $this->sendError(message: 'Giriş bilgileri yanlış. Lütfen kontrol edin!', code: 401);

        $user->tokens()->delete();

        $token = $user->createToken('client_token')->plainTextToken;

        return $this->sendResponse(message: 'Başarıyla giriş yaptınız.', data: [
            'token' => $token,
            'expiration' => config('sanctum.expiration'),
            'type' => 'bearer'
        ]);

    }


}
