@if(($pengumumans ?? collect())->isNotEmpty())
    <div class="portal-card rounded-2xl bg-white p-6 mb-6">
        <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Pengumuman</p>
        <div class="mt-3 divide-y divide-amber-100 overflow-hidden rounded-xl border border-amber-100 bg-amber-50">
            @foreach($pengumumans as $pengumuman)
                <button type="button"
                    class="js-pengumuman-open flex w-full items-center justify-between gap-4 px-4 py-3 text-left font-black text-slate-900 hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-emerald-200"
                    data-pengumuman-target="pengumuman-modal-{{ $pengumuman->id }}">
                    <span>{{ $pengumuman->judul }}</span>
                    <span class="text-xs font-bold uppercase tracking-wide text-emerald-700">Buka</span>
                </button>
            @endforeach
        </div>
    </div>

    @foreach($pengumumans as $pengumuman)
        <div id="pengumuman-modal-{{ $pengumuman->id }}"
            class="js-pengumuman-modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 px-4 py-8">
            <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-2xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Pengumuman</p>
                        <h2 class="mt-1 text-xl font-black text-slate-900">{{ $pengumuman->judul }}</h2>
                        <p class="mt-1 text-xs text-slate-400">{{ ($pengumuman->tanggal ?? $pengumuman->created_at)->translatedFormat('d M Y') }}</p>
                    </div>
                    <button type="button"
                        class="js-pengumuman-close rounded-lg bg-slate-100 px-3 py-2 text-sm font-black text-slate-600 hover:bg-slate-200"
                        aria-label="Tutup pengumuman">
                        Tutup
                    </button>
                </div>

                <div class="mt-5 max-h-[60vh] overflow-y-auto whitespace-pre-line text-sm leading-6 text-slate-700">{{ $pengumuman->isi }}</div>

                @if($pengumuman->lampiran_path)
                    <a href="{{ route('portal.pengumuman.lampiran', $pengumuman) }}" target="_blank"
                        class="mt-5 inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-black text-white hover:bg-emerald-700">
                        Lihat Lampiran
                    </a>
                @endif
            </div>
        </div>
    @endforeach

    <script>
        document.addEventListener('click', function (event) {
            const openButton = event.target.closest('.js-pengumuman-open');
            if (openButton) {
                const modal = document.getElementById(openButton.dataset.pengumumanTarget);
                modal?.classList.remove('hidden');
                modal?.classList.add('flex');
                return;
            }

            if (event.target.matches('.js-pengumuman-modal') || event.target.closest('.js-pengumuman-close')) {
                const modal = event.target.closest('.js-pengumuman-modal');
                modal?.classList.add('hidden');
                modal?.classList.remove('flex');
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') {
                return;
            }

            document.querySelectorAll('.js-pengumuman-modal').forEach(function (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });
        });
    </script>
@endif
