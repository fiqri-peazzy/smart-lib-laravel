# 4.2.3 Sequence Diagram (Hasil Analisis Sistem)

_Sequence Diagram_ menggambarkan interaksi antar objek di dalam dan di sekitar sistem (termasuk aktor, tampilan antarmuka, pengontrol, dan database) berupa pengiriman pesan _(messages)_ terhadap waktu. Spesifikasi relasi objek (_Lifelines_) yang digunakan pada sistem _Smart-Lib_ ini merujuk secara teknikal meniru pola arsitektur MVC (Model-View-Controller) milik framework Laravel.

Diagram di bawah ini secara lengkap menyematkan representasi kotak _boundary-interface_ (View), kotak _control_ (Controller), dan lambang _entity-model_ (Database & Class). Terdapat versi Mermaid.js dan PlantUML untuk setiap modul (kami merekomendasikan paste kode **PlantUML ke Draw.io** untuk hasil kotak _lifelines_ yang sama persis seperti gambar yang Anda ajukan).

---

## 1. Modul Pencarian & Booking Buku Fisik

Alur pengiriman pesan _(request-return)_ ketika pengguna (Mahasiswa/Dosen) memproses penambahan tiket pesanan (booking).

### A. Kode PlantUML (Disarankan untuk Draw.io)

_Cara Penggunaan: Buka Draw.io -> Arrange -> Insert -> Advanced -> PlantUML_

```plantuml
@startuml
autonumber
actor "Anggota Pelanggan" as User
participant "BookingView (Interface)" as View
participant "BookingController (Control)" as Ctrl
participant "Book & Booking (Entity)" as Model
participant "Database" as DB

User -> View : klikBooking(book_id)
activate View
View -> Ctrl : store(request)
activate Ctrl
Ctrl -> Model : create(data_booking)
activate Model
Model -> DB : insertIntoBookings()
activate DB
DB --> Model : return booking_id
deactivate DB
Model -> DB : updateBookStock()
activate DB
DB --> Model : return success
deactivate DB
Model --> Ctrl : return success
deactivate Model
Ctrl --> View : redirectWithSuccess()
deactivate Ctrl
View --> User : tampilkanPesanSukses()
deactivate View
@enduml
```

### B. Diagram Mermaid JS

```mermaid
sequenceDiagram
    autonumber
    actor User as Anggota Pelanggan
    participant View as BookingView (Interface)
    participant Ctrl as BookingController (Control)
    participant Model as Book & Booking (Entity)
    participant DB as Database

    User->>View: klikBooking(book_id)
    activate View
    View->>Ctrl: store(request)
    activate Ctrl
    Ctrl->>Model: create(data_booking)
    activate Model
    Model->>DB: insertIntoBookings()
    activate DB
    DB-->>Model: return booking_id
    deactivate DB
    Model->>DB: updateBookStock()
    activate DB
    DB-->>Model: return success
    deactivate DB
    Model-->>Ctrl: return success
    deactivate Model
    Ctrl-->>View: redirectWithSuccess()
    deactivate Ctrl
    View-->>User: tampilkanPesanSukses()
    deactivate View
```

---

## 2. Modul Proses Verifikasi Peminjaman oleh Admin

Alur interaksi logika ketika Admin meng-_approve_ tiket yang masuk hingga _State_ pesanan tercatat 'Aktif'.

### A. Kode PlantUML (Disarankan untuk Draw.io)

```plantuml
@startuml
autonumber
actor "Admin / Staff" as Admin
participant "TransactionView (Interface)" as View
participant "LoanController (Control)" as Ctrl
participant "Loan & Booking (Entity)" as Model
participant "Database" as DB

Admin -> View : klikApprovePeminjaman(booking_id)
activate View
View -> Ctrl : approve(request)
activate Ctrl
Ctrl -> Model : updateStatusAndCreateLoan(booking_id)
activate Model
Model -> DB : updateBookingStatus('active')
activate DB
DB --> Model : return success
deactivate DB
Model -> DB : insertIntoLoans(data_loan)
activate DB
DB --> Model : return loan_id
deactivate DB
Model --> Ctrl : return success
deactivate Model
Ctrl --> View : redirectWithSuccess()
deactivate Ctrl
View --> Admin : tampilkanStatusDipinjam()
deactivate View
@enduml
```

### B. Diagram Mermaid JS

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Admin / Staff
    participant View as TransactionView (Interface)
    participant Ctrl as LoanController (Control)
    participant Model as Loan & Booking (Entity)
    participant DB as Database

    Admin->>View: klikApprovePeminjaman(booking_id)
    activate View
    View->>Ctrl: approve(request)
    activate Ctrl
    Ctrl->>Model: updateStatusAndCreateLoan(booking_id)
    activate Model
    Model->>DB: updateBookingStatus('active')
    activate DB
    DB-->>Model: return success
    deactivate DB
    Model->>DB: insertIntoLoans(data_loan)
    activate DB
    DB-->>Model: return loan_id
    deactivate DB
    Model-->>Ctrl: return success
    deactivate Model
    Ctrl-->>View: redirectWithSuccess()
    deactivate Ctrl
    View-->>Admin: tampilkanStatusDipinjam()
    deactivate View
```

---

## 3. Modul Pengembalian Buku & Perhitungan Denda

Alur interaksi saat buku fisik dicatat pulang, serta kalkulasi uang keterlambatan (_fines_).

### A. Kode PlantUML (Disarankan untuk Draw.io)

```plantuml
@startuml
autonumber
actor "Admin / Staff" as Admin
participant "ReturnView (Interface)" as View
participant "ReturnController (Control)" as Ctrl
participant "Loan & Fine (Entity)" as Model
participant "Database" as DB

Admin -> View : submitScanBuku(book_code)
activate View
View -> Ctrl : processReturn(request)
activate Ctrl
Ctrl -> Model : checkAndCalculateFine(loan_id)
activate Model
Model -> DB : getLoanDueDate()
activate DB
DB --> Model : return date_info
deactivate DB
Model --> Ctrl : return fine_amount
deactivate Model
Ctrl -> Model : completeReturn(loan_id, fine_status)
activate Model
Model -> DB : updateLoansAndStock()
activate DB
DB --> Model : return success
deactivate DB
Model --> Ctrl : return success
deactivate Model
Ctrl --> View : redirectWithSuccess()
deactivate Ctrl
View --> Admin : tampilkanBuktiPengembalian()
deactivate View
@enduml
```

### B. Diagram Mermaid JS

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Admin / Staff
    participant View as ReturnView (Interface)
    participant Ctrl as ReturnController (Control)
    participant Model as Loan & Fine (Entity)
    participant DB as Database

    Admin->>View: submitScanBuku(book_code)
    activate View
    View->>Ctrl: processReturn(request)
    activate Ctrl
    Ctrl->>Model: checkAndCalculateFine(loan_id)
    activate Model
    Model->>DB: getLoanDueDate()
    activate DB
    DB-->>Model: return date_info
    deactivate DB
    Model-->>Ctrl: return fine_amount
    deactivate Model
    Ctrl->>Model: completeReturn(loan_id, fine_status)
    activate Model
    Model->>DB: updateLoansAndStock()
    activate DB
    DB-->>Model: return success
    deactivate DB
    Model-->>Ctrl: return success
    deactivate Model
    Ctrl-->>View: redirectWithSuccess()
    deactivate Ctrl
    View-->>Admin: tampilkanBuktiPengembalian()
    deactivate View
```
