<?php

namespace Fused\Zoom\Traits;

use Carbon\Carbon;
use Fused\Zoom\Contracts\ZoomUser as ZoomUserContract;

trait ZoomMeeting
{
    public $zoom_meeting;

    public function getZoomMeeting()
    {
        if (!$this->zoom_meeting && $this->getZoomId()) {
            $this->refreshZoomMeeting();
        }

        return $this->zoom_meeting;
    }

    public function setZoomMeeting($meeting)
    {
        $this->zoom_meeting = $meeting;

        return $this;
    }

    protected function refreshZoomMeeting()
    {
        $zoom = app()->make('zoom');

        $this->zoom_meeting = $zoom->meeting->get([
            'host_id' => $this->getZoomHostId(),
            'id' => $this->getZoomId(),
        ]);
    }

    public function getZoomId()
    {
        return $this->zoom_id;
    }

    public function setZoomId($zoomId)
    {
        $this->zoom_id = $zoomId;

        return $this;
    }

    public function getStartTime()
    {
        if ($this->start_time && !$this->start_time instanceof Carbon) {
            $this->start_time = Carbon::parse($this->start_time, $this->timezone);
        }

        return $this->start_time;
    }

    public function setStartTime($startTime)
    {
        if (!$startTime instanceof Carbon && !is_null($startTime)) {
            $startTime = Carbon::parse($startTime, $this->timezone);
        }

        $this->start_time = $startTime;

        return $this;
    }

    public function getEndTime()
    {
        if ($this->end_time && !$this->end_time instanceof Carbon) {
            $this->end_time = Carbon::parse($this->end_time, $this->timezone);
        }

        return $this->end_time;
    }

    public function setEndTime($endTime)
    {
        if (!$endTime instanceof Carbon && !is_null($endTime)) {
            $endTime = Carbon::parse($endTime, $this->timezone);
        }

        $this->end_time = $endTime;

        return $this;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    public function getTimezone()
    {
        return $this->timezone;
    }

    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function getZoomPassword()
    {
        return $this->zoom_password;
    }

    public function setZoomPassword($password)
    {
        $this->zoom_password = $password;

        return $this;
    }

    public function getZoomHostId()
    {
        return $this->zoom_host_id;
    }

    public function setZoomHostId($zoomHostId)
    {
        $this->zoom_host_id = $zoomHostId;

        return $this;
    }

    public function getZoomStartUrl()
    {
        return $this->zoom_start_url;
    }

    public function setZoomStartUrl($zoomStartUrl)
    {
        $this->zoom_start_url = $zoomStartUrl;

        return $this;
    }

    public function getTopic()
    {
        return $this->topic;
    }

    public function setTopic($topic)
    {
        $this->topic = $topic;

        return $this;
    }

    public function createZoomInstantMeeting($host, $topic, $password = null, $data = [])
    {
        $data = array_merge([
            'type' => 1,
        ], $data);

        $this->processZoomCreateMeeting($host, $topic, $password, $data);

        return $this;
    }

    public function createZoomScheduledMeeting($host, $topic, $startTime, $timezone = null, $duration = null, $password = null, $data = [])
    {
        if (!$startTime instanceof Carbon) {
            $startTime = Carbon::parse($startTime, $timezone);
        }

        $data = array_merge([
            'start_time' => $this->formatDateToZoom($startTime),
            'timezone' => $timezone,
            'duration' => $duration,
            'type' => 2,
        ], $data);

        $this->processZoomCreateMeeting($host, $topic, $password, $data);

        $this->setStartTime($startTime);
        $this->setTimezone($timezone);

        return $this;
    }

    protected function processZoomCreateMeeting(ZoomUserContract $host, $topic, $password = null, $data = [])
    {
        $data = array_merge([
            'host_id' => $host->getZoomId(),
            'password' => $password,
            'topic' => $topic,
        ], $data);

        $zoom = app()->make('zoom');

        $zoomMeeting = $zoom->meeting->create($data);

        $this->setZoomMeeting($zoomMeeting);
        $this->setZoomId($zoomMeeting->id);
        $this->setZoomPassword($password);
        $this->setZoomHostId($zoomMeeting->host_id);
        $this->setZoomStartUrl($zoomMeeting->start_url);

        return $this;
    }

    public function updateZoomMeeting($data = [])
    {
        $data = array_merge([
            'id' => $this->getZoomId(),
            'host_id' => $this->getZoomHostId(),
            'password' => $this->getZoomPassword(),
            'start_time' => $this->formatDateToZoom($this->getStartTime()),
            'duration' => $this->getDuration(),
            'timezone' => $this->getTimezone(),
        ], $data);

        $zoom = app()->make('zoom');

        $zoom->meeting->update($data);

        // Only refresh if it's needed. Don't need to make a pointless API call
        if ($this->zoom_meeting) {
            $this->refreshZoomMeeting();
        }

        return $this;
    }

    public function deleteZoomMeeting()
    {
        $zoom = app()->make('zoom');

        $zoom->meeting->delete([
            'host_id' => $this->getZoomHostId(),
            'id' => $this->getZoomId(),
        ]);

        $this->setZoomMeeting(null);
        $this->setZoomId(null);
        $this->setZoomPassword(null);
        $this->setZoomHostId(null);
        $this->setDuration(null);
        $this->setTimezone(null);
        $this->setStartTime(null);
        $this->setEndTime(null);
        $this->setZoomStartUrl(null);

        return $this;
    }

    public function endZoomMeeting()
    {
        $zoom = app()->make('zoom');
        
        $response = $zoom->meeting->delete([
            'host_id' => $this->getZoomHostId(),
            'id' => $this->getZoomId(),
        ]);

        $this->setEndTime($response->ended_at);

        return $this;
    }

    /**
     * Zoom uses a non-standard date format.
     * Format: 2015-01-01T00:00:00Z
     */
    protected function formatDateToZoom(Carbon $time)
    {
        return $time->copy()->tz('UTC')->format('Y-m-d\TH:i:s\Z');
    }
}
