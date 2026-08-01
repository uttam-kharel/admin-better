<?php

namespace App\Http\Middleware;

use App\Models\PageVisit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackPageVisits
{
    /**
     * Path prefixes that should never be counted as page views.
     */
    protected array $excludedPrefixes = [
        'admin',
        'livewire',
        'build',
        'storage',
        'vendor',
        'api',
        'horizon',
        'telescope',
        'up',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only count successful page views (skip 404/500 noise).
        if ($response->getStatusCode() >= 400) {
            return $response;
        }

        if (! $this->shouldTrack($request)) {
            return $response;
        }

        try {
            $this->record($request);
        } catch (\Throwable $e) {
            // Analytics must never break the site.
        }

        return $response;
    }

    protected function shouldTrack(Request $request): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        // Skip Livewire internal updates / navigations (they are not real page views).
        if ($request->header('X-Livewire')) {
            return false;
        }

        // Skip favicon / robots / sitemap noise.
        $path = '/'.ltrim($request->path(), '/');
        if (in_array($path, ['/favicon.ico', '/robots.txt', '/sitemap.xml', '/.well-known/vercel/microfrontend'])) {
            return false;
        }

        foreach ($this->excludedPrefixes as $prefix) {
            if ($path === '/'.$prefix || str_starts_with($path, '/'.$prefix.'/')) {
                return false;
            }
        }

        return true;
    }

    protected function record(Request $request): void
    {
        $visitorId = $this->visitorId($request);
        $isUnique = ! PageVisit::query()->where('visitor_id', $visitorId)->exists();

        PageVisit::create([
            'path' => '/'.ltrim($request->path(), '/'),
            'query' => $request->getQueryString(),
            'full_url' => $request->fullUrl(),
            'referer' => $this->cleanReferer($request->headers->get('referer')),
            'visitor_id' => $visitorId,
            'ip_hash' => $this->hashIp($request),
            'user_agent' => Str::limit((string) $request->userAgent(), 500),
            'device' => $this->deviceType($request->userAgent()),
            'browser' => $this->browserName($request->userAgent()),
            'is_unique' => $isUnique,
        ]);

        $this->setVisitorCookie($request, $visitorId);
    }

    protected function visitorId(Request $request): string
    {
        // Reuse the stable per-visitor cookie when present.
        $existing = $request->cookie('visitor_id');
        if (is_string($existing) && strlen($existing) >= 16) {
            return Str::limit($existing, 64, '');
        }

        // Otherwise derive a stable id from the client IP (+ UA as salt),
        // so returning visitors without cookies still count as the same visitor.
        return substr(hash('sha256', $this->clientIp($request).'|'.$request->userAgent()), 0, 40);
    }

    protected function setVisitorCookie(Request $request, string $visitorId): void
    {
        if ($request->cookies->has('visitor_id')) {
            return;
        }

        // Best-effort: queue the cookie so repeat visits reuse the same id.
        cookie()->queue(cookie('visitor_id', $visitorId, 60 * 24 * 365, '/', null, $request->isSecure(), true, false, 'lax'));
    }

    protected function hashIp(Request $request): string
    {
        return substr(hash('sha256', $this->clientIp($request)), 0, 40);
    }

    protected function clientIp(Request $request): string
    {
        // Vercel exposes the real client IP via X-Forwarded-For.
        $forwarded = $request->header('X-Forwarded-For');
        if (is_string($forwarded)) {
            $first = explode(',', $forwarded)[0] ?? '';
            if (filter_var(trim($first), FILTER_VALIDATE_IP)) {
                return trim($first);
            }
        }

        return (string) $request->ip();
    }

    protected function cleanReferer(?string $referer): ?string
    {
        if (! $referer) {
            return null;
        }

        $host = parse_url($referer, PHP_URL_HOST);
        if (! $host) {
            return null;
        }

        // Normalise to scheme://host/path (no query/fragment) for grouping.
        $scheme = parse_url($referer, PHP_URL_SCHEME) ?: 'http';

        return $scheme.'://'.$host.(parse_url($referer, PHP_URL_PATH) ?: '');
    }

    protected function deviceType(?string $ua): string
    {
        $ua = strtolower((string) $ua);

        if (str_contains($ua, 'ipad') || str_contains($ua, 'tablet') || str_contains($ua, 'kindle')) {
            return 'tablet';
        }

        if (preg_match('/mobile|android|iphone|ipod|opera mini|iemobile|wpdesktop/i', $ua)) {
            return 'mobile';
        }

        return 'desktop';
    }

    protected function browserName(?string $ua): string
    {
        $ua = strtolower((string) $ua);

        $map = [
            'edg/' => 'Edge',
            'opr/' => 'Opera',
            'chrome/' => 'Chrome',
            'firefox/' => 'Firefox',
            'safari/' => 'Safari',
            'trident/' => 'IE',
        ];

        foreach ($map as $needle => $name) {
            if (str_contains($ua, $needle)) {
                return $name;
            }
        }

        return 'Other';
    }
}
