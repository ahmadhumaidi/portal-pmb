<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    protected $fillable = [
        'badge',
        'headline',
        'subheadline',
        'primary_button_text',
        'primary_button_url',
        'secondary_button_text',
        'secondary_button_url',
        'announcement',
        'registration_status',
        'registration_deadline',
        'contact_whatsapp',
        'contact_email',
        'features',
        'steps',
        'faqs',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'steps' => 'array',
            'faqs' => 'array',
            'registration_deadline' => 'date',
        ];
    }

    public static function defaults(): array
    {
        return [
            'badge' => 'Penerimaan Mahasiswa Baru 2026/2027',
            'headline' => 'Langkah pertamamu menuju masa depan dimulai di sini.',
            'subheadline' => 'Daftar kuliah dengan proses yang mudah, transparan, dan didampingi tim kami dari awal hingga resmi menjadi mahasiswa.',
            'primary_button_text' => 'Mulai Pendaftaran',
            'primary_button_url' => '#alur',
            'secondary_button_text' => 'Lihat Program Studi',
            'secondary_button_url' => '#program',
            'announcement' => 'Gelombang 1 sedang dibuka — dapatkan potongan biaya pendaftaran.',
            'registration_status' => 'Pendaftaran Dibuka',
            'registration_deadline' => now()->addMonth()->toDateString(),
            'contact_whatsapp' => '6281234567890',
            'contact_email' => 'pmb@kampus.ac.id',
            'features' => [
                ['title' => 'Pilihan Kampus Luas', 'description' => 'Temukan kampus dan program studi yang paling sesuai dengan tujuanmu.', 'icon' => 'bi-buildings'],
                ['title' => 'Proses Serba Digital', 'description' => 'Lengkapi data, unggah berkas, dan pantau status dari mana saja.', 'icon' => 'bi-phone'],
                ['title' => 'Pendampingan Personal', 'description' => 'Tim admisi siap membantu setiap tahap proses pendaftaranmu.', 'icon' => 'bi-chat-heart'],
            ],
            'steps' => [
                ['title' => 'Buat akun', 'description' => 'Isi data awal dan pilih kampus tujuan.'],
                ['title' => 'Lengkapi berkas', 'description' => 'Unggah dokumen persyaratan secara online.'],
                ['title' => 'Verifikasi', 'description' => 'Tim kami akan memeriksa berkas dan pembayaran.'],
                ['title' => 'Resmi diterima', 'description' => 'Pantau hasil seleksi langsung melalui portal.'],
            ],
            'faqs' => [
                ['question' => 'Siapa yang dapat mendaftar?', 'answer' => 'Lulusan SMA, SMK, MA, atau sederajat dapat mengikuti proses penerimaan.'],
                ['question' => 'Apakah pendaftaran bisa dilakukan dari ponsel?', 'answer' => 'Bisa. Seluruh proses dirancang nyaman digunakan melalui ponsel maupun komputer.'],
                ['question' => 'Bagaimana jika saya kesulitan mengunggah berkas?', 'answer' => 'Hubungi tim admisi melalui WhatsApp yang tersedia pada halaman ini.'],
            ],
        ];
    }
}
