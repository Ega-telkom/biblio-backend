# REST API untuk Biblio

## Autentikasi
- **User** → Firebase login di Android → dapat ID Token → kirim ke `POST /api/auth/firebase` → dapat Sanctum token → pakai untuk request selanjutnya
- **Admin** → `POST /api/auth/login` pakai email/password
- **Logout** → hapus Sanctum token via `POST /api/auth/logout` — berlaku untuk keduanya