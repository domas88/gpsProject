<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GpsData extends Model
{
    protected $table = 'gps_data';

    public static function saveGpsData($deviceId, $coordinates, $destination)
    {
    	$data = new GpsData();
        $data->deviceId = $deviceId;
        $location = explode(", ", $coordinates);
        $data->latitude = $location[0];
        $data->longtitude = $location[1];
        $data->destination = $destination;
        $data->save();
    }
}
