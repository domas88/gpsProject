@extends('layouts.app')

@section('content')
<div class="container mt-3">
	<div class="row">
		<div id="adminCard" class="card col-4 mr-5">
			<div class="card-body">
				<p>List of Devices:</p>
				<ul class="list-group">
					@if (isset($devices))
						@foreach ($devices as $val)
							<li class="list-group-item">{{$val['deviceId']}}</li>
						@endforeach
					@endif
				</ul>
			</div>
			<a type="button" class="btn btn-primary" role="button" href="{{ route('home') }}">Add Device</a>
		</div>
		<div id="map" class="col-6"></div>
	</div>
</div>
<div class="container mt-3">
	<div class="row">
		<div id="adminCard" class="card col-4 mr-5">
			<div class="card-body">
				<p>Distance between devices:</p>
				<ul class="list-group">
						@foreach ($distance as $key => $val)
							<li class="list-group-item">{{$key . ': ' . $val . ' Km'}}</li>
						@endforeach
				</ul>
			</div>
		</div>
	</div>
</div>

<script>
	function initMap() {
		// The location of Uluru
		@if (isset($coordinates))
			var uluru = {lat: {{$coordinates[0]}}, lng: {{$coordinates[1]}}}
		@else var uluru = {lat: 11.5073509, lng: 11.127758299}
		@endif;
		// The map, centered at Uluru
		var map = new google.maps.Map(
		  document.getElementById('map'), {zoom: 12, center: uluru});
		// The marker, positioned at Uluru
		@if (isset($nominatim))
			var marker = new google.maps.Marker({
				position: uluru,
				map: map
			});
			var jsData = <?php echo json_encode($nominatim) ?>;
			var deviceData = <?php echo json_encode($lastDevice) ?>;
			var infowindow = new google.maps.InfoWindow();

	        marker.addListener('click', function() {
	        	infowindow.setContent(deviceData['deviceId'] + '</br>' + deviceData['destination'] + '</br>' + jsData[3] + ' ' + jsData[1] + ' ' + jsData[0]);
				infowindow.open(map, marker);
	    	});  
		@endif
	}
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD257lPGwQNh7jAMV4iSaKFbDk0wboDQx0&callback=initMap"
async defer>
</script>
