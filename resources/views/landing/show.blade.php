<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $content['subheadline'] }}">
    <title>Portal Mahasiswa Baru</title>
    @vite(['resources/css/app.css', 'resources/css/landing.css', 'resources/js/app.js'])
</head>
<body class="landing-page">
    <nav class="landing-nav">
        <div class="landing-shell landing-nav-inner">
            <a class="landing-brand" href="{{ route('landing') }}">
                <span class="landing-logo"><i class="bi bi-mortarboard-fill"></i></span>
                Portal Mahasiswa
            </a>
            <div class="landing-links">
                <a href="#program">Keunggulan</a>
                <a href="#alur">Alur Daftar</a>
                <a href="#faq">FAQ</a>
                <a href="{{ route('portal.mahasiswa.login') }}">Login Mahasiswa</a>
                <a href="{{ route('portal.koordinator.login') }}">Login Koordinator</a>
                <a class="landing-login" href="{{ route('login') }}">Masuk Portal</a>
            </div>
        </div>
    </nav>

    <main>
        <section class="landing-hero">
            <div class="landing-shell hero-grid">
                <div>
                    <div class="hero-system-label"><span></span> Digital Admission System</div>
                    @if($content['badge'])<div class="hero-badge">{{ $content['badge'] }}</div>@endif
                    <h1>{!! str_replace('masa depan', '<span>masa depan</span>', e($content['headline'])) !!}</h1>
                    <p class="hero-copy">{{ $content['subheadline'] }}</p>
                    <div class="hero-actions">
                        @if($content['primary_button_text'])
                            <a class="lp-button lp-button-primary js-open-registration" href="#form-pendaftaran">
                                {{ $content['primary_button_text'] }} <i class="bi bi-arrow-up-right"></i>
                            </a>
                        @endif
                        @if($content['secondary_button_text'])
                            <a class="lp-button lp-button-secondary" href="{{ $content['secondary_button_url'] ?: '#program' }}">
                                {{ $content['secondary_button_text'] }}
                            </a>
                        @endif
                    </div>
                </div>
                <div class="hero-visual" aria-hidden="true">
                    <div class="tech-orbit tech-orbit-one"></div>
                    <div class="tech-orbit tech-orbit-two"></div>
                    <div class="hero-shape"></div>
                    <div class="hero-pattern"></div>
                    <div class="hero-mark"><i class="bi bi-mortarboard"></i></div>
                    <div class="hero-card">
                        <div class="hero-card-label">PMB / 2026</div>
                        <div class="status-line"><span class="status-dot"></span>{{ $content['registration_status'] }}</div>
                        <small>Batas pendaftaran</small>
                        <strong>{{ $content['registration_deadline'] ? \Illuminate\Support\Carbon::parse($content['registration_deadline'])->translatedFormat('d F Y') : 'Informasi menyusul' }}</strong>
                    </div>
                </div>
            </div>
            <div class="hero-wave" aria-hidden="true"><span></span></div>
        </section>

        @if($content['announcement'])
            <div class="announcement">
                <div class="landing-shell announcement-inner"><i class="bi bi-megaphone-fill"></i>{{ $content['announcement'] }}</div>
            </div>
        @endif

        <section class="landing-section" id="program">
            <div class="landing-shell">
                <div class="section-kicker">Kenapa memilih kami</div>
                <h2 class="section-heading">Pendaftaran kuliah yang terasa lebih sederhana.</h2>
                <p class="section-copy">Semua yang kamu butuhkan untuk memulai perjalanan pendidikan tersedia dalam satu portal.</p>
                <div class="feature-grid">
                    @foreach($content['features'] as $feature)
                        <article class="feature-card">
                            <div class="feature-icon"><i class="bi {{ $feature['icon'] ?? 'bi-stars' }}"></i></div>
                            <h3>{{ $feature['title'] }}</h3>
                            <p>{{ $feature['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="landing-section steps-section" id="alur">
            <div class="landing-shell">
                <div class="section-kicker">Alur pendaftaran</div>
                <h2 class="section-heading">Empat langkah menuju kampus impian.</h2>
                <div class="steps-grid">
                    @foreach($content['steps'] as $step)
                        <article class="step-card">
                            <div class="step-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                            <h3>{{ $step['title'] }}</h3>
                            <p>{{ $step['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="landing-section" id="faq">
            <div class="landing-shell faq-layout">
                <div>
                    <div class="section-kicker">Pertanyaan umum</div>
                    <h2 class="section-heading">Masih ada yang ingin ditanyakan?</h2>
                    <p class="section-copy">Temukan jawaban singkat atau hubungi tim admisi kami.</p>
                </div>
                <div class="faq-list">
                    @foreach($content['faqs'] as $faq)
                        <details class="faq-item" @if($loop->first) open @endif>
                            <summary class="faq-question">{{ $faq['question'] }} <i class="bi bi-plus-lg"></i></summary>
                            <div class="faq-answer">{{ $faq['answer'] }}</div>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="landing-section">
            <div class="landing-shell">
                <div class="cta-card">
                    <div>
                        <h2>Siap menulis cerita barumu?</h2>
                        <p>Mulai pendaftaran hari ini. Tim kami siap mendampingimu.</p>
                    </div>
                    <a class="lp-button js-open-registration" href="#form-pendaftaran">Daftar sekarang <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </section>
    </main>

    <div class="registration-modal" id="form-pendaftaran" aria-hidden="true">
        <div class="registration-backdrop js-close-registration"></div>
        <section class="registration-dialog" role="dialog" aria-modal="true" aria-labelledby="registration-title">
            <button class="registration-close js-close-registration" type="button" aria-label="Tutup formulir"><i class="bi bi-x-lg"></i></button>
            <div class="registration-intro">
                <div class="section-kicker">Pendaftaran awal</div>
                <h2 id="registration-title">Mulai langkah pertamamu.</h2>
                <p>Isi data berikut. Tim admisi akan menghubungi kamu untuk melanjutkan proses pendaftaran.</p>
                <div class="registration-note"><i class="bi bi-shield-check"></i><span>Data kamu aman dan hanya digunakan untuk proses penerimaan mahasiswa baru.</span></div>
            </div>
            <form class="registration-form" method="POST" action="{{ route('landing.register') }}">
                @csrf
                @if($errors->any())
                    <div class="registration-alert registration-alert-error">
                        <i class="bi bi-exclamation-circle"></i>
                        <div><strong>Form belum lengkap.</strong><br>{{ $errors->first() }}</div>
                    </div>
                @endif
                <div class="form-grid">
                    <label class="form-field form-field-wide"><span>Nama lengkap *</span><input name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="Nama sesuai identitas"></label>
                    <label class="form-field"><span>Nomor WhatsApp *</span><input name="whatsapp" value="{{ old('whatsapp') }}" required inputmode="tel" autocomplete="tel" placeholder="08xxxxxxxxxx"></label>
                    <label class="form-field"><span>Email</span><input type="email" name="email" value="{{ old('email') }}" autocomplete="email" placeholder="nama@email.com"></label>
                    <label class="form-field">
                        <span>Pendidikan terakhir *</span>
                        <select name="education_level" required>
                            <option value="">Pilih pendidikan</option>
                            @foreach(['SMA', 'SMK', 'MA', 'Paket C', 'Lainnya'] as $level)
                                <option value="{{ $level }}" @selected(old('education_level') === $level)>{{ $level }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="form-field"><span>Asal sekolah *</span><input name="school" value="{{ old('school') }}" required placeholder="Nama sekolah"></label>
                    <label class="form-field form-field-wide"><span>Kota / kabupaten domisili *</span><input name="city" value="{{ old('city') }}" required placeholder="Contoh: Kota Bandung"></label>
                    <label class="form-field">
                        <span>Kampus tujuan *</span>
                        <select name="kampus_id" id="registration-campus" required>
                            <option value="">Pilih kampus</option>
                            @foreach($kampuses as $kampus)
                                <option value="{{ $kampus->id }}" @selected((string) old('kampus_id') === (string) $kampus->id)>{{ $kampus->nama_kampus }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="form-field">
                        <span>Program studi *</span>
                        <select name="jurusan_id" id="registration-major" required>
                            <option value="">Pilih kampus terlebih dahulu</option>
                            @foreach($kampuses as $kampus)
                                @foreach($kampus->jurusans as $jurusan)
                                    <option value="{{ $jurusan->id }}" data-campus="{{ $kampus->id }}" @selected((string) old('jurusan_id') === (string) $jurusan->id)>{{ $jurusan->jenjang ? $jurusan->jenjang.' — ' : '' }}{{ $jurusan->nama_jurusan }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </label>
                    <label class="form-field form-field-wide"><span>Catatan atau pertanyaan</span><textarea name="notes" rows="3" placeholder="Opsional">{{ old('notes') }}</textarea></label>
                </div>
                <label class="consent-field">
                    <input type="checkbox" name="consent" value="1" required @checked(old('consent'))>
                    <span>Saya menyetujui data ini digunakan untuk proses pendaftaran dan dihubungi oleh tim admisi.</span>
                </label>
                <button class="registration-submit" type="submit">Kirim pendaftaran <i class="bi bi-arrow-right"></i></button>
            </form>
        </section>
    </div>

    @if(session('registration_success'))
        <div class="registration-toast" role="status"><i class="bi bi-check-circle-fill"></i><span>{{ session('registration_success') }}</span></div>
    @endif

    <footer class="landing-footer">
        <div class="landing-shell footer-inner">
            <div class="landing-brand"><span class="landing-logo"><i class="bi bi-mortarboard-fill"></i></span>Portal Mahasiswa</div>
            <div class="footer-contact">
                @if($content['contact_whatsapp'])<a href="https://wa.me/{{ preg_replace('/\D/', '', $content['contact_whatsapp']) }}"><i class="bi bi-whatsapp"></i> WhatsApp</a>@endif
                @if($content['contact_email'])<a href="mailto:{{ $content['contact_email'] }}"><i class="bi bi-envelope"></i> {{ $content['contact_email'] }}</a>@endif
            </div>
        </div>
    </footer>
    <script>
        (() => {
            const modal = document.getElementById('form-pendaftaran');
            const campus = document.getElementById('registration-campus');
            const major = document.getElementById('registration-major');
            const majorOptions = [...major.options].filter(option => option.dataset.campus);

            const filterMajors = () => {
                const selected = campus.value;
                majorOptions.forEach(option => {
                    option.hidden = option.dataset.campus !== selected;
                    option.disabled = option.dataset.campus !== selected;
                });
                if (major.selectedOptions[0]?.disabled) major.value = '';
                major.options[0].textContent = selected ? 'Pilih program studi' : 'Pilih kampus terlebih dahulu';
            };
            const openModal = () => {
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('modal-open');
                setTimeout(() => modal.querySelector('input, select')?.focus(), 100);
            };
            const closeModal = () => {
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('modal-open');
            };

            document.querySelectorAll('.js-open-registration').forEach(button => button.addEventListener('click', event => {
                event.preventDefault();
                openModal();
            }));
            document.querySelectorAll('.js-close-registration').forEach(button => button.addEventListener('click', closeModal));
            document.addEventListener('keydown', event => { if (event.key === 'Escape') closeModal(); });
            campus.addEventListener('change', filterMajors);
            filterMajors();

            if (@json($errors->any())) openModal();
            if (window.location.hash === '#form-pendaftaran') openModal();
        })();
    </script>
</body>
</html>
