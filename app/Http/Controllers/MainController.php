<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GpsService;
use App\GpsData;
use Mail;

class MainController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    public $service;

    public function __construct()
    {
        $this->middleware('auth');
        $this->service = app()->make('Service');
    }
    
    public function index()
    {
        return view('pages.welcome');
    }

    public function addDevice(Request $request) 
    {
        $this->service->addDevice($request);

        return redirect()->route('admin');
    }

    public function deleteDevice($id)
    {
        gpsData::destroy($id);

        return redirect()->route('admin');
    }

    public function adminPage() 
    {
        $data = $this->service->adminPageData();

        return view('pages.admin')->with('data', $data);
    }

    public function sendMail() 
    {
        Mail::send(['message'=>'mail'],['name','yourname'],function($message){
            $message->to('mail@gmail.com', 'to')->subject('Test Email');
            $message->from('youremail@gmail.com','name');
        });
    }
}
