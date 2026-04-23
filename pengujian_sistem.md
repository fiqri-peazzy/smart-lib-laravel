    Hasil Pengujian Sistem
    Whitebox Testing

Berdasarkan sistem yang telah dibangun, pengujian whitebox (struktural) dilakukan pada modul Keranjang Belanja (Cart), secara spesifik pada fungsi penambahan produk (store) yang berada pada CartController. Pemilihan modul ini dikarenakan blok method ini memuat fungsionalitas utama yang melibatkan beberapa percabangan logika (predicate) untuk validasi ketersediaan stok hingga validasi duplikasi pengurutan pesanan, sehingga sangat cocok untuk analisis jalur struktural dan penghitungan Cyclomatic Complexity.
Kode program
Berikut adalah cuplikan kode program murni dari fungsi store pada CartController yang dipetakan berdasarkan titik-titik eksekusi node.
public function store(Request $request)	1
    {	1
        $request->validate([	2
            'product_id' => 'required|exists:products,id',	2
            'product_variant_id' => 'nullable|exists:product_variants,id',	2
            'quantity' => 'required|integer|min:1'	2
        ]);	2
        $product = Product::findOrFail($request->product_id); 2
if ($product->stock < $request->quantity) {	3
            return back()->with('error', 'Stok tidak mencukupi.');3	4
        }	4
        $cart = $this->getCart();	5
        $cartItem = $cart->items()	5
            ->where('product_id', $product->id)	5
            ->where('product_variant_id', $request->product_variant_id)	5
            ->first();	5
        if ($cartItem) { 6
$cartItem->quantity += $request->quantity; 7
$cartItem->save(); 7
} else { 8
$cart->items()->create([ 8
'product_id' => $product->id, 8
'product_variant_id' => $request->product_variant_id, 8
'quantity' => $request->quantity 8
]); 8
} 8
return redirect()->route('cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang.'); 9
} 9
End 10

    Flowchart

Gambar 4. 11 Flowchart

 
Flowgraph

Gambar 4. 12 Flowgraph
Pemetaan Kode ke Simpul (Node)
Pemetaan alur kontrol logic dari algoritma sistem tersebut melahirkan node pemetaan berikut:
Node 1: Mulai
Node 2: Menginisialisasi jalurnya Validasi Request dan query pengamanan Object database produk.
Node 3: Node Predikat yang menugaskan eksekusi logika kondisi dari if ($product->stock < $request->quantity)
	Node 4: Proses Terminasi di mana sistem melempar Error terkait stok.
	Node 5: Blok fungsional query pendataan keranjang.
	Node 6: Predikat kedua yang menugaskan pemeriksaan if ($cartItem)
