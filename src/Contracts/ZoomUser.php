<?php

namespace Fused\Zoom\Contracts;

interface ZoomUser
{
    public function getZoomId();

    public function setZoomId($zoomId);

    public function createZoom($email, $type = 1, $data = []);

    public function updateZoom(array $data);

    public function deleteZoom();

    public function addToZoomGroup($zoomGroupId);
}
