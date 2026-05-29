// ============================================================
// SISTEM PELAPORAN SAMPAH LIAR — JAVASCRIPT UTAMA
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // ---- DETEKSI LOKASI GPS ----
    const btnLoc    = document.getElementById('btn-get-loc');
    const locStatus = document.getElementById('loc-status');
    const inputLat  = document.getElementById('input-lat');
    const inputLng  = document.getElementById('input-lng');
    const mapDiv    = document.getElementById('map-preview');

    if (btnLoc) {
        btnLoc.addEventListener('click', function () {
            if (!navigator.geolocation) {
                showLocStatus('Browser Anda tidak mendukung GPS.', 'error');
                return;
            }
            btnLoc.disabled = true;
            btnLoc.textContent = '⏳ Mendeteksi...';
            showLocStatus('Mengakses GPS, harap tunggu...', 'info');

            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    const lat = pos.coords.latitude.toFixed(6);
                    const lng = pos.coords.longitude.toFixed(6);
                    inputLat.value = lat;
                    inputLng.value = lng;
                    showLocStatus('✅ Lokasi berhasil terdeteksi!', 'success');
                    btnLoc.textContent = '📡 Perbarui Lokasi';
                    btnLoc.disabled = false;
                    tampilkanPeta(lat, lng);
                },
                function (err) {
                    const pesan = {
                        1: 'Akses lokasi ditolak. Izinkan browser mengakses lokasi.',
                        2: 'Posisi tidak tersedia. Coba lagi.',
                        3: 'Waktu habis. Pastikan GPS aktif.',
                    };
                    showLocStatus('❌ ' + (pesan[err.code] || err.message), 'error');
                    btnLoc.textContent = '📡 Coba Lagi';
                    btnLoc.disabled = false;
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        });
    }

    function showLocStatus(msg, type) {
        if (!locStatus) return;
        locStatus.textContent = msg;
        locStatus.style.color = type === 'success' ? '#2e7d32'
                               : type === 'error'   ? '#c62828'
                               : '#1565c0';
    }

    function tampilkanPeta(lat, lng) {
        if (!mapDiv) return;
        const bbox = [
            parseFloat(lng) - 0.006,
            parseFloat(lat) - 0.006,
            parseFloat(lng) + 0.006,
            parseFloat(lat) + 0.006,
        ].join('%2C');
        mapDiv.innerHTML =
            '<iframe width="100%" height="260" frameborder="0" scrolling="no" ' +
            'src="https://www.openstreetmap.org/export/embed.html?bbox=' + bbox +
            '&layer=mapnik&marker=' + lat + '%2C' + lng +
            '" style="border:none;border-radius:6px;display:block"></iframe>';
        mapDiv.style.display = 'block';
    }

    // ---- PREVIEW FOTO ----
    const inputFoto  = document.getElementById('input-foto');
    const previewImg = document.getElementById('foto-preview');

    if (inputFoto && previewImg) {
        inputFoto.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            // Validasi tipe
            const allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (!allowed.includes(file.type)) {
                alert('Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.');
                this.value = '';
                return;
            }

            // Validasi ukuran (5 MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 5 MB.');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    }

    // ---- ANIMASI BAR CHART ----
    const fills = document.querySelectorAll('.bar-fill[data-width]');
    if (fills.length) {
        // Mulai dari 0 lalu animate ke target
        fills.forEach(el => { el.style.width = '0'; });
        requestAnimationFrame(function () {
            setTimeout(function () {
                fills.forEach(el => {
                    el.style.width = el.dataset.width + '%';
                });
            }, 100);
        });
    }

    // ---- KONFIRMASI HAPUS ----
    document.querySelectorAll('.btn-hapus').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!confirm('Yakin ingin menghapus laporan ini? Tindakan tidak dapat dibatalkan.')) {
                e.preventDefault();
            }
        });
    });

});
