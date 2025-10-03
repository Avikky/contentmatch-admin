<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.png') }}" />
  <title inertia>Access-Armpensions</title>
  <meta name="description" content="ContentMatch Admin Portal – manage creators, communities, and engagement analytics in one place.">
  <meta name="keywords" content="ContentMatch, Admin Portal, Social Media, Community Management, Analytics">
  <meta name="author" content="ACCESS-ARM Pensions Limited">
  <meta name="robots" content="index, follow">
  <meta name="googlebot" content="index, follow">
  <meta name="theme-color" content="#09518c">
  <meta property="og:title" content="ContentMatch Admin Portal">
  <meta property="og:description" content="Manage your gratuity fund with ease and security.">
  <meta property="og:image" content="{{ asset('images/og-image.png') }}">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:type" content="website">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="ContentMatch Admin Portal">
  <meta name="twitter:description" content="Manage your gratuity fund with ease and security.">
  <meta name="twitter:image" content="{{ asset('images/og-image.png') }}">
  <style>
    @font-face {
      font-family: 'EffraTrialBold';
      src: url('/fonts/Effra_Trial_Bd.ttf') format('truetype');
      font-weight: bold;
      font-style: normal;
    }

    body {
      font-family: 'EffraTrialBold', sans-serif !important;
    }

    .no-underline {
      text-decoration: none;
    }
  </style>

  <!-- Scripts -->
  @routes
  @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
  @inertiaHead
</head>

<body class="font-sans antialiased">
  @inertia
</body>

</html>