# Alur Proses Bisnis Aplikasi

Aplikasi ini mengelola dua alur kerja utama yang saling berhubungan untuk manajemen inventaris dan logistik antara pemasok, gudang, dan toko.

---

## 1. Proses Pengadaan Barang dari Pemasok (Procurement)

Proses ini mengatur bagaimana barang masuk dari pemasok (supplier) ke gudang utama.

1.  **Pembuatan Pesanan Pembelian (Purchase Order - PO)**
    *   **Aktor:** Admin
    *   **Aksi:** Admin membuat Purchase Order baru melalui sistem, menunjuk produk yang dibutuhkan dan memilih pemasok yang akan dituju.

2.  **Persetujuan dan Pengiriman oleh Pemasok**
    *   **Aktor:** Pemasok (Supplier)
    *   **Aksi:** Pemasok masuk ke sistem, melihat PO yang ditugaskan, dan memberikan persetujuan. Setelah disetujui, pemasok menyiapkan barang dan melakukan 'pengiriman' melalui sistem. Stok di inventaris pemasok akan berkurang.

3.  **Penerimaan Barang di Gudang**
    *   **Aktor:** Operator Gudang
    *   **Aksi:** PO yang sudah berstatus 'dikirim' akan muncul di antrean tugas Operator. Operator menerima kiriman fisik, memindai setiap item, dan menempatkannya di lokasi penyimpanan (storage location) yang spesifik di dalam gudang. Sistem akan mencatat penambahan stok secara *real-time*.

4.  **Penyelesaian Penerimaan**
    *   **Aktor:** Operator Gudang
    *   **Aksi:** Setelah semua item dalam PO diterima dan ditempatkan, Operator menandai PO sebagai 'selesai diterima'. Proses pengadaan untuk PO tersebut selesai.

---

## 2. Proses Permintaan dan Pengiriman Barang ke Toko (Store Replenishment)

Proses ini mengatur alur permintaan barang dari toko hingga barang tersebut diterima oleh toko.

1.  **Pembuatan Permintaan Produk**
    *   **Aktor:** Pengguna Toko (Store)
    *   **Aksi:** Pengguna dari pihak toko membuat 'Permintaan Produk' (Product Request) untuk item yang mereka butuhkan.

2.  **Penentuan Lokasi Pengambilan oleh Gudang**
    *   **Aktor:** Manajer Gudang / Admin
    *   **Aksi:** Permintaan dari toko masuk ke antrean Admin/Manajer Gudang. Mereka akan memilih dari lokasi penyimpanan mana barang tersebut akan diambil untuk memenuhi permintaan.

3.  **Verifikasi Stok oleh Operator**
    *   **Aktor:** Operator Gudang
    *   **Aksi:** Setelah lokasi ditentukan, permintaan dialihkan ke antrean Operator. Operator secara fisik memeriksa ketersediaan stok di lokasi yang telah ditentukan dan melakukan 'verifikasi' di sistem untuk memastikan barang siap kirim.

4.  **Persetujuan Akhir dan Pengiriman**
    *   **Aktor:** Admin
    *   **Aksi:** Permintaan yang sudah diverifikasi kembali ke antrean Admin untuk persetujuan akhir. Setelah disetujui, sistem secara otomatis membuat catatan pengiriman (shipment) dan mengubah status permintaan menjadi 'dikirim'.

5.  **Konfirmasi Penerimaan oleh Toko**
    *   **Aktor:** Pengguna Toko (Store)
    *   **Aksi:** Setelah barang sampai di toko, pengguna toko akan masuk ke sistem dan menandai permintaan sebagai 'telah diterima' untuk menyelesaikan alur kerja.