Node 7: State keberadaan item meng-update/overwrite kuantitas transaksi.
Node 8: State absensi item menugaskan penyisipan (Insert) rekaman relasional baru.
Node 9: Pengembalian (Return) halaman keranjang belanja beserta penanda keberhasilan (Flash Session Success).
Node 10: Skala Titik Akhir / Selesai (End Point).
Perhitungan Cyclomatic Complexity (CC)
Berdasar simulasi Flowgraph di atas, kompleksitas siklomatis dilakukan penghitungan kuantitatif untuk memperoleh takaran minimum dalam batasan jalur mandiri (independent path):
Diketahui:
Edge (E) = 11 (Total Jumlah garis transisi dalam flowgraph)
Node (N) = 10 (Total simpul flowgraph)
Predicate Node (P) = 2 (Titik percabangan logika pada Node 2 dan Node 5)
Penyelesaian menggunakan rumus Node dan Edge:
V(G) = E - N + 2
V(G) = 11 - 10 + 2
V(G) = 3
Diverifikasi pula dengan rumus Predicate Node:
V(G) = P + 1
V(G) = 2 + 1
V(G) = 3
Hasil nilai metrik kompleksitas memberikan angka rujukan sebesar 3.
Menentukan Basis Path
Berdasarkan parameter kalkulasi CC yang ditetapkan berjumlah 3, dipersyaratkan terdapat 3 Basis Path yang dibentuk untuk memberikan jaminan verifikasi eksekusinya:
Tabel 4. 12 Basis Path
No. Rute Path Penjelasan Skenario Kondisi
1 1 - 2 - 3 - 9 Kondisi Terbatas: Stok Produk ternyata bernilai di bawah atau tidak mencukupi jumlah kuantitas yang diminta dalam transaksi.
2 1 - 2 - 4 - 5 - 6 - 8 - 9 Kondisi Update Kuantitas: Cek Stok Lolos, dan didapati produk ini ternyata sudah ada pada riwayat di dalam Keranjang Belanjanya.
3 1 - 2 - 4 - 5 - 7 - 8 - 9 Kondisi Masukan Baru: Cek Stok Lolos, namun transaksi ini murni karena produk varian bersangkutan belum didaftarkan sebelumnya di Keranjang (Record Baru).
Dari basis path diatsas membuktikan bahwasanya ketiga Basis Path error free dari anomali bottleneck dengan interelasi yang mulus. Tiap-tiap jalur statement memenuhi ketentuan logika terstruktur perangkat lunak dengan optimalitas yang baik tanpa adanya redundansi kode yang berlebih.
Blackbox Testing
engujian blackbox dilakukan Aplikasi Penjualan Furnitur Aquaris untuk memastikan fungsionalitas antarmuka sistem berjalan sesuai dengan spesifikasi kebutuhan use case dan skenario interaksi yang diharapkan oleh pengguna (baik pelanggan maupun pengelola admin), tanpa perlu meninjau detail struktur kode di dalam perangkat lunak tersebut.
Pengujian Blackbox (Sisi Pelanggan)
Pengujian di sisi pelanggan (aktor pembeli) difokuskan pada kelancaran alur berbelanja, mulai dari melihat katalog furnitur, pengelolaan keranjang, hingga tahapan checkout dan simulasi pembayaran Midtrans.
No Input / Event Fungsi yang Diuji Hasil yang Diharapkan Hasil Uji
1 Klik Menu Beranda dan Katalog Menampilkan daftar produk berjalan Tampil antarmuka katalog mebel secara utuh dengan kategorinya Sesuai
2 Klik Menu Login Membuka halaman Autentikasi Tampil form login dengan email dan password Sesuai
3 Input kredensial / sandi salah, klik login Validasi kredensial Tampil error message "Email atau password tidak sesuai" Sesuai
4 Input email & sandi benar, klik login Autorisasi dan Session Redirect langsung kembali ke halaman/toko pengguna Sesuai
5 Klik tombol "Tambah ke Keranjang" Memasukkan item Produk ke Cart Tampil notifikasi sukses dan jumlah angka pada ikon keranjang bertambah Sesuai
6 Membuka keranjang saat stok dinaikkan Perubahan nilai Quantity keranjang Subtotal kuantitas pada keranjang ter-update otomatis Sesuai
7 Mengklik tombol "Lanjut ke Pembayaran" Menjalankan fungsi Checkout Sistem menampilkan form detail alamat dan metode logistik pengiriman Sesuai
8 Memilih Tipe Pembayaran "Credit" (Cicilan) Kalkulasi perhitungan angsuran Sistem merubah tampilan total dengan menambahkan tabel kalkulasi simulasi bulanan cicilan Sesuai
9 Submit "Buat Pesanan" Checkout Validation & Integrasi Pesanan sukses terekam & Memunculkan sembulan antarmuka Payment Gateway Midtrans (Snap) Sesuai
10 Customer melakukan transfer virtual akun Proses Callback Midtrans Webhook Status payment_status sistem berubah dari "Unpaid" menjadi "Paid" (Diproses) otomatis Sesuai
11 Mengetikkan kuantitas pesanan melebihi stok Validasi Batas Maksimal Order Produk Tampil respon error peringatan bahwa batas ketersediaan barang (stock) tidak mencukupi Sesuai

    Pengujian Blackbox (Sisi Admin)

Pengujian fungsionalitas pada sisi admin sistem diarahkan pada operasional backend, yakni validasi proses pengelolaan katalog produk (CRUD), penanganan pesanan yang masuk, serta pendataan pelaporan (report).
No Input / Event Fungsi yang Diuji Hasil yang Diharapkan Hasil Uji
1 Admin Login akun kredensial yang valid Autorisasi hak akses Admin Redirect sukses menuju Dashboard ringkasan analitik aplikasi Sesuai
2 Buka menu "Kelola Produk" Pendataan Database Produk Mebel Tampil daftar entitas seluruh perabot mebel beserta fitur Tambah, Edit, dan Hapus Sesuai
3 Admin membuat Entitas "Produk Baru" Validasi Form Upload Gambar & Isian Produk berhasil ditambahkan beserta foto utamanya, memberikan notif "Berhasil Tersimpan" Sesuai
4 Buka menu "Pesanan Masuk" Menarik antrean pesanan Pelanggan Memunculkan daftar urutan order yang baru saja dibuat dengan keterangan label status Sesuai
5 Admin klik tombol "Update Resi / Status" Pembaruan lini masa tracking status Entitas status order berubah di sisi tabel dan tampil pada riwayat (history) akun pelanggan si pemesan Sesuai
6 Membuka menu kelola "Kategori" Pengelolaan jenis-jenis penamaan Sistem sukses merubah nama pengelompokan (Category Slug) jika diedit oleh admin Sesuai
7 Buka menu "Laporan Penjualan (Report)" Kalkulasi Rekapan Transaksi Lunas Tabel menampilkan riwayat total pemasukan dana komprehensif, dengan fasilitas filter laporan S
Berdasarkan serangkaian pengujian blackbox yang dilakukan pada puluhan skenario interaksi end-to-end, platform E-Commerce Mebel ini menunjukkan tingkat keberhasilan sebesar 100%. Sistem ini terbukti telah mampu memenuhi seluruh spesifikasi fungsional dari perspektif pengguna, baik secara kelancaran jual-beli sebagai konsumen pelanggan maupun manajemen sumber daya (resource management) di sisi administrator operasional.
