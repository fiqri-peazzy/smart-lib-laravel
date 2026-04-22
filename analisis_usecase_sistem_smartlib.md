# 4.2.1 Use Case Diagram (Hasil Analisis Sistem)

Use Case Diagram menggambarkan interaksi antara sistem dengan aktor luar yang menggunakan sistem ini. Untuk sistem perpustakaan _Smart-Lib_, terdapat dua aktor utama yang dianalisis, yaitu **Admin / Staff** dan **Mahasiswa / Dosen** (sebagai pengguna atau anggota perpustakaan).

Berikut adalah identifikasi peran dari masing-masing aktor:

1. **Admin / Staff**: Bertanggung jawab secara penuh dalam mengelola data master sistem, sirkulasi transaksi peminjaman, serta operasional administratif seperti validasi denda dan pendaftaran koleksi katalog buku.
2. **Mahasiswa / Dosen (Anggota)**: Pengguna yang diberikan akses untuk mengeksplorasi layanan perpustakaan secara mandiri, seperti melihat modul katalog, melakukan _booking_ buku, membaca koleksi _e-book_ digital, dan mengelola sirkulasi pribadi serta denda mereka.

---

## 1. Visualisasi Use Case Diagram (Mermaid)

_Gunakan ekstensi atau viewer Markdown yang mendukung `mermaid js` untuk melihat diagram di bawah secara langsung. Visualisasi ini telah disesuaikan dengan struktur dan tata letak relasi menyebar yang Anda bagikan sebagai referensi._

```mermaid
flowchart LR
    %% Styling Configuration
    classDef actor fill:#ffffff,stroke:#000000,stroke-width:2px,color:#000000;
    classDef usecase fill:#ffffff,stroke:#000000,stroke-width:1px,rx:40px,ry:40px,color:#000000;

    %% Actors
    Admin["👤 Admin / Staff"]:::actor
    User["👤 Mahasiswa / Dosen"]:::actor

    %% Admin Sub Use Cases
    LoginA(("Login Admin")):::usecase
    DataMaster(("Data Master")):::usecase
    DataAgt(("Data Keanggotaan")):::usecase
    DataTrans(("Data Transaksi")):::usecase
    SubTambah(("Tambah/Edit/Hapus")):::usecase
    CetakQR(("Cetak QR")):::usecase
    Validasi(("Verifikasi Transaksi")):::usecase

    %% User Sub Use Cases
    LoginU(("Login Anggota")):::usecase
    Register(("Daftar Akun")):::usecase
    Cari(("Pencarian Katalog")):::usecase
    Book(("Booking Buku")):::usecase
    Baca(("Baca Koleksi Digital")):::usecase
    MyLoan(("Riwayat Peminjaman")):::usecase
    Perpanjang(("Perpanjang Pinjaman")):::usecase
    Bayar(("Pembayaran Denda")):::usecase
    Profil(("Kelola Profil")):::usecase

    %% -------------- Relasi Admin --------------
    Admin --- LoginA

    LoginA -. "<<include>>" .-> DataMaster
    LoginA -. "<<include>>" .-> DataAgt
    LoginA -. "<<include>>" .-> DataTrans
    LoginA -. "<<include>>" .-> Profil

    DataMaster -. "<<include>>" .-> SubTambah
    DataMaster -. "<<extend>>" .-> CetakQR
    DataAgt -. "<<include>>" .-> SubTambah
    DataTrans -. "<<include>>" .-> Validasi

    %% -------------- Relasi Mahasiswa --------------
    LoginU --- User
    Register --- User
    Cari --- User
    Book --- User
    MyLoan --- User
    Bayar --- User
    Profil --- User

    %% Inner User Relasi
    Register -. "<<include>>" .-> LoginU
    Book -. "<<include>>" .-> LoginU
    MyLoan -. "<<include>>" .-> LoginU
    Bayar -. "<<include>>" .-> LoginU
    Profil -. "<<include>>" .-> LoginU

    Cari -. "<<extend>>" .-> Book
    Cari -. "<<extend>>" .-> Baca
    Baca -. "<<include>>" .-> LoginU

    MyLoan -. "<<extend>>" .-> Perpanjang

    %% -------------- Relasi Menyilang (Cross-Interaction Utama) --------------
    Book -. "<<extend>>" .-> Validasi
    Bayar -. "<<extend>>" .-> Validasi
```

