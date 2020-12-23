@php
$config = [
    'appName' => config('app.name'),
    'locale' => $locale = app()->getLocale(),
    'locales' => config('app.locales'),
    'githubAuth' => config('services.github.client_id'),
];
@endphp
{{-- <!DOCTYPE html> --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
{{-- <html lang="{{ app()->getLocale() }}"> --}}
<head>
  {{-- <meta charset="utf-8"> --}}
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  {{-- <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> --}}
  <meta name="keywords" content="SUSTipe, Human Resource Management, Payroll, Inventory Control, Inventory Management">
  <meta name="description" content="In order to cover all day-to-day HR tasks and personnel and payroll needs, you may take advantage of a Human Resource Management System (HRMS) ‒ specialized software that helps with HR activities.">
  <meta name="author" content="Muslehuddinjuned, Muslehuddin Juned, Musleh Uddin Juned, Muslehuddin, Juned">
  <meta name="copyright"content="www.sustipe.com">
  <meta name="designer" content="Musleh Uddin Juned">
  <meta name="owner" content="Musleh Uddin Juned">
  <meta name="revisit-after" content="3 days">
  <meta name="google-site-verification" content="MfB1nUyw-5DRaOaBax88VAt841RH_il1KX0rxWA4x18" />
    
  <!--Start Google AdSense-->
  <?php
    if(isset($addBlock)){}
    else { 
  ?>
  <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
   <script>
    (adsbygoogle = window.adsbygoogle || []).push({
      google_ad_client: "ca-pub-7098760579524778",
      enable_page_level_ads: true
    });
  </script>

  <?php }?>
    
  <!--End Google AdSense-->
    
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-136841507-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
  
    gtag('config', 'UA-136841507-1');
  </script>
  <!-- END Global site tag (gtag.js) - Google Analytics -->
  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Nunito&family=Piedra&family=Raleway&display=swap" rel="stylesheet">

  <title>{{ config('app.name') }}</title>

  <link rel="stylesheet" href="{{ mix('dist/css/app.css') }}">
</head>
<body>
  <div id="app"></div>

  {{-- Global configuration object --}}
  <script>
    window.config = @json($config);
  </script>

  {{-- Load the application scripts --}}
  <script src="{{ mix('dist/js/app.js') }}"></script>
</body>
</html>
