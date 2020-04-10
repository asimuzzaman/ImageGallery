<!DOCTYPE html>
<html>
<head>
	<title>@yield('title')</title>
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap.min.css') }}"> {{-- Bootstrap 4 --}}

	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous"> {{-- for icons --}}

	<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/dropzone.css') }}"> {{-- For dropzone file uploader --}}
	<link href="{{ URL::asset('css/simple-lightbox.min.css') }}" rel="stylesheet" /> {{-- For lightbox gallery --}}

	<style type="text/css">
		.slide-object p{
			text-decoration:none;
			margin-top: 0em;
			/*display:inline-block;*/
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}

		.slide-object .slider {
			-webkit-transition: 3.3s;
			-moz-transition: 3.3s;
			transition: 3.3s;     
			
			-webkit-transition-timing-function: linear;
			-moz-transition-timing-function: linear;
			transition-timing-function: linear;
		}

		.slide-object {
			position: inherit;
			left: 10%;
			overflow: hidden;
			width: 80%;
			/*padding-top: 5px;*/
		}

		.slider {
			/*margin-left: 0em;*/
		}

		.slide-object:hover .slider {
			margin-left: -300px;
		}

		/*For live search*/
		.overlay {
			position: absolute;
			z-index: 3;
			max-width: 80%;
			min-width: 50%;
		}
	</style>

</head>
	<body>

@yield('content')

	<script src="{{ URL::asset('js/jquery.min.js') }}"></script>
	<script src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
	
	<script src="{{ URL::asset('js/sweetalert.min.js') }}"></script> {{-- for sweetalert --}}
	<script src="{{ URL::asset('js/dropzone.js') }}"></script> {{-- For dropzone file uploader --}}
@yield('script')
	</body>

</html>