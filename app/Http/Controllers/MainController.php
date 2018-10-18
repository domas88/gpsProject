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

        return view('admin')->with('devices', $devices);
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
        //Nominatim api for converting coordinates to address
        $nominatim = $this->nominatimRequest($request);
        
        return view('admin')->with('coordinates', $coordinates)->with('devices', $devices)->with('nominatim', $nominatim);
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

    public function testApi() {
        $lat = 54.701799;
        $lon = 25.302582;
        $nom = $this->nominatimRequest($lat, $lon);
        $items = array();

        foreach ($nom['address'] as $value) {
            $items[] = $value;
        }

        dd($items);
    }
}
