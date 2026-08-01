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
        <h3 class="font-semibold text-sm mb-4">Visits per day</h3>
        <div class="flex items-end gap-1 h-36">
            @php $max = max(1, $chart->max('total')); @endphp
            @foreach($chart as $bar)
                <div class="flex-1 flex flex-col items-center justify-end gap-1 min-w-0 h-full" title="{{ $bar['day'] }} — {{ $bar['total'] }} visits">
                    <span class="text-[9px] text-muted-foreground tabular-nums">{{ $bar['total'] ?: '' }}</span>
                    <div class="w-full rounded-t bg-primary/70 hover:bg-primary transition-colors" style="height: {{ max(2, round($bar['total'] / $max * 100)) }}%"></div>
                </div>
            @endforeach
        </div>
        <div class="flex gap-1 mt-1.5">
            @foreach($chart as $bar)
                <div class="flex-1 text-center text-[9px] text-muted-foreground min-w-0">{{ \Carbon\Carbon::parse($bar['day'])->format('d/m') }}</div>
            @endforeach
        </div>
    </x-ui.card>

    <div class="grid lg:grid-cols-3 gap-4">
        <x-ui.card padding="none" class="overflow-hidden">
            <div class="px-5 py-4 border-b border-border">
                <h3 class="font-semibold text-sm">Top pages</h3>
            </div>
            <ul class="divide-y divide-border">
                @foreach($topPages as $page)
                    <li class="px-5 py-3 flex items-center justify-between gap-3 text-sm">
                        <span class="font-medium truncate">{{ $page->path }}</span>
                        <span class="text-xs text-muted-foreground tabular-nums shrink-0">{{ $page->total }}</span>
                    </li>
                @endforeach
            </ul>
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
                <div class="px-5 py-4 border-b border-border">
                    <h3 class="font-semibold text-sm">By hour of day</h3>
                </div>
                <div class="px-5 py-4 grid grid-cols-6 gap-2">
                    @for($h = 0; $h < 24; $h++)
                        <div class="text-center">
                            <div class="text-xs tabular-nums font-medium">{{ $hourly[$h] ?? 0 }}</div>
                            <div class="text-[9px] text-muted-foreground">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}</div>
                        </div>
                    @endfor
                </div>
            </x-ui.card>

            <x-ui.card padding="none" class="overflow-hidden">
                <div class="px-5 py-4 border-b border-border">
                    <h3 class="font-semibold text-sm">Devices &amp; browsers</h3>
                </div>
                <div class="px-5 py-4 space-y-4">
                    @foreach($deviceSplit as $device)
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="capitalize text-muted-foreground">{{ $device->device }}</span>
                                <span class="tabular-nums">{{ $device->total }}</span>
                            </div>
                            <div class="h-1.5 rounded-full bg-muted overflow-hidden">
                                <div class="h-full rounded-full bg-primary" style="width: {{ $kpis['total'] ? round($device->total / max(1, $kpis['total']) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                    <div class="pt-2 border-t border-border space-y-1.5">
                        @foreach($browserSplit as $browser)
                            <div class="flex justify-between text-xs">
                                <span class="text-muted-foreground">{{ $browser->browser }}</span>
                                <span class="tabular-nums">{{ $browser->total }}</span>
                            </div>
                        @endforeach
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
