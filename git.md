# Dokumentasi Git

## Remote & Branch

-   Branch utama: `main`
-   Remote: `origin` → `https://github.com/retriyuselli/update5Dec25maknakreatif.id.git`

```bash
# cek branch aktif
git branch --show-current
# cek remote
git remote -v
```

## Status Perubahan

-   Cek perubahan lokal sebelum commit.

```bash
git status -s
# ringkas 50 baris pertama
git status -s | head -n 50
```

## Staging & Commit

-   Tahapan standar untuk menyimpan perubahan.

```bash
# stage file tertentu
git add path/ke/file
# stage semua perubahan ter-tracked
git add -A
# buat commit dengan pesan jelas
git commit -m "deskripsi singkat perubahan"git status -s
```

## Sinkronisasi Perubahan

-   Tarik perubahan terbaru dan dorong perubahan lokal.

```bash
# ambil dan merge dari remote
git pull origin main
# dorong commit ke remote
git push origin main
```

## Riwayat & Review

-   Tinjau riwayat commit.

```bash
git log -n 5 --oneline
git diff --stat
```

## Branch Fitur

-   Buat branch fitur untuk perubahan besar agar rapi.

```bash
# buat dan pindah ke branch fitur
git checkout -b feature/nama-fitur
# dorong branch baru
git push -u origin feature/nama-fitur
```

## Menangani Konflik Merge

-   Saat konflik, perbaiki file yang bertanda konflik lalu commit.

```bash
# setelah perbaikan
git add path/yang/diperbaiki
git commit -m "resolve merge conflict"
```

## Stash (Simpan Sementara)

-   Simpan perubahan sementara tanpa commit.

```bash
git stash
git stash list
git stash pop
```

## Mengabaikan File Ter-generated

-   Banyak file di `storage/framework/views/` sebaiknya tidak dilacak.
-   Tambahkan pola ini ke `.gitignore` (jika belum):

```
storage/
bootstrap/cache/
vendor/
node_modules/
.env
```

## Tag Rilis (Opsional)

```bash
# buat tag rilis
git tag -a v1.0.0 -m "rilis 1.0.0"
# dorong tag
git push origin v1.0.0
```

## Tips

-   Gunakan pesan commit yang deskriptif.
-   Pisahkan perubahan besar ke dalam branch fitur.
-   Jalankan `git status` dan `git log` secara rutin untuk memantau.
