    <?php
        $activePage = basename($_SERVER['PHP_SELF']);
        $basePath = dirname($_SERVER['PHP_SELF']);
        $root = basename($basePath) === 'includes' ? '../' : '';
    ?>

    <aside class="sidebar" id="mySidebar">
        <div class="toggle-container">
            <button class="toggle-btn" onclick="toggleSidebar()">
                <i class='bx bx-menu'></i>
            </button>
        </div>

        <div class="sidebar-logo">
            <img src="<?= $root ?>includes/img/logo_biovita.png" alt="Logo Bio Vita" class="main-logo">
            <h2 class="logo-text">MedSystem</h2>
        </div>

        <nav>
            <a href="<?= $root ?>medico.php" class="<?= $activePage === 'medico.php' ? 'active' : '' ?>">
                <i class='bx bx-plus-medical'></i>
                <span>Área Médica</span>
            </a>
            <a href="<?= $root ?>relatorios.php" class="<?= $activePage === 'relatorios.php' ? 'active' : '' ?>">
                <i class='bx bx-file'></i>
                <span>Relatórios</span>
            </a>
            <a href="<?= $root ?>../index.php" class="sair">
                <i class='bx bx-log-out'></i>
                <span>Sair</span>
            </a>
        </nav>
    </aside>
    