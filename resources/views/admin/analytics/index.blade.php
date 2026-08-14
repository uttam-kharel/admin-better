<?php

use App\Models\PageVisit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public int $days = 14;

    public bool $custom = false;
    public string $from = '';
    public string $to = '';

    public string $search = '';

    public array $kpis = [];
    public $chart;
    public $topPages;
    public $topReferrers;
    public $hourly;
    public $deviceSplit;
    public $browserSplit;

    public function mount(): void
    {
        $this->load();
    }

    public function setDays(int $days): void
    {
        $this->days = in_array($days, [7, 14, 30, 90]) ? $days : 14;
        $this->custom = false;
        $this->from = '';
        $this->to = '';
        $this->resetPage();
        $this->load();
    }

    public function applyCustomRange(): void
    {
        $this->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $this->custom = true;
        $this->days = 0;
        $this->resetPage();
        $this->load();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    protected function since(): Carbon
    {
        if ($this->custom && $this->from) {
            return Carbon::parse($this->from)->startOfDay();
        }

        return now()->subDays($this->days - 1)->startOfDay();
    }

    protected function until(): Carbon
    {
        if ($this->custom && $this->to) {
            return Carbon::parse($this->to)->startOfDay();
        }

        return now()->startOfDay();
    }

    public function exportCsv()
    {
        $since = $this->since();
        $until = $this->until();

        $rows = PageVisit::query()
            ->where('created_at', '>=', $since)
            ->where('created_at', '<', $until->copy()->addDay())
            ->orderByDesc('created_at')
            ->get();

        $filename = 'page-visits-'.$since->format('Y-m-d').'-to-'.$until->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Time', 'Path', 'Referrer', 'Device', 'Browser', 'Visitor ID', 'IP Hash', 'Type']);
            foreach ($rows as $v) {
                fputcsv($out, [
                    $v->created_at->format('Y-m-d'),
                    $v->created_at->format('H:i'),
                    $v->path,
                    $v->referer ?? '',
                    $v->device ?? '',
                    $v->browser ?? '',
                    $v->visitor_id,
                    $v->ip_hash,
                    $v->is_unique ? 'new' : 'returning',
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function load(): void
    {
        $since = $this->since();
        $until = $this->until();
        $daysInRange = abs((int) $until->diffInDays($since)) + 1;

        $base = PageVisit::query()
            ->where('created_at', '>=', $since)
            ->where('created_at', '<', $until->copy()->addDay());

        $total = (clone $base)->count();
        $unique = (clone $base)->distinct()->count('visitor_id');
        $newVisits = (clone $base)->where('is_unique', true)->count();
        $today = PageVisit::whereDate('created_at', today())->count();

        // Previous equal-length period, for % change on the KPI cards.
        $prevSince = $since->copy()->subDays($daysInRange);
        $prevBase = PageVisit::query()
            ->where('created_at', '>=', $prevSince)
            ->where('created_at', '<', $since);

        $prevTotal = (clone $prevBase)->count();
        $prevUnique = (clone $prevBase)->distinct()->count('visitor_id');

        $this->kpis = [
            'total' => $total,
            'unique' => $unique,
            'new' => $newVisits,
            'newPct' => $total > 0 ? round($newVisits / $total * 100, 1) : 0,
            'today' => $today,
            'avg' => round($total / max(1, $daysInRange), 1),
            'pagesPerVisit' => $unique > 0 ? round($total / $unique, 2) : 0,
            'days' => $daysInRange,
            'deltaTotal' => $prevTotal > 0 ? round(($total - $prevTotal) / $prevTotal * 100, 1) : null,
            'deltaUnique' => $prevUnique > 0 ? round(($unique - $prevUnique) / $prevUnique * 100, 1) : null,
            'since' => $since->format('M j, Y'),
            'until' => $until->format('M j, Y'),
        ];

        $raw = (clone $base)
            ->selectRaw("date(created_at) as day, count(*) as total, count(distinct visitor_id) as uniq")
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $chart = collect();
        $cursor = $since->copy();
        while ($cursor->lte($until)) {
            $d = $cursor->toDateString();
            $chart->push([
                'day' => $d,
                'total' => (int) ($raw[$d]->total ?? 0),
                'unique' => (int) ($raw[$d]->uniq ?? 0),
            ]);
            $cursor->addDay();
        }
        $this->chart = $chart;

        $this->topPages = (clone $base)
            ->selectRaw('path, count(*) as total, count(distinct visitor_id) as uniq')
            ->groupBy('path')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $this->topReferrers = (clone $base)
            ->selectRaw('referer, count(*) as total')
            ->whereNotNull('referer')
            ->where('referer', '!=', '')
            ->groupBy('referer')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // Hour extraction differs between SQLite (strftime) and Postgres (extract).
        $hourExpr = DB::connection()->getDriverName() === 'pgsql'
            ? "extract(hour from created_at) as hour"
            : "cast(strftime('%H', created_at) as integer) as hour";

        $this->hourly = (clone $base)
            ->selectRaw($hourExpr.', count(*) as total')
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('total', 'hour');

        $this->deviceSplit = (clone $base)
            ->selectRaw("COALESCE(device, 'unknown') as device, count(*) as total")
            ->groupBy('device')
            ->orderByDesc('total')
            ->get();

        $this->browserSplit = (clone $base)
            ->selectRaw("COALESCE(browser, 'Other') as browser, count(*) as total")
            ->groupBy('browser')
            ->orderByDesc('total')
            ->get();

    }

    public function render()
    {
        $base = PageVisit::query()
            ->where('created_at', '>=', $this->since())
            ->where('created_at', '<', $this->until()->copy()->addDay());

        $q = trim($this->search);
        if ($q !== '') {
            $base->where(function ($w) use ($q) {
                $w->where('path', 'like', "%{$q}%")
                    ->orWhere('referer', 'like', "%{$q}%")
                    ->orWhere('browser', 'like', "%{$q}%")
                    ->orWhere('device', 'like', "%{$q}%");
            });
        }

        return $this->view([
            'recentVisits' => $base->latest()->paginate(20),
        ])->layout('layouts.admin', ['title' => 'Analytics — Admin']);
    }
};

?>
<div class="space-y-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">Analytics</h2>
            <p class="text-sm text-muted-foreground mt-1">Page visits, unique visitors and traffic sources.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="inline-flex rounded-lg bg-surface border border-border p-1 text-sm">
                @foreach([7, 14, 30, 90] as $range)
                    <button wire:click="setDays({{ $range }})"
                        class="px-3 py-1.5 rounded-md font-medium transition-colors {{ ! $custom && $days === $range ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground' }}">
                        {{ $range }}d
                    </button>
                @endforeach
            </div>

            <form wire:submit="applyCustomRange" class="flex items-center gap-2 rounded-lg bg-surface border border-border px-2 py-1">
                <span class="text-xs text-muted-foreground pl-1">Custom</span>
                <input type="date" wire:model="from" class="bg-transparent text-xs text-foreground focus:outline-none [color-scheme:light]" />
                <span class="text-xs text-muted-foreground">–</span>
                <input type="date" wire:model="to" class="bg-transparent text-xs text-foreground focus:outline-none [color-scheme:light]" />
                <button type="submit" class="px-2 py-1 rounded-md text-xs font-semibold text-primary hover:bg-primary/10">Apply</button>
            </form>

            <x-ui.button wire:click="exportCsv" variant="outline" size="sm">
                @svg('lucide-download', 'h-3.5 w-3.5')
                CSV
            </x-ui.button>
        </div>
    </div>

    @if($kpis['total'] === 0)
        <x-ui.card variant="muted">
            <p class="text-sm text-muted-foreground">
                No visits recorded {{ $custom ? 'in this range' : 'yet' }} ({{ $kpis['since'] }} – {{ $kpis['until'] }}).
                The tracker records every public page view — check back after some traffic.
            </p>
        </x-ui.card>
    @endif

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <x-ui.stat-card :value="number_format($kpis['total'])" :label="'Visits (' . $kpis['days'] . 'd)'" :delta="$kpis['deltaTotal']">
            <x-slot:icon>@svg('lucide-eye')</x-slot:icon>
        </x-ui.stat-card>
        <x-ui.stat-card :value="number_format($kpis['unique'])" label="Unique visitors" :delta="$kpis['deltaUnique']">
            <x-slot:icon>@svg('lucide-users')</x-slot:icon>
        </x-ui.stat-card>
        <x-ui.stat-card :value="number_format($kpis['new'])" :label="'New visitors (' . $kpis['newPct'] . '% of all)'">
            <x-slot:icon>@svg('lucide-user-plus')</x-slot:icon>
        </x-ui.stat-card>
        <x-ui.stat-card :value="number_format($kpis['today'])" label="Visits today">
            <x-slot:icon>@svg('lucide-calendar-days')</x-slot:icon>
        </x-ui.stat-card>
    </div>

    <x-ui.card>
        <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <div>
                <h3 class="font-semibold text-sm">Visits &amp; unique visitors per day</h3>
                <p class="text-xs text-muted-foreground mt-0.5">{{ $kpis['since'] }} – {{ $kpis['until'] }}</p>
            </div>
            <span class="text-xs text-muted-foreground">Line chart</span>
        </div>
        <div
            x-data="{
                labels: @js($chart->pluck('day')->map(fn ($b) => \Carbon\Carbon::parse($b)->format('d M'))->values()),
                visits: @js($chart->pluck('total')->values()),
                uniques: @js($chart->pluck('unique')->values()),
                init() {
                    AdminCharts.renderMultiLineChart(this.$refs.canvas, this.labels, [
                        { label: 'Visits', data: this.visits },
                        { label: 'Unique', data: this.uniques },
                    ]);
                },
                destroy() { AdminCharts.destroyChart(this.$refs.canvas); },
            }"
            wire:key="ana-daily-{{ $custom ? 'custom-' . $from . '-' . $to : $days }}"
        >
            <div class="h-64">
                <canvas x-ref="canvas"></canvas>
            </div>
        </div>
    </x-ui.card>

    <div class="grid lg:grid-cols-3 gap-4">
        <x-ui.card padding="none" class="overflow-hidden">
            <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                <h3 class="font-semibold text-sm">Top pages</h3>
                <span class="text-xs text-muted-foreground">By visits</span>
            </div>
            @if($topPages->count() === 0)
                <p class="p-6 text-sm text-muted-foreground">No visits recorded.</p>
            @else
                <ul class="divide-y divide-border">
                    @foreach($topPages as $i => $page)
                        @php $pct = $kpis['total'] > 0 ? round($page->total / $kpis['total'] * 100, 1) : 0; @endphp
                        <li class="px-5 py-3">
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <span class="font-medium truncate">{{ $page->path }}</span>
                                <span class="text-xs text-muted-foreground tabular-nums shrink-0">{{ number_format($page->total) }} <span class="text-muted-foreground/50">({{ $page->uniq }} unique)</span></span>
                            </div>
                            <div class="mt-2 h-1.5 rounded-full bg-muted overflow-hidden">
                                <div class="h-full rounded-full bg-primary" style="width: {{ max(2, $pct) }}%"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>

        <x-ui.card padding="none" class="overflow-hidden">
            <div class="px-5 py-4 border-b border-border">
                <h3 class="font-semibold text-sm">Top referrers</h3>
            </div>
            @if($topReferrers->count() === 0)
                <p class="p-6 text-sm text-muted-foreground">No external referrers.</p>
            @else
                <ul class="divide-y divide-border">
                    @foreach($topReferrers as $ref)
                        <li class="px-5 py-3 flex items-center justify-between gap-3 text-sm">
                            <span class="font-medium truncate">{{ parse_url($ref->referer, PHP_URL_HOST) ?: $ref->referer }}</span>
                            <span class="text-xs text-muted-foreground tabular-nums shrink-0">{{ number_format($ref->total) }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>

        <div class="space-y-4">
            <x-ui.card padding="none" class="overflow-hidden">
                <div class="px-5 py-4 border-b border-border">
                    <h3 class="font-semibold text-sm">New vs returning</h3>
                </div>
                <div class="px-5 py-4">
                    <div
                        x-data="{
                            labels: @js(['New', 'Returning']),
                            values: @js([$kpis['new'], max(0, $kpis['total'] - $kpis['new'])]),
                            init() { AdminCharts.renderDoughnut(this.$refs.canvas, this.labels, this.values, { colors: ['#0F6CBD', '#14B8A6'] }); },
                            destroy() { AdminCharts.destroyChart(this.$refs.canvas); },
                        }"
                        wire:key="ana-newreturn-{{ $custom ? 'custom' : $days }}"
                    >
                        <div class="h-44">
                            <canvas x-ref="canvas"></canvas>
                        </div>
                    </div>
                    <div class="pt-3 mt-3 border-t border-border grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <p class="text-muted-foreground">Pages / visit</p>
                            <p class="text-lg font-bold tabular-nums mt-0.5">{{ $kpis['pagesPerVisit'] }}</p>
                        </div>
                        <div>
                            <p class="text-muted-foreground">Avg visits / day</p>
                            <p class="text-lg font-bold tabular-nums mt-0.5">{{ $kpis['avg'] }}</p>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-4">
        <x-ui.card padding="none" class="overflow-hidden">
            <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                <h3 class="font-semibold text-sm">By hour of day</h3>
                <span class="text-xs text-muted-foreground">Nepal time</span>
            </div>
            <div class="px-5 py-4">
                <div
                    x-data="{
                        labels: @js(collect(range(0, 23))->map(fn ($h) => str_pad($h, 2, '0', STR_PAD_LEFT))->values()),
                        values: @js(collect(range(0, 23))->map(fn ($h) => $hourly[$h] ?? 0)->values()),
                        init() { AdminCharts.renderBarChart(this.$refs.canvas, this.labels, this.values, { label: 'Visits', color: '#14B8A6' }); },
                        destroy() { AdminCharts.destroyChart(this.$refs.canvas); },
                    }"
                    wire:key="ana-hourly-{{ $custom ? 'custom' : $days }}"
                >
                    <div class="h-40">
                        <canvas x-ref="canvas"></canvas>
                    </div>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card padding="none" class="overflow-hidden">
            <div class="px-5 py-4 border-b border-border">
                <h3 class="font-semibold text-sm">Devices</h3>
            </div>
            <div class="px-5 py-4">
                <div
                    x-data="{
                        labels: @js($deviceSplit->pluck('device')->map(fn ($d) => ucfirst($d))->values()),
                        values: @js($deviceSplit->pluck('total')->values()),
                        init() { AdminCharts.renderDoughnut(this.$refs.canvas, this.labels, this.values); },
                        destroy() { AdminCharts.destroyChart(this.$refs.canvas); },
                    }"
                    wire:key="ana-devices-{{ $custom ? 'custom' : $days }}"
                >
                    <div class="h-44">
                        <canvas x-ref="canvas"></canvas>
                    </div>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card padding="none" class="overflow-hidden">
            <div class="px-5 py-4 border-b border-border">
                <h3 class="font-semibold text-sm">Browsers</h3>
            </div>
            <div class="px-5 py-4">
                <div
                    x-data="{
                        labels: @js($browserSplit->pluck('browser')->values()),
                        values: @js($browserSplit->pluck('total')->values()),
                        init() { AdminCharts.renderDoughnut(this.$refs.canvas, this.labels, this.values, { colors: ['#14B8A6', '#EAB308', '#DC2626', '#16A34A', '#8B5CF6', '#F97316'] }); },
                        destroy() { AdminCharts.destroyChart(this.$refs.canvas); },
                    }"
                    wire:key="ana-browsers-{{ $custom ? 'custom' : $days }}"
                >
                    <div class="h-44">
                        <canvas x-ref="canvas"></canvas>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>

    <x-ui.card padding="none" class="overflow-hidden">
        <div class="px-5 py-4 border-b border-border flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="font-semibold text-sm">All visits</h3>
                <p class="text-xs text-muted-foreground mt-0.5">Times in Asia/Kathmandu (UTC+5:45)</p>
            </div>
            <input
                type="search"
                wire:model.live.debounce.300ms="search"
                placeholder="Search page, referrer, browser…"
                class="bg-muted/50 border border-border rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-primary/40"
            />
        </div>
        @if($recentVisits->count() === 0)
            <p class="p-6 text-sm text-muted-foreground">No visits match this range or search.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-[10px] uppercase tracking-widest text-muted-foreground border-b border-border">
                            <th class="px-5 py-2.5 font-semibold">Page</th>
                            <th class="px-5 py-2.5 font-semibold">Referrer</th>
                            <th class="px-5 py-2.5 font-semibold">Device</th>
                            <th class="px-5 py-2.5 font-semibold">Browser</th>
                            <th class="px-5 py-2.5 font-semibold">Type</th>
                            <th class="px-5 py-2.5 font-semibold">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach($recentVisits as $visit)
                            <tr>
                                <td class="px-5 py-3 font-medium truncate max-w-[200px]">{{ $visit->path }}</td>
                                <td class="px-5 py-3 text-xs text-muted-foreground truncate max-w-[180px]">
                                    @if($visit->referer)
                                        {{ parse_url($visit->referer, PHP_URL_HOST) }}
                                    @else
                                        <span class="text-muted-foreground/50">Direct</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-xs text-muted-foreground capitalize">{{ $visit->device ?? '—' }}</td>
                                <td class="px-5 py-3 text-xs text-muted-foreground">{{ $visit->browser ?? '—' }}</td>
                                <td class="px-5 py-3">
                                    @if($visit->is_unique)
                                        <span class="text-[10px] uppercase tracking-widest font-semibold px-2 py-0.5 rounded bg-primary/10 text-primary">new</span>
                                    @else
                                        <span class="text-[10px] uppercase tracking-widest font-semibold px-2 py-0.5 rounded bg-muted text-muted-foreground">returning</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-xs text-muted-foreground tabular-nums whitespace-nowrap">{{ \Carbon\Carbon::parse($visit->created_at)->format('M j, H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3 border-t border-border">
                {{ $recentVisits->links() }}
            </div>
        @endif
    </x-ui.card>
</div>
