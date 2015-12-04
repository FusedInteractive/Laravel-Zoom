<?php

namespace Fused\Zoom\Traits;

trait ZoomUser
{
    public function getZoomId()
    {
        return $this->zoom_id;
    }

    public function setZoomId($zoomId)
    {
        $this->zoom_id = $zoomId;

        return $this;
    }

    public function createZoom($email, $type = 1, $data = [])
    {
        $zoom = app()->make('zoom');

        $data = array_merge([
            'type' => $type,
            'email' => $email,
        ], $data);

        $zoomUser = $zoom->user->custcreate($data);

        $this->setZoomId($zoomUser->id);
        
        return $this;
    }

    public function updateZoom(array $data)
    {
        $zoom = app()->make('zoom');

        $data = array_merge([
            'id' => $this->getZoomId(),
        ], $data);

        $zoom->user->update($data);

        return $this;
    }

    public function deleteZoom()
    {
        $zoom = app()->make('zoom');

        $zoom->user->delete([
            'id' => $this->getZoomId(),
        ]);

        return $this;
    }

    public function addToZoomGroup($zoomGroupId)
    {
        $zoom = app()->make('zoom');

        $zoom->group->member->add([
            'id' => $zoomGroupId,
            'member_ids' => $this->getZoomId(),
        ]);

        return $this;
    }
}
