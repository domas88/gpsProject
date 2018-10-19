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

    /**
     * Description
     * @param Request $request 
     * @return type
     */
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

        return redirect()->route('admin');
    }

    /**
     * All data needed for admin page. Devices, coordinates, distances, addresses.
     * @return array
     */
    public function adminPageData() 
    {
        $db = gpsData::all();
        $lastDevice = $db->last();
        //Nominatim api for converting coordinates to address
        $nominatim = $this->nominatimRequest($lastDevice['latitude'], $lastDevice['longtitude']);
        //Distance between devices
        $distance = $this->deviceDistance();
        $data = [
            'latitude' => $lastDevice['latitude'],
            'longtitude' => $lastDevice['longtitude'],
            'devices' => $db,
            'lastDevice' => $lastDevice,
            'nominatim' => $nominatim,
            'distance' => $distance
        ];

        return view('admin')->with('data', $data);
    }

    /**
     * Nominatim for converting latitude and longtitude in to address
     * @param int $lat 
     * @param int $lon 
     * @return array
     */
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

    /**
     * Distances between devices
     * @return array
     */
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

    /**
     * Distance calculator
     * @param type $latitude1 
     * @param type $longtitude1
     * @param type $latitude2 
     * @param type $longtitude2 
     * @param type $speed metric 
     * @return float
     */
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
