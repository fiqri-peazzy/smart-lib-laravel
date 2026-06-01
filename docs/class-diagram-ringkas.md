# Class Diagram Ringkas — Smart Library System
*(Versi Draft Skripsi — A4 Landscape)*

---

## PlantUML

> Render di: https://www.planttext.com atau plugin VS Code PlantUML

```plantuml
@startuml SmartLibrary_Skripsi
!theme plain
skinparam classAttributeIconSize 0
skinparam classFontSize 10
skinparam classHeaderBackgroundColor #2C5F8A
skinparam classHeaderFontColor #FFFFFF
skinparam classBorderColor #2C5F8A
skinparam packageBorderColor #AAAAAA
skinparam packageFontSize 10
skinparam ArrowColor #444444
skinparam linetype ortho
skinparam nodesep 40
skinparam ranksep 50
hide empty members

title Diagram Kelas — Sistem Smart Library

' ─── MASTER DATA ─────────────────────────────
package "Master Data" #EEF4FF {
  class Major {
    +code : string
    +name : string
    +faculty : string
    +is_active : bool
  }
  class Author {
    +name : string
    +biography : string
  }
  class Publisher {
    +name : string
    +address : string
    +email : string
  }
  class Rack {
    +code : string
    +name : string
  }
  class BookCategory {
    +name : string
    +slug : string
    +color : string
    +is_active : bool
  }
  class SystemSetting {
    +key : string
    +value : string
    +type : string
    +group : string
    --
    +{static} get(key) : mixed
    +{static} set(key, value) : void
  }
}

' ─── KOLEKSI BUKU ─────────────────────────────
package "Koleksi Buku" #EEFFF5 {
  class Book {
    +isbn : string
    +title : string
    +publication_year : int
    +total_stock : int
    +available_stock : int
    +is_available : bool
    +is_digital : bool
    +digital_file_type : string
  }
  class BookItem {
    +qr_code : string
    +status : string
    +condition : string
  }
}

' ─── PENGGUNA ─────────────────────────────────
package "Pengguna" #FFFFF0 {
  class User {
    +nim : string
    +name : string
    +email : string
    +major_id : int
    +angkatan : int
    +max_loans : int
    +total_fines : decimal
    +status : string
    +role : string
  }
}

' ─── TRANSAKSI ────────────────────────────────
package "Transaksi Peminjaman" #FFF0F0 {
  class Loan {
    +loan_date : date
    +due_date : date
    +return_date : date
    +status : string
    +is_extended : bool
    +fine_amount : decimal
    +return_condition : string
  }
  class Booking {
    +booking_date : date
    +expires_at : date
    +status : string
    +is_priority : bool
  }
  class LoanHistory {
    +total_loans : int
    +on_time_returns : int
    +late_returns : int
    +active_loans : int
    +calculated_score : decimal
  }
}

' ─── KEUANGAN ─────────────────────────────────
package "Keuangan" #F5EEFF {
  class Fine {
    +amount : decimal
    +days_overdue : int
    +status : string
    +paid_amount : decimal
    +payment_method : string
    +is_waived : bool
  }
  class PaymentTransaction {
    +amount : decimal
    +payment_method : string
    +payment_channel : string
    +status : string
    +gateway_order_id : string
  }
}

' ─── RELASI ───────────────────────────────────
Author      "1" --> "0..*" Book          : menulis
Publisher   "1" --> "0..*" Book          : menerbitkan
Rack        "1" --> "0..*" Book          : disimpan di
Major       "1" --> "0..*" Book          : direkomendasikan
BookCategory "0..*" <--> "0..*" Book    : dikategorikan
Book        "1" *-- "1..*" BookItem      : memiliki eksemplar

Major       "1" --> "0..*" User          : menaungi
User        "1" --> "0..*" Loan          : meminjam
User        "1" --> "1"    LoanHistory   : memiliki riwayat
User        "1" --> "0..*" Booking       : memesan
User        "1" --> "0..*" Fine          : dikenai denda
BookItem    "1" --> "0..*" Loan          : dipinjamkan
Book        "1" --> "0..*" Booking       : dipesan melalui
Loan        "1" --> "0..1" Fine          : menghasilkan
Fine        "1" --> "0..*" PaymentTransaction : dibayar via
User        ..>             SystemSetting : <<use>>

@enduml
```

---

## Mermaid

> Render di: GitHub, Notion, Obsidian, atau https://mermaid.live

```mermaid
classDiagram
  direction LR

  class Major {
    +string code
    +string name
    +string faculty
    +bool is_active
  }
  class Author {
    +string name
    +string biography
  }
  class Publisher {
    +string name
    +string address
    +string email
  }
  class Rack {
    +string code
    +string name
  }
  class BookCategory {
    +string name
    +string slug
    +string color
    +bool is_active
  }
  class SystemSetting {
    +string key
    +string value
    +string type
    +string group
    +get(key)$ mixed
    +set(key, value)$ void
  }

  class Book {
    +string isbn
    +string title
    +int publication_year
    +int total_stock
    +int available_stock
    +bool is_available
    +bool is_digital
    +string digital_file_type
  }
  class BookItem {
    +string qr_code
    +string status
    +string condition
  }

  class User {
    +string nim
    +string name
    +string email
    +int major_id
    +int angkatan
    +int max_loans
    +decimal total_fines
    +string status
    +string role
  }

  class Loan {
    +date loan_date
    +date due_date
    +date return_date
    +string status
    +bool is_extended
    +decimal fine_amount
    +string return_condition
  }
  class Booking {
    +date booking_date
    +date expires_at
    +string status
    +bool is_priority
  }
  class LoanHistory {
    +int total_loans
    +int on_time_returns
    +int late_returns
    +int active_loans
    +decimal calculated_score
  }

  class Fine {
    +decimal amount
    +int days_overdue
    +string status
    +decimal paid_amount
    +string payment_method
    +bool is_waived
  }
  class PaymentTransaction {
    +decimal amount
    +string payment_method
    +string payment_channel
    +string status
    +string gateway_order_id
  }

  Author      "1"   --> "0..*" Book               : menulis
  Publisher   "1"   --> "0..*" Book               : menerbitkan
  Rack        "1"   --> "0..*" Book               : disimpan di
  Major       "1"   --> "0..*" Book               : direkomendasikan
  BookCategory"0..*"<-->"0..*" Book               : dikategorikan
  Book        "1"   *-- "1..*" BookItem           : memiliki eksemplar
  Major       "1"   --> "0..*" User               : menaungi
  User        "1"   --> "0..*" Loan               : meminjam
  User        "1"   --> "1"    LoanHistory        : memiliki riwayat
  User        "1"   --> "0..*" Booking            : memesan
  User        "1"   --> "0..*" Fine               : dikenai denda
  BookItem    "1"   --> "0..*" Loan               : dipinjamkan
  Book        "1"   --> "0..*" Booking            : dipesan melalui
  Loan        "1"   --> "0..1" Fine               : menghasilkan
  Fine        "1"   --> "0..*" PaymentTransaction : dibayar via
  User        ..>              SystemSetting      : use
```

---

## Tips Cetak ke Kertas Skripsi

| Pengaturan | Rekomendasi |
|---|---|
| **Orientasi kertas** | Landscape (A4) |
| **Export PlantUML** | SVG → buka di browser → Print to PDF |
| **Export Mermaid** | [mermaid.live](https://mermaid.live) → Download PNG (pilih resolusi tinggi) |
| **Ukuran font** | Minimal 8pt agar terbaca |
| **Posisi di skripsi** | Gambar landscape biasanya ditempatkan miring (rotate 90°) di halaman tersendiri |
