@extends('layouts.app')

@section('content')
<div id="form" class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('addDevice') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="deviceId" class="col-sm-4 col-form-label text-md-right">Device id</label>

                            <div class="col-md-6">
                                <input id="deviceId" type="text" class="deviceId" name="deviceId" value="" required autofocus>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="coordinates" class="col-sm-4 col-form-label text-md-right">Coordinates</label>

                            <div class="col-md-6">
                                <input id="coordinates" type="text" class="coordinates" name="coordinates" value="" placeholder="E.g. 54.7005, 25.296117" required autofocus>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="destination" class="col-sm-4 col-form-label text-md-right">Destination</label>

                            <div class="col-md-6">
                                <input id="destination" type="radio" class="destination" name="destination" value="Home" required autofocus>Home
                                <input id="destination" type="radio" class="destination" name="destination" value="Work" required autofocus>Work
                            </div>
                        </div>

                        <div class="form-group row justify-content-md-center">
                            <div class="col-md-1">
                                <input type="submit" value="Send">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection