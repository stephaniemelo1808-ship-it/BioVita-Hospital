
function toggleSidebar() {
    const sidebar = document.getElementById("mySidebar");
    const mainContent = document.getElementById("mainContent");
    sidebar.classList.toggle("collapsed");
    mainContent.classList.toggle("expanded");
}

function openTab(evt, tabId) {
    const tabContents = document.querySelectorAll('.tab-content');
    const tabButtons = document.querySelectorAll('.tab-btn');

    tabContents.forEach(content => content.classList.remove('active'));
    tabButtons.forEach(btn => btn.classList.remove('active'));

    document.getElementById(tabId).classList.add('active');
    
    if (evt) {
        evt.currentTarget.classList.add('active');
    }
}

function openTabById(tabId) {
    openTab(null, tabId);
    
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(btn => {
        if (btn.getAttribute('onclick') && btn.getAttribute('onclick').includes(tabId)) {
            btn.classList.add('active');
        }
    });
}

function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    if (!toast) return;
    toast.textContent = message;
    toast.className = 'toast show ' + type;
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

function exportarDashboard() {
    showToast('Dados exportados com sucesso!', 'success');
}


function abrirOverlayReceituario() {
    const overlay = document.getElementById('receituarioOverlay');
    if (overlay) overlay.classList.add('active');
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

    let itemsHTML = '';
    
    lista.forEach(item => {
        const nome = item.querySelector('.med-nome')?.textContent || '';
        const dose = item.querySelector('.med-dose')?.textContent || '';
        if (nome !== 'Selecione um medicamento') {
            itemsHTML += `<li><strong>${nome}</strong><br>${dose}</li>`;
        }
    });

    if (itemsHTML === '') {
        previewList.innerHTML = '<li>Sem prescrições adicionadas.</li>';
    } else {
        previewList.innerHTML = itemsHTML;
    }

    const observacoesTextarea = document.getElementById('observacoes_receituario');
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
    if (overlay) overlay.classList.remove('print-mode');
}