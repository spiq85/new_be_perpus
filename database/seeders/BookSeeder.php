<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Category;
use App\Models\Review;
use App\Models\User;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan kategori sudah ada
        $categories = Category::pluck('id_category', 'category_name');

        if ($categories->isEmpty()) {
            $this->command->error('Kategori kosong! Jalankan CategorySeeder dulu!');
            return;
        }

        $books = [
            // 20 BUKU NYATA & LEGENDARIS
            ['title' => 'Laskar Pelangi', 'author' => 'Andrea Hirata', 'publisher' => 'Bentang Pustaka', 'publish_year' => 2005, 'description' => 'Kisah inspiratif 10 anak dari desa miskin di Belitung yang berjuang mengejar mimpi lewat pendidikan.', 'stock' => 12, 'categories' => ['Fiksi', 'Novel', 'Inspiratif', 'Indonesia']],
            ['title' => 'Bumi Manusia', 'author' => 'Pramoedya Ananta Toer', 'publisher' => 'Hasta Mitra', 'publish_year' => 1975, 'description' => 'Bagian pertama Tetralogi Buru. Kisah cinta Minke dan Annelies di masa kolonial Belanda.', 'stock' => 8, 'categories' => ['Fiksi', 'Sejarah', 'Novel', 'Indonesia']],
            ['title' => 'Harry Potter and the Philosopher\'s Stone', 'author' => 'J.K. Rowling', 'publisher' => 'Bloomsbury', 'publish_year' => 1997, 'description' => 'Petualangan pertama Harry Potter di dunia sihir Hogwarts.', 'stock' => 15, 'categories' => ['Fantasi', 'Petualangan', 'Remaja']],
            ['title' => 'The Da Vinci Code', 'author' => 'Dan Brown', 'publisher' => 'Doubleday', 'publish_year' => 2003, 'description' => 'Thriller misteri yang mengungkap rahasia tersembunyi di balik lukisan Leonardo da Vinci.', 'stock' => 10, 'categories' => ['Thriller', 'Misteri', 'Fiksi']],
            ['title' => 'Atomic Habits', 'author' => 'James Clear', 'publisher' => 'Avery', 'publish_year' => 2018, 'description' => 'Buku pengembangan diri terbaik tentang cara membangun kebiasaan baik dan menghilangkan yang buruk.', 'stock' => 20, 'categories' => ['Pengembangan Diri', 'Psikologi', 'Non-Fiksi']],
            ['title' => 'Sapiens: A Brief History of Humankind', 'author' => 'Yuval Noah Harari', 'publisher' => 'Harvill Secker', 'publish_year' => 2011, 'description' => 'Sejarah umat manusia dari zaman batu hingga era digital.', 'stock' => 14, 'categories' => ['Sejarah', 'Sains', 'Non-Fiksi']],
            ['title' => 'Dilan: Dia Adalah Dilanku Tahun 1990', 'author' => 'Pidi Baiq', 'publisher' => 'Pastel Books', 'publish_year' => 2014, 'description' => 'Kisah cinta remaja Bandung tahun 90-an yang penuh cinta, motor, dan puisi.', 'stock' => 18, 'categories' => ['Romansa', 'Remaja', 'Indonesia']],
            ['title' => 'Ayat-Ayat Cinta', 'author' => 'Habiburrahman El Shirazy', 'publisher' => 'Republika', 'publish_year' => 2004, 'description' => 'Novel religi romantis terlaris sepanjang masa di Indonesia.', 'stock' => 11, 'categories' => ['Religi', 'Romansa', 'Indonesia']],
            ['title' => 'Rich Dad Poor Dad', 'author' => 'Robert T. Kiyosaki', 'publisher' => 'Warner Books', 'publish_year' => 1997, 'description' => 'Pelajaran keuangan dari dua ayah dengan cara pandang berbeda.', 'stock' => 22, 'categories' => ['Keuangan', 'Pengembangan Diri', 'Bisnis']],
            ['title' => 'The Subtle Art of Not Giving a F*ck', 'author' => 'Mark Manson', 'publisher' => 'HarperOne', 'publish_year' => 2016, 'description' => 'Buku pengembangan diri yang blak-blakan dan sangat populer.', 'stock' => 17, 'categories' => ['Pengembangan Diri', 'Psikologi']],
            ['title' => 'Petualangan Tintin: Kuil Matahari', 'author' => 'Hergé', 'publisher' => 'Casterman', 'publish_year' => 1949, 'description' => 'Serial komik petualangan klasik yang dicintai semua umur.', 'stock' => 9, 'categories' => ['Komik', 'Anak', 'Petualangan']],
            ['title' => '5 Cm', 'author' => 'Donny Dhirgantoro', 'publisher' => 'Grasindo', 'publish_year' => 2005, 'description' => 'Kisah persahabatan 5 sahabat yang mendaki Gunung Semeru.', 'stock' => 13, 'categories' => ['Petualangan', 'Remaja', 'Indonesia']],
            ['title' => '1984', 'author' => 'George Orwell', 'publisher' => 'Secker & Warburg', 'publish_year' => 1949, 'description' => 'Novel dystopia paling berpengaruh abad ke-20.', 'stock' => 7, 'categories' => ['Fiksi', 'Klasik', 'Dystopia']],
            ['title' => 'To Kill a Mockingbird', 'author' => 'Harper Lee', 'publisher' => 'J.B. Lippincott', 'publish_year' => 1960, 'description' => 'Klasik Amerika tentang rasisme dan keadilan.', 'stock' => 10, 'categories' => ['Klasik', 'Fiksi', 'Drama']],
            ['title' => 'The Alchemist', 'author' => 'Paulo Coelho', 'publisher' => 'HarperTorch', 'publish_year' => 1988, 'description' => 'Perjalanan spiritual seorang penggembala Andalusia mencari harta karun.', 'stock' => 25, 'categories' => ['Filosofi', 'Petualangan', 'Inspiratif']],
            ['title' => 'Norwegian Wood', 'author' => 'Haruki Murakami', 'publisher' => 'Kodansha', 'publish_year' => 1987, 'description' => 'Novel cinta dan kesedihan klasik dari Jepang.', 'stock' => 11, 'categories' => ['Romansa', 'Drama', 'Jepang']],
            ['title' => 'Cantik Itu Luka', 'author' => 'Eka Kurniawan', 'publisher' => 'Gramedia Pustaka Utama', 'publish_year' => 2002, 'description' => 'Novel magis realis Indonesia yang mendunia.', 'stock' => 9, 'categories' => ['Fiksi', 'Sejarah', 'Indonesia']],
            ['title' => 'Perahu Kertas', 'author' => 'Dee Lestari', 'publisher' => 'Bentang Pustaka', 'publish_year' => 2009, 'description' => 'Cinta segitiga Keenan, Kugy, dan teman-temannya.', 'stock' => 16, 'categories' => ['Romansa', 'Remaja', 'Indonesia']],
            ['title' => 'The Psychology of Money', 'author' => 'Morgan Housel', 'publisher' => 'Harriman House', 'publish_year' => 2020, 'description' => 'Buku keuangan terbaik 5 tahun terakhir.', 'stock' => 19, 'categories' => ['Keuangan', 'Psikologi', 'Non-Fiksi']],
            ['title' => 'Man\'s Search for Meaning', 'author' => 'Viktor E. Frankl', 'publisher' => 'Beacon Press', 'publish_year' => 1946, 'description' => 'Kisah nyata psikolog yang selamat dari kamp konsentrasi Nazi.', 'stock' => 12, 'categories' => ['Psikologi', 'Filosofi', 'Biografi']],
        ];

        // Buat buku + attach kategori
        foreach ($books as $b) {
            $book = Book::create([
                'title'        => $b['title'],
                'author'       => $b['author'],
                'publisher'    => $b['publisher'],
                'publish_year' => $b['publish_year'],
                'description'  => $b['description'],
                'stock'        => $b['stock'],
            ]);

            $categoryIds = [];
            foreach ($b['categories'] as $catName) {
                if ($categories->has($catName)) {
                    $categoryIds[] = $categories[$catName];
                }
            }

            if (!empty($categoryIds)) {
                $book->categories()->attach($categoryIds);
            }
        }

        $this->command->info('20 buku nyata berhasil dibuat!');

        // === BAGIAN RATING & REVIEW ===
        $users = User::where('role', 'user')->inRandomOrder()->take(30)->get();

        if ($users->isEmpty()) {
            $this->command->warn('Tidak ada user biasa. Rating & review di-skip.');
            return;
        }

        $allBooks = Book::all();

        foreach ($allBooks as $book) {
            $reviewCount = rand(8, 45);
            $totalRating = 0;

            for ($i = 0; $i < $reviewCount; $i++) {
                $user = $users->random();
                $rating = rand(32, 50) / 10; // 3.2 – 5.0 (lebih realistis)
                $totalRating += $rating;

                Review::create([
                    'id_user' => $user->id_user,
                    'id_book' => $book->id_book,
                    'review'  => $this->fakeReviews()[array_rand($this->fakeReviews())],
                    'rating'  => $rating,
                ]);
            }

            $avgRating = round($totalRating / $reviewCount, 1);
            $book->update([
                'reviews_avg_rating' => $avgRating,
                'reviews_count'      => $reviewCount,
            ]);

            $this->command->info("{$book->title} → {$reviewCount} ulasan, rating rata-rata: {$avgRating}");
        }

        $this->command->info('SELESAI! Semua buku sekarang punya rating & ulasan realistis!');
    }

    // METHOD FAKE REVIEW (WAJIB ADA!)
    private function fakeReviews(): array
    {
        return [
            "Buku yang sangat menginspirasi! Wajib baca untuk semua orang.",
            "Salah satu novel terbaik yang pernah saya baca. Ceritanya dalam banget.",
            "Bener-bener bikin nangis dan termotivasi. Terima kasih penulis!",
            "Buku ini mengubah cara pandang saya tentang hidup.",
            "Keren banget! Gak bisa berhenti baca sampai habis.",
            "Rekomendasi banget buat yang lagi down, ini obatnya!",
            "Ceritanya realistis dan menyentuh hati. 10/10!",
            "Buku legendaris. Harus ada di rak buku setiap orang.",
            "Baca ini pas SMA, sampe sekarang masih inget banget ceritanya.",
            "Nilai sejarah dan budayanya sangat kaya. Luar biasa!",
            "Buku yang bikin mikir jauh tentang makna hidup.",
            "Romantisnya pas, gak lebay. Suka banget!",
            "Buku ini bikin saya jatuh cinta sama membaca lagi.",
            "Setiap halamannya penuh makna. Masterpiece!",
            "Baca ulang berkali-kali tetep gak bosen.",
            "Buku yang mengajarkan banyak hal tanpa terasa menggurui.",
            "Endingnya bikin speechless. Gila!",
            "Buku ini harus jadi bacaan wajib sekolah!",
            "Dari awal sampai akhir, gak ada satupun halaman yang membosankan.",
            "Terima kasih telah menulis karya seindah ini.",
            "Buku ini bikin saya jadi orang yang lebih baik.",
            "Saya kasih 5 bintang karena emang pantas!",
            "Gak nyangka buku ini sebagus ini. Rekomen banget!",
            "Baca ini sambil nangis. Sedih tapi indah.",
            "Buku favorit saya sepanjang masa!",
            "Buku ini bikin saya belajar banyak hal baru.",
            "Gaya bahasanya enak dibaca, gak berat.",
            "Plot twist-nya gila! Gak nyangka!",
            "Buku yang bikin saya mikir ulang tentang hidup.",
            "Worth every penny!",
        ];
    }
}
