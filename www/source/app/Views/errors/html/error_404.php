<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>404 Page Not Found</title>
	<link type="text/css" href="<?= '/' ?>css/style.css" rel="stylesheet">
	<link type="text/css" href="<?= '/' ?>css/_main.css" rel="stylesheet">
	<style>
		*{
			color: #263238 !important;
		}
		#parallax {
			background-image: url(<?= base_url() . '/image/404.svg' ?>);
			background-repeat: no-repeat;
			background-position: center;
			background-position: 50% 50%;
		}
	</style>
</head>
<body class="bg-dark" >
	<div class="container d-flex" id="app">
		<div class="row my-auto w-100">
			<div class="col-md-12 d-flex flex-lg-row flex-column align-items-md-center" style="height: 100vh;" >
				<div class="d-flex flex-column mt-auto mb-lg-auto mb-5 order-lg-first order-last position-absolute" style="top: 40%">
					<div class="d-flex flex-column mt-auto text-md-center text-sm-center text-xs-center text-lg-left" style="z-index: 110 !important;">
						<h1 class="font-weight-bold"> <?= ! empty($message) && $message !== '(null)' ? esc($message) : 'Page Not Found' ?> </h1>
						<h3 class="font-xl">The page you looking for was not reachable by our system</h3>
					</div>
					<div class="d-flex flex-row mt-5 justify-content-lg-start justify-content-md-center justify-content-sm-center justify-content-xs-center">
						<button class="btn btn-outline-dark py-2 px-5 rounded-pill mr-3" onclick="window.history.back()">Back</button>
						<button class="btn btn-danger py-2 px-5 rounded-pill mr-3" onclick="window.location.href = '<?= base_url() ?>'">Home</button>
					</div>
				</div>
				<div class="d-none d-lg-block order-lg-last order-first my-auto w-75 h-100 ml-auto" id="parallax">
			</div>
		</div>
	</div>
</body>
</html>
