<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" href="{{ URL::asset(config('logo.logos')::first()->icon)}}">
    <!-- Scripts -->
    {{-- <script src="{{ asset('js/app.js') }}" defer></script> --}}
    {{-- <link rel="stylesheet" href="{{asset('login_css/fonts/icomoon/style.css')}}">
    <link rel="stylesheet" href="{{asset('login_css/css/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('login_css/css/bootstrap.min.css')}}"> --}}
    <link rel="stylesheet" href="{{asset('login_css/css/style.css')}}">

    <link rel="stylesheet" href="{{ asset('/body_css/vendors/feather/feather.css') }}">
	<link rel="stylesheet" href="{{ asset('/body_css/vendors/ti-icons/css/themify-icons.css') }}">
	<link rel="stylesheet" href="{{ asset('/body_css/vendors/css/vendor.bundle.base.css') }}">

    <link rel="stylesheet" href="{{ asset('/body_css/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
	<link rel="stylesheet" href="{{ asset('/body_css/vendors/ti-icons/css/themify-icons.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('/body_css/js/select.dataTables.min.css') }}">
	<!-- Plugin css for this page -->
	<link rel="stylesheet" href="{{ asset('/body_css/vendors/select2/select2.min.css') }}">
	<link rel="stylesheet" href="{{ asset('/body_css/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
	<link rel="stylesheet" href="{{ asset('/body_css/css/vertical-layout-light/style.css') }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    {{-- <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css"> --}}
    <style>
        .loader {
            position: fixed;
            left: 0px;
            top: 0px;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background: url("{{ asset('images/m.png')}}") 50% 50% no-repeat white ;
            opacity: .8;
            background-size:120px 120px;
        }
        .orangebox {
			background-color: lightgrey;
			width: 15px;
			height: 15px;
			border: 10px solid orange;
			float: right;
		}

		.orangebox1 {
			background-color: lightgrey;
			width: 15px;
			height: 15px;
			border: 10px solid orange;
			display: inline-block;
		}

		.green {
			background-color: lightgrey;
			width: 15px;
			height: 15px;
			border: 10px solid green;
			display: inline-block;
		}
        /*Hide all except first fieldset*/
		#msform fieldset:not(:first-of-type) {
			display: none;
		}

		#msform fieldset .form-card {
			text-align: left;
			color: #9E9E9E;
		}



		#msform .action-button:hover,
		#msform .action-button:focus {
			box-shadow: 0 0 0 2px white, 0 0 0 3px skyblue;
		}


		#msform .action-button-previous:hover,
		#msform .action-button-previous:focus {
			box-shadow: 0 0 0 2px white, 0 0 0 3px #616161;
		}

		/*Dropdown List Exp Date*/
		select.list-dt {
			border: none;
			outline: 0;
			border-bottom: 1px solid #ccc;
			padding: 2px 5px 3px 5px;
			margin: 2px;
		}

		select.list-dt:focus {
			border-bottom: 2px solid skyblue;
		}

		/*The background card*/
		.card {
			z-index: 0;
			border: none;
			border-radius: 0.5rem;
			position: relative;
		}

		/*FieldSet headings*/
		.fs-title {
			font-size: 25px;
			color: #2C3E50;
			margin-bottom: 10px;
			font-weight: bold;
			text-align: left;
		}

		/*progressbar*/
		#progressbar {
			margin-bottom: 30px;
			overflow: hidden;
			color: lightgrey;
		}

		#progressbar .active {
			color: #000000;
		}

		#progressbar li {
			list-style-type: none;
			font-size: 12px;
			width: 25%;
			float: left;
			position: relative;
		}

		/*Icons in the ProgressBar*/
		#progressbar #account:before {
			font-family: FontAwesome;
			content: "\f007";
		}

		#progressbar #personal:before {
			font-family: FontAwesome;
			content: "\f007";
		}

		#progressbar #payment:before {
			font-family: FontAwesome;
			content: "\f09d";
		}

		#progressbar #confirm:before {
			font-family: FontAwesome;
			content: "\f090";
		}

		.user:before {
			font-family: FontAwesome;
			content: "\f02d";
		}

		.employment:before {
			font-family: FontAwesome;
			content: "\f0f0";
		}

		/*ProgressBar before any progress*/
		#progressbar li:before {
			width: 50px;
			height: 50px;
			line-height: 45px;
			display: block;
			font-size: 18px;
			color: #ffffff;
			background: lightgray;
			border-radius: 50%;
			margin: 0 auto 10px auto;
			padding: 2px;
		}

		/*ProgressBar connectors*/
		#progressbar li:after {
			content: '';
			width: 100%;
			height: 2px;
			background: lightgray;
			position: absolute;
			left: 0;
			top: 25px;
			z-index: -1;
		}

		/*Color number of the step and the connector before it*/
		#progressbar li.active:before,
		#progressbar li.active:after {
			background: skyblue;
		}

		/* width */
		::-webkit-scrollbar {
			width: 5px;
		}

		/* Track */
		::-webkit-scrollbar-track {
			background: #f1f1f1;
		}

		/* Handle */
		::-webkit-scrollbar-thumb {
			background: #888;
		}

		/* Handle on hover */
		::-webkit-scrollbar-thumb:hover {
			background: #555;
		}

		.tab-content {
			padding: 20px;
		}

		table.table,
		table.dataTable,
		.table {
			width: 100% !important;
			border-collapse: separate !important;
			border-spacing: 0 !important;
			border: 1px solid #d6dee8 !important;
			border-radius: 8px !important;
			overflow: hidden !important;
			background: #ffffff !important;
			box-shadow: 0 6px 18px rgba(31, 45, 61, 0.06) !important;
		}

		table.table th,
		table.table td,
		table.dataTable th,
		table.dataTable td,
		.table th,
		.table td {
			border-top: 0 !important;
			border-left: 0 !important;
			border-right: 1px solid #d6dee8 !important;
			border-bottom: 1px solid #d6dee8 !important;
			color: #2d3748 !important;
			line-height: 1.35 !important;
			padding: .7rem .85rem !important;
			vertical-align: middle !important;
		}

		table.table th:last-child,
		table.table td:last-child,
		table.dataTable th:last-child,
		table.dataTable td:last-child,
		.table th:last-child,
		.table td:last-child {
			border-right: 0 !important;
		}

		table.table tbody tr:last-child td,
		table.dataTable tbody tr:last-child td,
		.table tbody tr:last-child td {
			border-bottom: 0 !important;
		}

		table.table thead th,
		table.dataTable thead th,
		.table thead th {
			background: #f4f7fb !important;
			color: #24324b !important;
			font-size: .78rem !important;
			font-weight: 700 !important;
			letter-spacing: 0 !important;
			text-transform: uppercase !important;
			white-space: nowrap !important;
		}

		table.table tbody tr:nth-child(even),
		table.dataTable tbody tr:nth-child(even),
		.table tbody tr:nth-child(even) {
			background: #fbfcfe !important;
		}

		table.table tbody tr:hover,
		table.dataTable tbody tr:hover,
		.table tbody tr:hover {
			background: #eef5ff !important;
			transition: background-color .15s ease-in-out;
		}

		.dataTables_wrapper .dataTables_filter input,
		.dataTables_wrapper .dataTables_length select,
		.dt-container .dt-search input,
		.dt-container .dt-length select {
			border: 1px solid #d6dee8 !important;
			border-radius: 6px !important;
			color: #2d3748 !important;
			min-height: 34px !important;
			padding: .35rem .6rem !important;
		}

		.dataTables_wrapper .dataTables_filter input:focus,
		.dataTables_wrapper .dataTables_length select:focus,
		.dt-container .dt-search input:focus,
		.dt-container .dt-length select:focus {
			border-color: #4b8fef !important;
			box-shadow: 0 0 0 3px rgba(75, 143, 239, .14) !important;
			outline: 0 !important;
		}

		input::-webkit-outer-spin-button,
		input::-webkit-inner-spin-button {
			-webkit-appearance: none;
			margin: 0;
		}

		/* Firefox */
		input[type=number] {
			-moz-appearance: textfield;
		}
    </style>
    <!-- LogIN CSS -->
  

    <!-- Styles -->
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}
</head>
<body>
    <div id = "loader" style="display:none;" class="loader">
    </div>
    <div id="app">
        <main class="py-4">
            @yield('content')
        </main>
    </div>

    {{-- <script src="{{asset('login_css/js/jquery-3.3.1.min.js')}}"></script>
    <script src="{{asset('login_css/js/popper.min.jss')}}"></script>
    <script src="{{asset('login_css/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('login_css/js/main.js')}}"></script> --}}

    <script src="{{ asset('/body_css/vendors/js/vendor.bundle.base.js') }}"></script>
	<!-- endinject -->
	<!-- Plugin js for this page -->
	<script src="{{ asset('/body_css/vendors/chart.js/Chart.min.js') }}"></script>

	<script src="{{ asset('/body_css/vendors/select2/select2.min.js') }}"></script>

    <script src="{{ asset('/body_css/js/dashboard.js') }}"></script>
	<script src="{{ asset('/body_css/js/select2.js') }}"></script>


	<script src="{{ asset('/body_css/vendors/datatables.net/jquery.dataTables.js') }}"></script>
	<script src="{{ asset('/body_css/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
	<script src="{{ asset('/body_css/vendors/jquery.repeater/jquery.repeater.min.js') }}"></script>

	<script src="{{ asset('/body_css/js/dataTables.select.min.js') }}"></script>

	<script src="{{ asset('/body_css/js/off-canvas.js') }}"></script>
	<script src="{{ asset('/body_css/js/hoverable-collapse.js') }}"></script>
	<script src="{{ asset('/body_css/js/template.js') }}"></script>
	<script src="{{ asset('/body_css/js/settings.js') }}"></script>
	<script src="{{ asset('/body_css/js/todolist.js') }}"></script>

	<script src="{{ asset('/body_css/js/tabs.js') }}"></script>
	<script src="{{ asset('/body_css/js/form-repeater.js') }}"></script>
	<script src="{{ asset('/body_css/vendors/sweetalert/sweetalert.min.js') }}"></script>
    
    <script src="{{ asset('/body_css/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
    <script src="{{ asset('/body_css/vendors/inputmask/jquery.inputmask.bundle.js') }}"></script>
    <script src="{{ asset('/body_css/js/inputmask.js') }}"></script>
    @include('sweetalert::alert')
    <script type='text/javascript'>
        function show() {
            document.getElementById("loader").style.display="block";
        }
    </script>
    <script>
		$(document).ready(function() {

			var current_fs, next_fs, previous_fs; //fieldsets
			var opacity;

			$(".next").click(function() {
				current_fs = $(this).parent();
				next_fs = $(this).parent().next();
				var fld = $(this).closest("fieldset").attr('id');
				// alert(fld);
				var isValid = true;
				var classname = 'required';
				$('#' + fld + ' .' + classname + '').each(function(i, obj) {
					if (obj.value == '') {
						isValid = false;
						return isValid;
					}
				});

				if (!isValid) {
					$('#' + fld + ' .' + classname + '').each(function(i, obj) {
						if (obj.value == '') {

							var d = (obj.className).includes("js-example-basic-single");
							if (d == false) {
								// return false;
								obj.style.border = '1px solid red';
							} else {

								$("select[name='" + obj.getAttribute("name") + "']").next("span").css(
									'border', '1px solid red');
								console.log(obj.getAttribute("name"));
							}
						} else {
							$("select[name='" + obj.getAttribute("name") + "']").next("span").css(
								'border', '1px solid #CED4DA');
							obj.style.border = '1px solid #CED4DA';
						}
					});
				}
				if (isValid) {


					//Add Class Active
					$("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");
					next_fs.show();
					//hide the current fieldset with style
					current_fs.animate({
						opacity: 0
					}, {
						step: function(now) {
							// for making fielset appear animation
							opacity = 1 - now;

							current_fs.css({
								'display': 'none',
								'position': 'relative'
							});
							next_fs.css({
								'opacity': opacity
							});
						},
						duration: 600
					});
				}
				return isValid;

			});

			$(".previous").click(function() {

				current_fs = $(this).parent();
				previous_fs = $(this).parent().prev();

				//Remove class active
				$("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

				//show the previous fieldset
				previous_fs.show();

				//hide the current fieldset with style
				current_fs.animate({
					opacity: 0
				}, {
					step: function(now) {
						// for making fielset appear animation
						opacity = 1 - now;

						current_fs.css({
							'display': 'none',
							'position': 'relative'
						});
						previous_fs.css({
							'opacity': opacity
						});
					},
					duration: 600
				});
			});

			$('.radio-group .radio').click(function() {
				$(this).parent().find('.radio').removeClass('selected');
				$(this).addClass('selected');
			});

		});
	</script>
</body>
</html>
