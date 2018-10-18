<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GpsData;

class MainController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index()
    {
        return view('welcome');
    }

    public function showAdminPage() 
    {
        $devices = gpsData::all();
        $distance = $this->deviceDistance();

        return view('admin')
            ->with('devices', $devices)
            ->with('distance', $distance);
    }

    public function addDevice(Request $request) 
    {
        //Validation
        $data = $request->validate([
            'coordinates' => 'required|string',
            'deviceId' => 'required|string',
            'destination' => 'required'
        ]);

        //Saving valid data
        gpsData::saveGpsData($data['deviceId'], $data['coordinates'], $data['destination']);

        //Separating latitude and longtitude
        $coordinates = explode(", ", $data['coordinates']);
        $devices = gpsData::all();
        $lastDevice = $devices->last();
        //Nominatim api for converting coordinates to address
        $nominatim = $this->nominatimRequest($request);
        //Distance between devices
        $distance = $this->deviceDistance();
        
        return view('admin')
            ->with('coordinates', $coordinates)
            ->with('devices', $devices)
            ->with('nominatim', $nominatim)
            ->with('lastDevice', $lastDevice)
            ->with('distance', $distance);

    }

    public function nominatimRequest($request) 
    {   
        $coordinates = explode(", ", $request['coordinates']);
        $url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' . $coordinates[0] . '&lon=' . $coordinates[1] . '&email=domas.sab@gmail.com';
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
        $data = gpsData::all();
        $items = array();

        for ($i=0; $i < count($data) - 1; $i++) { 
            for ($x=0; $x < count($data); $x++) { 
                if ($i != $x) {
                        $dist = $this->distance(
                        $data[$i]['latitude'], 
                        $data[$i]['longtitude'],
                        $data[$x]['latitude'],
                        $data[$x]['longtitude'],
                        'K');
                    $items[$data[$i]['deviceId']."-".$data[$x]['deviceId']] = $dist; 
                } else continue;        
            }
        }
        return $items;
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

    //Function for quick testing
    public function testApi() 
    {
        $data = gpsData::all();
        $items = array();
        $maxItems = array();

        for ($i=0; $i < count($data) - 1; $i++) { 
            for ($x=0; $x < count($data); $x++) { 
                if ($i != $x) {
                        $dist = $this->distance(
                        $data[$i]['latitude'], 
                        $data[$i]['longtitude'],
                        $data[$x]['latitude'],
                        $data[$x]['longtitude'],
                        'K');
                    $items[$data[$i]['deviceId']."-".$data[$x]['deviceId']] = $dist;
                    $max = max($items);
                } else continue;
                $maxItems[$i] = $max;        
            }
        }
        $dev1 = $data[0]['deviceId'];
        $dev2 = $data[2]['deviceId'];
        $result = $dev1.$dev2;
        dd($maxItems);
    }
}
