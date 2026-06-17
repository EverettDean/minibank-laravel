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

    // --- 9. INISIALISASI NOTIFIKASI ---
    checkNotifications();
    setInterval(checkNotifications, 30000); // Cek setiap 30 detik
});

// --- FUNGSI GLOBAL ---

// Logika Notifikasi Terpusat
function checkNotifications() {
    fetch("/notifications/check", {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((res) => res.json())
        .then((data) => {
            // Update badge kelas .notif-badge (di sidebar/bell)
            const badges = document.querySelectorAll(".notif-badge");
            badges.forEach((badge) => {
                badge.innerText = data.count > 0 ? data.count : "";
                badge.style.display = data.count > 0 ? "block" : "none";
            });

            // Update ID notif-count (di navbar)
            const badgeById = document.getElementById("notif-count");
            if (badgeById) {
                badgeById.innerText = data.count > 0 ? data.count : "";
                badgeById.style.display =
                    data.count > 0 ? "inline-block" : "none";
            }
        })
        .catch((err) => console.error("Gagal cek notifikasi:", err));
}

// Logika Tandai Semua Dibaca
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
