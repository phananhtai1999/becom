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
            'full_name' => $this->full_name,
            'credit' => $this->credit,
            'email' => $this->email,
            'banned_at' => $this->banned_at,
            'can_add_smtp_account' => $this->can_add_smtp_account,
            'can_remove_footer_template' => $this->can_remove_footer_template,
            'avatar_img' => $this->avatar_img,
            'avatar_img_absolute' => $this->avatar_img_absolute,
            'cover_img' => $this->cover_img,
            'cover_img_absolute' => $this->cover_img_absolute,
            'platform_package' => $this->platform_package,
            'team' => $this->team,
            'app_id' => $this->app_id,
            'user_uuid' => $this->user_uuid,
            'email_verified_at' => $this->email_verified_at,
            'email_verification_code' => $this->email_verification_code,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('user__partner', $expand)) {
            $data['partner'] = new PartnerResource($this->partner);
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

        if (\in_array('user__contacts', $expand)) {
            $data['contacts'] = ContactResource::collection($this->contacts);
        }

        if (\in_array('user__contact_lists', $expand)) {
            $data['contact_lists'] = ContactListResource::collection($this->contactLists);
        }

        if (\in_array('user__business_management', $expand)) {
            $data['business_management'] = BusinessManagementResource::collection($this->businessManagements);
        }

        if (\in_array('user__domains', $expand)) {
            $data['domains'] = DomainResource::collection($this->domains);
        }

        if (\in_array('user__domains', $expand)) {
            $data['domains'] = DomainResource::collection($this->domains);
        }

        if (\in_array('user__partner_user', $expand)) {
            $data['partner_user'] = new PartnerUserResource($this->partnerUser);
        }

        if (\in_array('user__user_trackings', $expand)) {
            $data['user_trackings'] = UserTrackingResource::collection($this->userTrackings);
        }

        if (\in_array('user__article_series', $expand)) {
            $data['article_series'] = ArticleSeriesResource::collection($this->articleSeries);
        }

        return $data;
    }
}
