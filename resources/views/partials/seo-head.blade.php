{{--
    Partial SEO terpusat.
    Nilai yang masuk WAJIB sudah di-escape tepat satu kali oleh pemanggil
    (mis. melalui @section inline / {{ }} pada blok section), sehingga di sini
    nilai dinamis dicetak dengan {!! !!} untuk mencegah double-escape.

    Variabel opsional (semua punya default):
      $seoTitle        - judul halaman final
      $seoDescription  - meta description (fallback: deskripsi situs)
      $seoRobots       - index/noindex (fallback otomatis per-path)
      $seoCanonical    - URL kanonikal (default url()->current())
      $seoImage        - URL gambar OG/Twitter (opsional)
      $seoType         - tipe Open Graph (default "website")
--}}
@php
    $seoTitle ??= 'SIG Klinik Kecantikan Kota Jambi';
    $seoDescription ??= '';
    $seoRobots ??= '';
    $seoCanonical ??= '';
    $seoImage ??= '';
    $seoType ??= '';

    // Fallback tipe Open Graph.
    if ($seoType === '') {
        $seoType = 'website';
    }

    // Fallback deskripsi default situs.
    if ($seoDescription === '') {
        $seoDescription = 'Direktori dan peta klinik kecantikan Kota Jambi. Temukan alamat, layanan perawatan kulit dan kecantikan, harga, jam operasional, serta kontak klinik terpercaya.';
    }

    // Kebijakan robots: halaman internal/transaksional tidak diindeks.
    if ($seoRobots === '') {
        $seoRobots = (request()->is('admin*') || request()->is('login*') || request()->is('register*') || request()->is('klinik/create*'))
            ? 'noindex, follow'
            : 'index, follow';
    }

    $seoIndexable = ! str_contains($seoRobots, 'noindex');

    if ($seoCanonical === '') {
        $seoCanonical = url()->current();
    }

    $seoSiteName = 'SIG Klinik Kecantikan Kota Jambi';
@endphp

<title>{!! $seoTitle !!}</title>
<meta name="description" content="{!! $seoDescription !!}">
<meta name="robots" content="{{ $seoRobots }}">

@if($seoIndexable)
    @if($seoCanonical !== '')
    <link rel="canonical" href="{{ $seoCanonical }}">
    @endif

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $seoType }}">
    <meta property="og:site_name" content="{!! $seoSiteName !!}">
    <meta property="og:locale" content="id_ID">
    <meta property="og:title" content="{!! $seoTitle !!}">
    <meta property="og:description" content="{!! $seoDescription !!}">
    <meta property="og:url" content="{{ $seoCanonical }}">
    @if($seoImage !== '')
    <meta property="og:image" content="{!! $seoImage !!}">
    <meta property="og:image:alt" content="{!! $seoTitle !!}">
    @endif

    <!-- Twitter -->
    <meta name="twitter:card" content="{{ $seoImage !== '' ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{!! $seoTitle !!}">
    <meta name="twitter:description" content="{!! $seoDescription !!}">
    @if($seoImage !== '')
    <meta name="twitter:image" content="{!! $seoImage !!}">
    @endif
@endif

{{-- Tempat skema JSON-LD spesifik halaman (dipush dari child view) --}}
@stack('seo-jsonld')
