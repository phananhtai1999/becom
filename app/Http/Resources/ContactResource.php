<?php

namespace App\Http\Resources;

use App\Abstracts\AbstractJsonResource;

class ContactResource extends AbstractJsonResource
{
    /**
     * @param $request
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function toArray($request)
    {
        $expand = request()->get('expand', []);

        $data = [
            'uuid' => $this->getKey(),
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'middle_name' => $this->middle_name,
            'full_name' => $this->full_name,
            'points' => $this->points,
            'phone' => $this->phone,
            'dob' => $this->dob,
            'sex' => $this->sex,
            'city' => $this->city,
            'country' => $this->country,
            'avatar' => $this->avatar,
            'status_uuid' => $this->status_uuid,
            'status_active' => $this->status_active,
            'status_list' => $this->status_list,
            'admin_status_active' => $this->admin_status_active,
            'admin_status_list' => $this->admin_status_list,
            'user_uuid' => $this->user_uuid,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        if (\in_array('contact__contact_lists', $expand)) {
            $data['contact_lists'] = ContactListResource::collection($this->contactLists);
        }

        if (\in_array('contact__user', $expand)) {
            $data['user'] = new UserResource($this->user);
        }

        if (\in_array('contact__status', $expand)) {
            $data['status'] = new StatusResource($this->status);
        }

        if (\in_array('contact__companies', $expand)) {
            $data['company'] = CompanyResource::collection($this->companies);
        }

        if (\in_array('contact__positions', $expand)) {
            $data['position'] = PositionResource::collection($this->positions);
        }

        if (\in_array('contact__notes', $expand)) {
            $data['note'] = NoteResource::collection($this->notes);
        }

        if (\in_array('contact__reminds', $expand)) {
            $data['remind'] = RemindResource::collection($this->reminds);
        }

        if (\in_array('contact__activity_histories', $expand)) {
            $data['activity_histories'] = ActivityHistoryResource::collection($this->activityHistories);
        }

        return $data;
    }
}