---

## 2. Solusi Layout Otomatis Rapi di Draw.io (Menggunakan PlantUML)

Agar Peletakan Aktor persis berada di **Sayap Kiri dan Sayap Kanan** mengapit semua fitur layaknya gambar referensi Anda (tidak ada kotak sistem vertikal dan tidak menumpuk di sisi kiri semua), ikuti pembaruan kode ini di Draw.io:

1. Buka Draw.io
2. Klik Menu **Arrange** (Tata Letak) di bar atas -> Pilih **Insert** (Sisipkan) -> Klik **Advanced** (Lanjutan) -> Pilih **PlantUML**.
3. **Copy-Paste kode ajaib di bawah ini**:

```plantuml
@startuml
left to right direction
skinparam usecase {
    BackgroundColor White
    BorderColor Black
    ArrowColor Black
}
skinparam actor {
    BackgroundColor White
    BorderColor Black
}

actor "Admin / Staff" as Admin
actor "Mahasiswa / Dosen" as User

' Usecase Group Admin
usecase "Login Admin" as LoginA
usecase "Data Master" as Master
usecase "Data Keanggotaan" as Agt
usecase "Data Transaksi" as Trans
usecase "Tambah/Edit/Hapus Data" as SubTambah
usecase "Cetak QR" as CetakQR
usecase "Verifikasi Transaksi" as Verifikasi

' Usecase Group Mahasiswa
usecase "Login Anggota" as LoginU
usecase "Daftar Akun" as Reg
usecase "Pencarian Katalog" as Cari
usecase "Booking Buku Fisik" as Book
usecase "Baca Koleksi Digital" as Baca
usecase "Riwayat Peminjaman" as MyLoan
usecase "Perpanjang Pinjaman" as Extend
usecase "Pembayaran Denda" as Bayar
usecase "Kelola Profil" as Profil

' -----------------------------------------
' Relasi Kiri (Admin) -> Panah Normal
' -----------------------------------------
Admin --> LoginA

LoginA .> Master : <<include>>
LoginA .> Agt : <<include>>
LoginA .> Trans : <<include>>

Master .> SubTambah : <<include>>
Agt .> SubTambah : <<include>>
Master .> CetakQR : <<extend>>
Trans .> Verifikasi : <<include>>

' -----------------------------------------
' Relasi Kanan (Mahasiswa) -> Panah Dibalik (<--)
' Ini memaksa PlantUML menaruh Mahasiswa di Ujung Kanan
' -----------------------------------------
LoginU <-- User
Reg <-- User
Cari <-- User
Book <-- User
MyLoan <-- User
Bayar <-- User
Profil <-- User

' Relasi antar usecase Mahasiswa
LoginU <. Reg : <<include>>
LoginU <. Book : <<include>>
LoginU <. MyLoan : <<include>>
LoginU <. Bayar : <<include>>
LoginU <. Profil : <<include>>
LoginU <. Baca : <<include>>

Book <. Cari : <<extend>>
Baca <. Cari : <<extend>>
Extend <. MyLoan : <<extend>>

' -----------------------------------------
' Relasi Lintas Sektor (Mahasiswa ke Admin)
' -----------------------------------------
Verifikasi <. Book : <<extend>>
Verifikasi <. Bayar : <<extend>>

@enduml
```

4. Klik **Insert**. Tampilannya akan otomatis melebar dengan komposisi visual **Persis seperti gambar referensi Anda** (Admin di kiri pojok, Mahasiswa di Kanan pojok, fitur menyebar rapih di tengah).
