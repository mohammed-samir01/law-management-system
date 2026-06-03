<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    @php
        $baseUrl = $office->custom_domain && $office->domain_verified_at
            ? 'https://' . $office->custom_domain
            : url('/offices/' . $office->slug);
    @endphp

    <url>
        <loc>{{ $baseUrl }}</loc>
        <lastmod>{{ $office->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>1.0</priority>
    </url>

</urlset>
