<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;
use Illuminate\Http\Request;

class UserResource extends AbstractJsonResource
{
    /**
     * @param Request $request
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function toArray($request)
    {
        $expand = request()->get('expand', []);

        $data = [
            'uuid' => $this->getKey(),
            'username' => $this->username,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'banned_at' => $this->banned_at,
            'avatar_img' => $this->avatar_img,
            'avatar_img_absolute' => $this->avatar_img_absolute,
            'cover_img' => $this->cover_img,
            'cover_img_absolute' => $this->cover_img_absolute,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (!auth()->guest()) {
            $data['current_membership'] = $this->currentMemberships;
        }

        if (\in_array('user__roles', $expand)) {
            $data['roles'] = RoleResource::collection($this->roles);
        }

        if (\in_array('user__user_config', $expand)) {
            $data['user_config'] = new UserConfigResource($this->userConfig);
        }

        if (\in_array('user__user_detail', $expand)) {
            $data['user_detail'] = new UserDetailResource($this->userDetails);
        }

        return $data;
    }
}
