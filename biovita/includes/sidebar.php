<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<aside class="sidebar" id="mySidebar">
    <div class="toggle-container">
        <button class="toggle-btn" onclick="toggleSidebar()">
            <i class='bx bx-menu'></i>
        </button>
    </div>

    <div class="sidebar-logo">
        <img src="img/logo_biovita.png" alt="Logo Bio Vita" class="main-logo">
        <h2 class="logo-text">MedSystem</h2>
    </div>

    <nav>
        <a href="dashboard.php">
            <i class='bx bx-grid-alt'></i>
            <span>Painel de Controle</span>
        </a>

        <a href="usuarios.php">
            <i class='bx bx-user-circle'></i>
            <span>Usuários</span>
        </a>

        <a href="pacientes.php">
            <i class='bx bx-clipboard'></i>
            <span>Pacientes</span>
        </a>

        <a href="index.php" style="margin-top: 50px; color: #ffbaba;">
            <i class='bx bx-log-out'></i>
            <span>Sair</span>
        </a>
    </nav>
</aside>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById("mySidebar");
        const mainContent = document.querySelector(".main-content");

        sidebar.classList.toggle("collapsed");
        if (mainContent) {
            mainContent.classList.toggle("expanded");
        }
    }
</script>