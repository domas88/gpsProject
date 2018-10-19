@extends('layouts.app')

@section('content')
<div class="container mt-3">
	<div class="row">
		<div id="devices" class="card col-4 mr-5">
			<div class="card-body">
				<h5>List of Devices:</h5>
				<ul class="list-group">
					@if (isset($data))
						@foreach ($data['devices'] as $key => $val)
							<li class="list-group-item">{{$val['deviceId']}}</li>
						@endforeach
					@else <h5>No devices found!</h5>
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
		<div id="distance" class="card col-4 mr-5">
			<div class="card-body">
				<h5>Maximum distance between devices:</h4>
				<ul class="list-group">
					@if (isset($data))
						<li class="list-group-item">{{ $data['distance'] }}</li>
					@endif
				</ul>
			</div>
		</div>
	</div>
</div>


<script>
	function initMap() {
		// The location of Uluru
		@if (isset($data))
			var uluru = {lat: {{ $data['latitude'] }}, lng: {{ $data['longtitude'] }}}
		@else var uluru = {lat: 54.6871555, lng: 25.279651400000034}
		@endif;
		// The map, centered at Uluru
		var map = new google.maps.Map(
		  document.getElementById('map'), {zoom: 12, center: uluru});
		// The marker, positioned at Uluru
		@if (isset($data['nominatim']))
			var marker = new google.maps.Marker({
				position: uluru,
				map: map
			});
			var jsData = <?php echo json_encode($data['nominatim']) ?>;
			var deviceData = <?php echo json_encode($data['lastDevice']) ?>;
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
