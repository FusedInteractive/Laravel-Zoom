<?php

namespace Fused\Zoom\Contracts;

interface ZoomMeeting
{
    public function getZoomMeeting();

    public function setZoomMeeting($meeting);

    public function getZoomId();

    public function setZoomId($zoomId);

    public function getStartTime();

    public function setStartTime($startTime);

    public function getEndTime();

    public function setEndTime($endTime);

    public function getDuration();

    public function setDuration($duration);

    public function getTimezone();

    public function setTimezone($timezone);

    public function getZoomPassword();

    public function setZoomPassword($password);

    public function getZoomHostId();

    public function setZoomHostId($zoomHostId);

    public function getZoomStartUrl();

    public function setZoomStartUrl($zoomStartUrl);

    public function getTopic();

    public function setTopic($topic);

    public function createZoomInstantMeeting($host, $topic, $password = null, $data = []);

    public function createZoomScheduledMeeting($host, $topic, $startTime, $timezone = null, $duration = null, $password = null, $data = []);

    public function deleteZoomMeeting();

    public function endZoomMeeting();
}
