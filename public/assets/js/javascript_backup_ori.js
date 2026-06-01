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

            // Tutup menu lain
            if (dropdownKelas) dropdownKelas.classList.remove("active");
            if (menuProfile) menuProfile.classList.remove("show");

            // Jika dropdown dibuka dan ada badge, tandai sebagai dibaca
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
                        if (data.success) {
                            badge.style.display = "none"; // Sembunyikan badge secara instan
                        }
                    })
                    .catch((error) => console.error("Error:", error));
            }
        }
    };

    // --- 5. LOGIKA GLOBAL (Klik di luar untuk menutup semua) ---
    window.addEventListener("click", function (event) {
        if (notifDropdown && !event.target.closest(".notif-wrapper")) {
            notifDropdown.style.display = "none";
        }
        if (dropdownKelas) dropdownKelas.classList.remove("active");
        if (menuProfile) menuProfile.classList.remove("show");
    });

    // --- 6. LOGIKA LOGOUT ---
    if (btnLogout && logoutForm) {
        btnLogout.addEventListener("click", function (e) {
            e.preventDefault();
            logoutForm.submit();
        });
    }
});
