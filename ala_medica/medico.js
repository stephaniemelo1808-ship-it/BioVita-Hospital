
// Toggle Sidebar
function toggleSidebar() {
    const sidebar = document.getElementById("mySidebar");
    const mainContent = document.getElementById("mainContent");
    sidebar.classList.toggle("collapsed");
    mainContent.classList.toggle("expanded");
}

// Tab Navigation
function openTab(evt, tabId) {
    const tabContents = document.querySelectorAll('.tab-content');
    const tabButtons = document.querySelectorAll('.tab-btn');

    tabContents.forEach(content => content.classList.remove('active'));
    tabButtons.forEach(btn => btn.classList.remove('active'));

    document.getElementById(tabId).classList.add('active');
    evt.currentTarget.classList.add('active');

    // Re-render charts if on painel tab
    if (tabId === 'tab-painel') {
        setTimeout(initCharts, 50);
    }
}

function openTabById(tabId) {
    const tabContents = document.querySelectorAll('.tab-content');
    const tabButtons = document.querySelectorAll('.tab-btn');

    tabContents.forEach(content => content.classList.remove('active'));
    tabButtons.forEach(btn => btn.classList.remove('active'));

    document.getElementById(tabId).classList.add('active');

    // Activate corresponding button
    tabButtons.forEach(btn => {
        if (btn.getAttribute('onclick') && btn.getAttribute('onclick').includes(tabId)) {
            btn.classList.add('active');
        }
    });

    if (tabId === 'tab-painel') {
        setTimeout(initCharts, 50);
    }
}

// Select consulta
function selectConsulta(element, paciente) {
    document.querySelectorAll('.paciente-item').forEach(item => item.classList.remove('active'));
    element.classList.add('active');
}

// Toast
function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = 'toast show ' + type;
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Adicionar medicamento ao receituario
function adicionarMedicamento(nome, dose, instrucoes) {
    const lista = document.getElementById('receituarioLista');
    const item = document.createElement('div');
    item.className = 'receituario-item';
    item.innerHTML = `
                <div>
                    <div class="med-nome">${nome}</div>
                    <div class="med-dose">${dose} - ${instrucoes}</div>
                </div>
                <button onclick="removerMedicamento(this)" title="Remover"><i class='bx bx-trash'></i></button>
            `;
    lista.appendChild(item);
    showToast(`${nome} adicionado ao receituario!`, 'success');
}

function removerMedicamento(btn) {
    btn.closest('.receituario-item').remove();
    showToast('Medicamento removido!', 'info');
}


function abrirOverlayReceituario() {
    const overlay = document.getElementById('receituarioOverlay');
    if (overlay) {
        overlay.classList.add('active');
    }
}

function fecharOverlayReceituario() {
    const overlay = document.getElementById('receituarioOverlay');
    if (overlay) {
        overlay.classList.remove('active');
        overlay.classList.remove('print-mode');
    }
}

function renderReceituarioPreview() {
    const lista = document.querySelectorAll('.receituario-item');
    const previewList = document.getElementById('previewReceituarioList');
    const previewNotes = document.getElementById('previewReceituarioNotes');
    if (!previewList || !previewNotes) return;

    if (lista.length === 0) {
        previewList.innerHTML = '<li>Sem prescrições adicionadas.</li>';
    } else {
        previewList.innerHTML = Array.from(lista).map(item => {
            const nome = item.querySelector('.med-nome')?.textContent || '';
            const dose = item.querySelector('.med-dose')?.textContent || '';
            return `<li><strong>${nome}</strong><br>${dose}</li>`;
        }).join('');
    }

    const observacoesTextarea = document.querySelector('.form-group textarea');
    previewNotes.textContent = observacoesTextarea?.value.trim() || 'Nenhuma observação adicional.';
}

function imprimirReceituario() {
    const overlay = document.getElementById('receituarioOverlay');
    if (!overlay) return;
    overlay.classList.add('print-mode');
    window.print();
}

function baixarReceituarioPDF() {
    if (typeof html2pdf === 'undefined') {
        showToast('Biblioteca de PDF não carregada. Tente novamente.', 'error');
        return;
    }

    // Garantir que a preview está atualizada
    renderReceituarioPreview();

    const elemento = document.querySelector('.preview-box');
    if (!elemento) return;

    showToast('Gerando PDF do receituário...', 'info');

    const opt = {
        margin: 10,
        filename: 'Receituario_Medico.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(elemento).save().then(() => {
        showToast('Download concluído com sucesso!', 'success');
    });
}

window.onafterprint = function () {
    const overlay = document.getElementById('receituarioOverlay');
    if (overlay) {
        overlay.classList.remove('print-mode');
    }
}

function exportarDashboard() {
    showToast('Dados do dashboard exportados!', 'success');
}

// Charts
let chartSemana, chartStatus;

function initCharts() {
    // Grafico de Linha - Consultas da Semana
    const ctxSemana = document.getElementById('graficoSemana');
    if (ctxSemana) {
        if (chartSemana) chartSemana.destroy();
        const ctx = ctxSemana.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 250);
        gradient.addColorStop(0, 'rgba(44, 130, 181, 0.3)');
        gradient.addColorStop(1, 'rgba(44, 130, 181, 0)');

        chartSemana = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Segunda', 'Terca', 'Quarta', 'Quinta', 'Sexta', 'Sabado', 'Domingo'],
                datasets: [{
                    label: 'Consultas',
                    data: [6, 8, 5, 9, 7, 3, 2],
                    borderColor: '#2C82B5',
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2C82B5',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f5f5f5' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Grafico Doughnut - Status
    const ctxStatus = document.getElementById('graficoStatus');
    if (ctxStatus) {
        if (chartStatus) chartStatus.destroy();
        chartStatus = new Chart(ctxStatus.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Concluidas', 'Agendadas', 'Em Espera', 'Canceladas'],
                datasets: [{
                    data: [6, 2, 1, 1],
                    backgroundColor: ['#27ae60', '#2C82B5', '#f1c40f', '#e74c3c'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 15, font: { size: 11 } }
                    }
                }
            }
        });
    }
}

// Inicializar
document.addEventListener('DOMContentLoaded', function () {
    initCharts();
});
