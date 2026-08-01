<?php

use App\Models\PageVisit;
use Livewire\Component;


new class extends Component
{
    public int $days = 14;

    public array $kpis = [];
    public $chart;
    public $topPages;
    public $topReferrers;
    public $hourly;
    public $deviceSplit;
    public $browserSplit;
    public $recentVisits;

    public function mount(): void
    {
        $this->load();
    }

    public function setDays(int $days): void
    {
        $this->days = in_array($days, [7, 14, 30, 90]) ? $days : 14;
        $this->load();
    }

    public function load(): void
    {
        $since = now()->subDays($this->days - 1)->startOfDay();

        $base = PageVisit::query()->where('created_at', '>=', $since);

        $this->kpis = [
            'total' => (clone $base)->count(),
            'unique' => (clone $base)->distinct()->count('visitor_id'),
            'today' => PageVisit::whereDate('created_at', today())->count(),
            'avg' => round((clone $base)->count() / max(1, $this->days), 1),
        ];

        $raw = (clone $base)
            ->selectRaw("date(created_at) as day, count(*) as total")
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $chart = collect();
        for ($i = $this->days - 1; $i >= 0; $i--) {
            $day = now()->subDays($i)->toDateString();
            $chart->push(['day' => $day, 'total' => (int) ($raw[$day] ?? 0)]);
        }
        $this->chart = $chart;

        $this->topPages = (clone $base)
            ->selectRaw('path, count(*) as total')
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

        $this->hourly = (clone $base)
            ->selectRaw("cast(strftime('%H', created_at) as integer) as hour, count(*) as total")
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('total', 'hour');

        $this->deviceSplit = (clone $base)
            ->selectRaw('COALESCE(device, "unknown") as device, count(*) as total')
            ->groupBy('device')
            ->orderByDesc('total')
            ->get();

        $this->browserSplit = (clone $base)
            ->selectRaw('COALESCE(browser, "Other") as browser, count(*) as total')
            ->groupBy('browser')
            ->orderByDesc('total')
            ->get();

        $this->recentVisits = (clone $base)->latest()->take(50)->get();
    }

    public function render()
    {
        return $this->view()
            ->layout('layouts.admin', ['title' => 'Analytics — Admin']);
    }
};

?>
<div class="space-y-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold tracking-tight">Analytics</h2>
            <p class="text-sm text-muted-foreground mt-1">Page visits, unique visitors and traffic sources.</p>
        </div>

        <div class="inline-flex rounded-lg bg-surface border border-border p-1 text-sm">
            @foreach([7, 14, 30, 90] as $range)
                <button wire:click="setDays({{ $range }})"
                    class="px-3 py-1.5 rounded-md font-medium transition-colors {{ $days === $range ? 'bg-primary text-primary-foreground' : 'text-muted-foreground hover:text-foreground' }}">
                    {{ $range }}d
                </button>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
        <x-ui.stat-card :value="number_format($kpis['total'])" :label="'Visits (' . $days . 'd)'">
            <x-slot:icon>@svg('lucide-eye')</x-slot:icon>
        </x-ui.stat-card>
        <x-ui.stat-card :value="number_format($kpis['unique'])" :label="'Unique visitors (' . $days . 'd)'">
            <x-slot:icon>@svg('lucide-users')</x-slot:icon>
        </x-ui.stat-card>
        <x-ui.stat-card :value="number_format($kpis['today'])" label="Visits today">
            <x-slot:icon>@svg('lucide-calendar-days')</x-slot:icon>
        </x-ui.stat-card>
        <x-ui.stat-card :value="number_format($kpis['avg'])" :label="'Avg / day (' . $days . 'd)'">
            <x-slot:icon>@svg('lucide-trending-up')</x-slot:icon>
        </x-ui.stat-card>
    </div>

    <x-ui.card>
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-sm">Visits per day</h3>
            <span class="text-xs text-muted-foreground">Line chart</span>
        </div>
        <div
            x-data="{
                labels: @js($chart->pluck('day')->map(fn ($b) => \Carbon\Carbon::parse($b)->format('d M'))->values()),
                values: @js($chart->pluck('total')->values()),
                init() { AdminCharts.renderLineChart(this.$refs.canvas, this.labels, this.values); },
                destroy() { AdminCharts.destroyChart(this.$refs.canvas); },
            }"
            wire:key="ana-daily-{{ $days }}"
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
                <span class="text-xs text-muted-foreground">Bar chart</span>
            </div>
            <div class="px-5 py-4">
                <div
                    x-data="{
                        labels: @js($topPages->pluck('path')->values()),
                        values: @js($topPages->pluck('total')->values()),
                        init() { AdminCharts.renderBarChart(this.$refs.canvas, this.labels, this.values, { label: 'Visits', horizontal: true }); },
                        destroy() { AdminCharts.destroyChart(this.$refs.canvas); },
                    }"
                    wire:key="ana-pages-{{ $days }}"
                >
                    <div class="h-56">
                        <canvas x-ref="canvas"></canvas>
                    </div>
                </div>
            </div>
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
                            <span class="text-xs text-muted-foreground tabular-nums shrink-0">{{ $ref->total }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>

        <div class="space-y-4">
        <x-ui.card padding="none" class="overflow-hidden">
            <div class="px-5 py-4 border-b border-border flex items-center justify-between">
                <h3 class="font-semibold text-sm">By hour of day</h3>
                <span class="text-xs text-muted-foreground">Bar chart</span>
            </div>
            <div class="px-5 py-4">
                <div
                    x-data="{
                        labels: @js(collect(range(0, 23))->map(fn ($h) => str_pad($h, 2, '0', STR_PAD_LEFT))->values()),
                        values: @js(collect(range(0, 23))->map(fn ($h) => $hourly[$h] ?? 0)->values()),
                        init() { AdminCharts.renderBarChart(this.$refs.canvas, this.labels, this.values, { label: 'Visits', color: '#14B8A6' }); },
                        destroy() { AdminCharts.destroyChart(this.$refs.canvas); },
                    }"
                    wire:key="ana-hourly-{{ $days }}"
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
                        wire:key="ana-devices-{{ $days }}"
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
                        wire:key="ana-browsers-{{ $days }}"
                    >
                        <div class="h-44">
                            <canvas x-ref="canvas"></canvas>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>

    <x-ui.card padding="none" class="overflow-hidden">
        <div class="px-5 py-4 border-b border-border">
            <h3 class="font-semibold text-sm">All visits (latest {{ $recentVisits->count() }})</h3>
        </div>
        @if($recentVisits->count() === 0)
            <p class="p-6 text-sm text-muted-foreground">No visits recorded yet.</p>
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
                                        <span class="text-[10px] uppercase tracking-widest font-semibold px-2 py-0.5 rounded bg-primary/10 text-primary">unique</span>
                                    @else
                                        <span class="text-[10px] uppercase tracking-widest font-semibold px-2 py-0.5 rounded bg-muted text-muted-foreground">return</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-xs text-muted-foreground tabular-nums whitespace-nowrap">{{ \Carbon\Carbon::parse($visit->created_at)->format('M j, H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-ui.card>
</div>
