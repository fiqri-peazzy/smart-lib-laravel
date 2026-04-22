# 4.2.2 Activity Diagram (Hasil Analisis Sistem)

Activity diagram adalah representasi visual dari alur kerja (workflow) atau aktivitas sistem yang menggambarkan langkah-langkah secara berurutan. Format ini disusun menggunakan model **Multilane Pool (Swimlane)** yang dibungkus oleh satu bingkai utama (Pool) dan dibagi menjadi dua lajur (Swimlanes).

**PENTING UNTUK DIGUNAKAN DI DRAW.IO:**
Karena kode `mermaid` seringkali _ngaco_ (_strecthed/awur-awuran_) saat me-render _Swimlane_, **gunakan kode PlantUML di bawah ini.**
Caranya di Draw.io: Klik **Arrange** -> **Insert** -> **Advanced** -> **PlantUML** lalu tempel kode-kodenya. Draw.io akan meng-_generate_ tabel kotak (Pool & Swimlanes) **100% SAMA PERSIS** seperti gambar referensi Anda tanpa kocar-kacir!

---

## 1. Modul Registrasi Anggota & Login

```plantuml
@startuml
title AD Pendaftaran & Login
|Admin / Anggota|
start
:buka halaman login;
|Sistem|
:tampil form login;
|Admin / Anggota|
:input email dan password;
:klik tombol login;
|Sistem|
if (validasi data) then (Data Tidak Valid)
  |Admin / Anggota|
  :tampilkan error;
  detach
else (Data Valid)
  |Sistem|
  :buat session login;
  :redirect ke dashboard;
  stop
endif
@enduml
```

---

## 2. Modul Pencarian dan Booking Buku Fisik

```plantuml
@startuml
title AD Booking Buku Fisik
|Anggota (Mahasiswa/Dosen)|
start
:ketik kata kunci;
|Sistem|
:tampil hasil pencarian;
|Anggota (Mahasiswa/Dosen)|
:pilih buku fisik;
|Sistem|
if (stok > 0?) then (Stok Habis)
  |Anggota (Mahasiswa/Dosen)|
  :tampilkan alert stok habis;
  detach
else (Stok Tersedia)
  |Sistem|
  :tampil detail buku;
  |Anggota (Mahasiswa/Dosen)|
  :klik tombol booking;
  |Sistem|
  :update sisa stok;
  :simpan data booking;
  stop
endif
@enduml
```

---

## 3. Modul Proses Verifikasi (Approve) Peminjaman

```plantuml
@startuml
title AD Verifikasi Peminjaman
|Admin / Staff|
start
:buka antrean pending;
|Sistem|
:tampil data antrean;
|Admin / Staff|
:pilih tiket booking;
|Sistem|
:tampil detail tiket;
|Admin / Staff|
:cek identitas & fisik buku;
|Sistem|
if (sesuai?) then (Data Sesuai)
  |Admin / Staff|
  :klik approve peminjaman;
  |Sistem|
  :ubah status ke sedang dipinjam;
  :catat tanggal jatuh tempo;
  stop
else (Data Tidak Sesuai)
  |Sistem|
  :batalkan booking;
  stop
endif
@enduml
```

---

## 4. Modul Pengembalian & Pembayaran Denda

```plantuml
@startuml
title AD Pengembalian Buku
|Admin / Staff|
start
:terima buku dari anggota;
:scan barcode buku;
|Sistem|
:catat tanggal kembali;
if (lewat jatuh tempo?) then (Ya)
  :tampilkan total denda;
  |Admin / Staff|
  :terima uang & klik lunas;
  |Sistem|
else (Tidak)
endif
:ubah status ke selesai;
:tambah stok eksemplar;
stop
@enduml
```
