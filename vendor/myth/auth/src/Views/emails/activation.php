<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
   <p>Halo,</p>

   <!-- <p>Terima kasih telah mendaftar di Sistem Manajemen Aset - Peminjaman Kendaraan Bus Kementerian PUPR.</p> -->

   <p>Untuk dapat mengakses sistem dan melakukan peminjaman kendaraan bus, silakan aktifkan akun Anda dengan mengklik tombol di bawah ini:</p>

   <p style="text-align: center; margin: 30px 0;">
       <a href="<?= url_to('activate-account') . '?token=' . $hash ?>" 
          style="display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px;">
          Aktifkan Akun
       </a>
   </p>

   <p>Jika tombol di atas tidak berfungsi, Anda dapat menyalin dan membuka tautan berikut di browser Anda:</p>
   <p style="word-break: break-all; color: #666;"><?= url_to('activate-account') . '?token=' . $hash ?></p>

   <p>Harap diperhatikan:</p>
   <ul>
       <li>Link aktivasi ini hanya berlaku selama 24 jam</li>
       <li>Setelah aktivasi berhasil, Anda dapat langsung login dan menggunakan sistem</li>
   </ul>

   <p><strong>Catatan:</strong> Jika Anda tidak merasa mendaftar di Sistem Manajemen Aset Kementerian Pekerjaan Umum, mohon untuk abaikan email ini.</p>

   <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">

   <!-- <p style="color: #666; font-size: 12px;">Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p> -->
   <p style="color: #666; font-size: 12px;">Kementerian Pekerjaan Umum<br>
   Sekretariat Jenderal<br>
   Biro Umum</p>
</div>