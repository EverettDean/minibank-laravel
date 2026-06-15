document.addEventListener("DOMContentLoaded", function () {
    // --- 1. DEKLARASI ELEMEN ---
    const dropdownKelas = document.getElementById("dropdownKelas");
    const tombolProfile = document.getElementById("profileDropdown");
    const menuProfile = document.getElementById("dropdownMenu");
    const notifDropdown = document.getElementById("notif-dropdown");
    const btnLogout = document.getElementById("btn-logout-navbar");
    const logoutForm = document.getElementById("logout-form-action");
    const burgerBtn = document.getElementById("sidebarToggle");
    const closeBtn = document.getElementById("closeSidebar");
    const sidebar = document.querySelector(".sidebar");
    const searchInput = document.getElementById("searchInput");
    const searchForm = document.getElementById("searchForm");

    // --- 2. LOGIKA DROPDOWN KELAS ---
    if (dropdownKelas) {
        const selected = dropdownKelas.querySelector(".dropdown-selected");
        const options = dropdownKelas.querySelectorAll(".dropdown-options li");

        selected.addEventListener("click", function (e) {
            e.stopPropagation();
            dropdownKelas.classList.toggle("active");
            if (menuProfile) menuProfile.classList.remove("show");
            if (notifDropdown) notifDropdown.style.display = "none";
        });

        options.forEach((option) => {
            option.addEventListener("click", function () {
                selected.querySelector("span").innerText = this.innerText;
                dropdownKelas.classList.remove("active");
            });
        });
    }

    // --- 3. LOGIKA DROPDOWN PROFILE ---
    if (tombolProfile && menuProfile) {
        tombolProfile.addEventListener("click", function (event) {
            event.stopPropagation();
            menuProfile.classList.toggle("show");
            if (dropdownKelas) dropdownKelas.classList.remove("active");
            if (notifDropdown) notifDropdown.style.display = "none";
        });
    }

    // --- 4. LOGIKA TOGGLE NOTIFIKASI ---
    window.toggleNotif = function (event) {
        if (event) event.stopPropagation();
        if (notifDropdown) {
            const isVisible = notifDropdown.style.display === "block";
            notifDropdown.style.display = isVisible ? "none" : "block";

            if (dropdownKelas) dropdownKelas.classList.remove("active");
            if (menuProfile) menuProfile.classList.remove("show");
        }
    };

    // --- 5. LOGIKA SIDEBAR ---
    if (burgerBtn && sidebar) {
        burgerBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            sidebar.classList.add("active");
            if (menuProfile) menuProfile.classList.remove("show");
            if (notifDropdown) notifDropdown.style.display = "none";
        });
    }

    if (closeBtn && sidebar) {
        closeBtn.addEventListener("click", () =>
            sidebar.classList.remove("active"),
        );
    }

    // --- 6. LOGIKA KLIK DI LUAR (CLOSE) ---
    window.addEventListener("click", function (event) {
        if (notifDropdown && !event.target.closest(".notif-wrapper"))
            notifDropdown.style.display = "none";
        if (dropdownKelas) dropdownKelas.classList.remove("active");
        if (menuProfile) menuProfile.classList.remove("show");

        if (
            sidebar &&
            sidebar.classList.contains("active") &&
            !sidebar.contains(event.target) &&
            (!burgerBtn || !burgerBtn.contains(event.target))
        ) {
            sidebar.classList.remove("active");
        }
    });

    // --- 7. LOGIKA LOGOUT ---
    if (btnLogout && logoutForm) {
        btnLogout.addEventListener("click", (e) => {
            e.preventDefault();
            logoutForm.submit();
        });
    }

    // --- 8. LOGIKA AUTO-SEARCH ---
    if (searchInput && searchForm) {
        let timeout = null;
        searchInput.addEventListener("keyup", () => {
            clearTimeout(timeout);
            timeout = setTimeout(() => searchForm.submit(), 500);
        });
    }
});

// --- 9. LOGIKA TANDAI SEMUA DIBACA ---
function tandaiSemuaDibaca(btn) {
    const originalText = btn.innerHTML;
    const targetUrl = btn.getAttribute("data-url");
    const csrfToken = btn.getAttribute("data-token");

    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
    btn.disabled = true;

    fetch(targetUrl, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": csrfToken,
            "Content-Type": "application/json",
            Accept: "application/json",
        },
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.success) {
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Berhasil!';
                btn.style.backgroundColor = "#48bb78";
                setTimeout(() => location.reload(), 800);
            }
        })
        .catch((err) => {
            console.error("Error:", err);
            alert("Terjadi kesalahan.");
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
}

// --- 10. LOGIKA AUTO-REFRESH NOTIFIKASI (POLLING) ---
function autoCheckNotifikasi() {
    // untuk debug js
    console.log("Mengecek notifikasi baru..");
    fetch("/notifications/check")
        .then((res) => res.json())
        .then((data) => {
            const badge = document.querySelector(".notif-badge");

            if (data.count > 0) {
                // Jika sudah ada badge, update angkanya
                if (badge) {
                    badge.innerText = data.count;
                    badge.style.display = "block";
                } else {
                    // Jika belum ada badge, tambahkan secara dinamis
                    const bell = document.querySelector(".fa-bell");
                    const newBadge = document.createElement("span");
                    newBadge.className = "notif-badge";
                    newBadge.style.cssText =
                        "position: absolute; top: -8px; right: -8px; background: #e53e3e; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; font-weight: bold;";
                    newBadge.innerText = data.count;
                    bell.parentElement.appendChild(newBadge);
                }
            } else {
                // Jika tidak ada notif baru, sembunyikan badge
                if (badge) badge.style.display = "none";
            }
        })
        .catch((err) => console.error("Gagal cek notifikasi:", err));
}

// Jalankan pengecekan setiap 10 detik (30000 milidetik)
setInterval(autoCheckNotifikasi, 10000);
