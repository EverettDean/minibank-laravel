document.addEventListener("DOMContentLoaded", function () {
    // --- 1. DEKLARASI ELEMEN ---
    const dropdownKelas = document.getElementById("dropdownKelas");
    const tombolProfile = document.getElementById("profileDropdown");
    const menuProfile = document.getElementById("dropdownMenu");

    // Notifikasi
    const notifDropdown = document.getElementById("notif-dropdown");
    const badge = document.querySelector(".notif-badge");

    // Logout
    const btnLogout = document.getElementById("btn-logout-navbar");
    const logoutForm = document.getElementById("logout-form-action");

    // Sidebar & Burger Menu (Responsif)
    const burgerBtn = document.getElementById("sidebarToggle");
    const closeBtn = document.getElementById("closeSidebar");
    const sidebar = document.querySelector(".sidebar");

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

    // --- 4. LOGIKA DROPDOWN NOTIFIKASI ---
    window.toggleNotif = function (event) {
        if (event) event.stopPropagation();

        if (notifDropdown) {
            const isVisible = notifDropdown.style.display === "block";
            notifDropdown.style.display = isVisible ? "none" : "block";

            if (dropdownKelas) dropdownKelas.classList.remove("active");
            if (menuProfile) menuProfile.classList.remove("show");

            if (!isVisible && badge) {
                fetch("/nasabah/notifikasi/read-all", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                        "Content-Type": "application/json",
                    },
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) badge.style.display = "none";
                    })
                    .catch((error) => console.error("Error:", error));
            }
        }
    };

    // --- 5. LOGIKA BURGER MENU & SIDEBAR (RESPONSIF HP) ---
    // Buka sidebar saat burger ditekan
    if (burgerBtn && sidebar) {
        burgerBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            sidebar.classList.add("active");

            // Tutup dropdown lain agar tidak menumpuk
            if (menuProfile) menuProfile.classList.remove("show");
            if (notifDropdown) notifDropdown.style.display = "none";
        });
    }

    // Tutup sidebar saat tombol X ditekan
    if (closeBtn && sidebar) {
        closeBtn.addEventListener("click", function () {
            sidebar.classList.remove("active");
        });
    }

    // --- 6. LOGIKA GLOBAL (Klik di luar elemen untuk menutup) ---
    window.addEventListener("click", function (event) {
        // Tutup Notif
        if (notifDropdown && !event.target.closest(".notif-wrapper")) {
            notifDropdown.style.display = "none";
        }
        // Tutup Kelas & Profile
        if (dropdownKelas) dropdownKelas.classList.remove("active");
        if (menuProfile) menuProfile.classList.remove("show");

        // Tutup Sidebar (jika klik di luar sidebar dan bukan pada tombol burger)
        if (sidebar && sidebar.classList.contains("active")) {
            if (
                !sidebar.contains(event.target) &&
                (!burgerBtn || !burgerBtn.contains(event.target))
            ) {
                sidebar.classList.remove("active");
            }
        }
    });

    // --- 7. LOGIKA LOGOUT ---
    if (btnLogout && logoutForm) {
        btnLogout.addEventListener("click", function (e) {
            e.preventDefault();
            logoutForm.submit();
        });
    }

    // --- 8. LOGIKA AUTO-SEARCH (Ditambahkan oleh Dean) ---
    const searchInput = document.getElementById("searchInput");
    const searchForm = document.getElementById("searchForm");

    if (searchInput && searchForm) {
        let timeout = null;
        searchInput.addEventListener("keyup", function () {
            clearTimeout(timeout);
            // Memberikan jeda 500ms setelah user berhenti mengetik sebelum submit
            timeout = setTimeout(function () {
                searchForm.submit();
            }, 500);
        });
    }
});
