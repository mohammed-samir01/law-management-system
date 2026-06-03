<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    {{-- Main platform pages --}}
    <sitemap>
        <loc>{{ url('/sitemap-pages.xml') }}</loc>
    </sitemap>

    {{-- One sitemap per active office --}}
    @foreach($offices as $office)
    <sitemap>
        <loc>{{ url('/offices/' . $office->slug . '/sitemap.xml') }}</loc>
        <lastmod>{{ $office->updated_at->toAtomString() }}</lastmod>
    </sitemap>
    @endforeach

</sitemapindex>
