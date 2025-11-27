# Flowchart Proses Bisnis

Berikut adalah flowchart yang menggambarkan dua proses bisnis utama dalam aplikasi.

---

## 1. Flowchart Proses Pengadaan (Procurement)

```mermaid
graph TD
    subgraph "Proses Pengadaan Barang"
        A(Mulai) --> B{Admin: Buat Purchase Order};
        B --> C{Supplier: Lihat & Setujui PO};
        C --> D{Supplier: Kirim Barang};
        D --> E{Operator: Terima & Scan Barang};
        E --> F{Operator: Tempatkan di Lokasi Gudang};
        F --> G{Operator: Selesaikan PO};
        G --> H(Selesai);
    end
```

### Diagram Peran (Swimlane)

```mermaid
sequenceDiagram
    participant Admin
    participant Supplier
    participant Operator

    Admin->>+Supplier: Membuat & Mengirim Purchase Order (PO)
    Supplier-->>-Admin: Menerima PO
    Supplier->>Supplier: Menyetujui PO
    Supplier->>+Operator: Mengirim Barang (Status PO: Dikirim)
    Operator-->>-Supplier: Menerima Notifikasi Pengiriman
    Operator->>Operator: Menerima Fisik & Memindai Barang
    Operator->>Operator: Menempatkan di Lokasi Penyimpanan
    Operator->>Admin: Menyelesaikan PO (Status PO: Diterima)
    Admin-->>Operator: -
```

---

## 2. Flowchart Proses Pengisian Ulang Toko (Store Replenishment)

```mermaid
graph TD
    subgraph "Proses Permintaan Barang Toko"
        A(Mulai) --> B{Store: Buat Permintaan Produk};
        B --> C{Admin/Gudang: Tentukan Lokasi Ambil};
        C --> D{Operator: Verifikasi Stok Fisik};
        D --> E{Admin: Setujui & Kirim};
        E --> F{Store: Konfirmasi Terima Barang};
        F --> G(Selesai);
    end
```

### Diagram Peran (Swimlane)

```mermaid
sequenceDiagram
    participant Store as Toko
    participant Warehouse as Admin/Gudang
    participant Operator
    participant Admin

    Store->>+Warehouse: Membuat Permintaan Produk
    Warehouse-->>-Store: Menerima Permintaan
    Warehouse->>+Operator: Menentukan Lokasi & Tugaskan
    Operator-->>-Warehouse: Menerima Tugas
    Operator->>Operator: Verifikasi Ketersediaan Stok
    Operator->>+Admin: Mengajukan untuk Persetujuan
    Admin-->>-Operator: Menerima Permintaan
    Admin->>+Store: Menyetujui & Mengirim Barang
    Store-->>-Admin: Menerima Barang
    Store->>Store: Konfirmasi Penerimaan
```
