<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\GpsData;

class GpsService
{
	public $db;

    public function __construct()
    {
    	$this->db = gpsData::all();
    }

    public function addDevice($request) 
    {
        //Validation
        $data = $request->validate([
            'coordinates' => 'required|string',
            'deviceId' => 'required|string',
            'destination' => 'required'
        ]);

        //Saving valid data
        gpsData::saveGpsData($data['deviceId'], $data['coordinates'], $data['destination']);
    }

    public function adminPageData() 
    {
        $lastDevice = $this->db->last();
        //Nominatim api for converting coordinates to address
        $nominatim = $this->nominatimRequest($lastDevice['latitude'], $lastDevice['longtitude']);
        //Distance between devices
        $distance = $this->deviceDistance();
        $data = [
            'latitude' => $lastDevice['latitude'],
            'longtitude' => $lastDevice['longtitude'],
            'devices' => $this->db,
            'lastDevice' => $lastDevice,
            'nominatim' => $nominatim,
            'distance' => $distance
        ];
        return $data;
    }

    public function nominatimRequest($lat, $lon) 
    {   
        $url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' . $lat . '&lon=' . $lon . '&email=domas.sab@gmail.com';
        $json = file_get_contents($url);
        $array = json_decode($json, true);
        $items = array();

        foreach ($array['address'] as $value) {
            $items[] = $value;
        }

        return $items;
    }

    public function deviceDistance() 
    {
        $items = array();
        $data = $this->db;

        for ($i=0; $i < count($data) - 1; $i++) { 
            for ($x=1; $x < count($data); $x++) { 
                if ($i != $x) {
                        $dist = $this->distance(
                        $data[$i]['latitude'], 
                        $data[$i]['longtitude'],
                        $data[$x]['latitude'],
                        $data[$x]['longtitude'],
                        'K');
                    $items[$data[$i]['deviceId']."-".$data[$x]['deviceId']] = $dist; 
                } else continue;  
                $maxDist = max(array_keys($items));
                $max = $maxDist . ' - ' . $items[$maxDist];     
            }
            return $max;
        }
    }

    public function distance($lat1, $lon1, $lat2, $lon2, $unit) 
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
          return ($miles * 1.609344);
        } else if ($unit == "N") {
          return ($miles * 0.8684);
        } else {
          return $miles;
        }
    }
}