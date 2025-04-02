document.getElementById('excelFile').addEventListener('change', handleFile);
document.getElementById('toggleTableButton').addEventListener('click', toggleTable);

let tableContainer = document.getElementById('tableContainer');

// Fonction pour afficher/masquer le tableau
function toggleTable() {
    if (tableContainer.classList.contains('hidden')) {
        tableContainer.classList.remove('hidden');
        document.getElementById('toggleTableButton').textContent = 'Masquer le tableau';
    } else {
        tableContainer.classList.add('hidden');
        document.getElementById('toggleTableButton').textContent = 'Afficher le tableau';
    }
}

// Fonction pour gérer le fichier Excel
function handleFile(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();

    reader.onload = function (e) {
        const data = new Uint8Array(e.target.result);
        const workbook = XLSX.read(data, { type: 'array' });

        // Lecture de la première feuille
        const sheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[sheetName];

        // Conversion des données en HTML
        const tableHTML = XLSX.utils.sheet_to_html(worksheet, { editable: true });
        document.getElementById('excelTable').innerHTML = tableHTML;
    };

    reader.readAsArrayBuffer(file);
}
