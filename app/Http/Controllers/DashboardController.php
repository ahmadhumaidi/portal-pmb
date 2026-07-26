<?php

namespace App\Http\Controllers;

use App\Models\Berkas;
use App\Models\Kampus;
use App\Models\Mahasiswa;
use App\Models\Pembayaran;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalMahasiswa = Mahasiswa::count();
        $totalKampus = Kampus::where('status_aktif', true)->count();
        $totalBerkas = Berkas::count();

        $totalPembayaran = (float) Pembayaran::where('status_bayar', 'terverifikasi')->sum('nominal');

        $berkasByStatus = Berkas::query()
            ->selectRaw('status_verifikasi, count(*) as jumlah')
            ->groupBy('status_verifikasi')
            ->pluck('jumlah', 'status_verifikasi');

        $berkasLengkap = (int) $berkasByStatus->get('lengkap', 0);
        $berkasBelumUpload = (int) $berkasByStatus->get('belum_upload', 0);
        $berkasBelumLengkap = $totalBerkas - $berkasLengkap - $berkasBelumUpload;

        return view('dashboard.index', [
            'totalMahasiswa' => $totalMahasiswa,
            'mahasiswaChange' => $this->monthOverMonthChange(Mahasiswa::class),
            'totalPembayaran' => $totalPembayaran,
            'pembayaranChange' => $this->monthOverMonthChange(Pembayaran::class, 'nominal', fn ($q) => $q->where('status_bayar', 'terverifikasi')),
            'totalKampus' => $totalKampus,
            'totalBerkas' => $totalBerkas,
            'berkasLengkap' => $berkasLengkap,
            'berkasBelumLengkap' => max($berkasBelumLengkap, 0),
            'berkasBelumUpload' => $berkasBelumUpload,
            'chartData' => [
                'harian' => $this->dailyRegistrations(7),
                'bulanan30' => $this->dailyRegistrations(30),
                'tahunan' => $this->monthlyRegistrations(),
            ],
        ]);
    }

    private function monthOverMonthChange(string $model, string $column = 'id', ?\Closure $scope = null): string
    {
        $startOfThisMonth = now()->startOfMonth();
        $startOfLastMonth = now()->subMonthNoOverflow()->startOfMonth();

        $query = fn () => $scope ? $scope($model::query()) : $model::query();

        $aggregate = fn ($query) => $column === 'id' ? $query->count() : (float) $query->sum($column);

        $thisMonth = $aggregate($query()->where('created_at', '>=', $startOfThisMonth));
        $lastMonth = $aggregate($query()->whereBetween('created_at', [$startOfLastMonth, $startOfThisMonth]));

        if ($lastMonth <= 0) {
            $percent = $thisMonth > 0 ? 100 : 0;
        } else {
            $percent = round((($thisMonth - $lastMonth) / $lastMonth) * 100);
        }

        $sign = $percent > 0 ? '+' : '';

        return "{$sign}{$percent}% bulan ini";
    }

    private function dailyRegistrations(int $days): array
    {
        $since = now()->subDays($days - 1)->startOfDay();

        $countsByDate = Mahasiswa::query()
            ->where('created_at', '>=', $since)
            ->pluck('created_at')
            ->groupBy(fn (Carbon $date) => $date->format('Y-m-d'))
            ->map->count();

        return collect(range($days - 1, 0))
            ->map(function (int $daysAgo) use ($countsByDate) {
                $date = now()->subDays($daysAgo);

                return [
                    'label' => $date->translatedFormat('d M'),
                    'value' => (int) $countsByDate->get($date->format('Y-m-d'), 0),
                ];
            })
            ->values()
            ->all();
    }

    private function monthlyRegistrations(): array
    {
        $countsByMonth = Mahasiswa::query()
            ->whereYear('created_at', now()->year)
            ->pluck('created_at')
            ->groupBy(fn (Carbon $date) => $date->format('n'))
            ->map->count();

        return collect(range(1, 12))
            ->map(function (int $month) use ($countsByMonth) {
                return [
                    'label' => Carbon::create(now()->year, $month, 1)->translatedFormat('M'),
                    'value' => (int) $countsByMonth->get((string) $month, 0),
                ];
            })
            ->values()
            ->all();
    }
}
