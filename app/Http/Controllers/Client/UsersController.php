<?php

namespace App\Http\Controllers\Client;


use App\Handlers\ImageUploadHandler;
use App\Http\Requests\Client\UserRequest;
use App\Models\Order;
use App\Models\User;
use App\Transformers\Client\UserTransformer;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{

    public function show()
    {
        $user = Auth::guard('client')->user();

        return $this->response->item($user, new UserTransformer());
    }

//    public function update(UserRequest $request, ImageUploadHandler $handler)
//    {
//        $user = Auth::guard('client')->user();
//
//        $attributes = $request->only(['avatar']);
//
//        if ($request->avatar)
//        {
//            $attributes['avatar'] = $handler->uploadOriginal($request->avatar, 'avatar/' . date('Ym', now()->timestamp), $request->avatar->hashName());
//        }
//
//        $user->update($attributes);
//
//        return $this->response->item($user, new UserTransformer());
//    }
}
