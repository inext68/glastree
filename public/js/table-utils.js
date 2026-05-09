/**
 * Table Utils - Gestione avanzata tabelle
 * Include: scelta colonne, ordinamento, filtri, ricerca, export, salvataggio vista
 */

class TableUtils {
    constructor(tableId, options = {}) {
        this.tableId = tableId;
        this.table = document.getElementById(tableId);
        this.options = options;
        
        this.columns = options.columns || [];
        this.currentPage = 1;
        this.perPage = options.perPage || 20;
        
        // Carica stato salvato se presente
        this.loadState();
        
        // Inizializza eventi
        this.initEvents();
    }
    
    loadState() {
        // Leggi parametri URL
        const urlParams = new URLSearchParams(window.location.search);
        this.vistaId = urlParams.get('vista_id');
        
        if (this.vistaId) {
            this.loadVista(this.vistaId);
        }
    }
    
    async loadVista(vistaId) {
        try {
            const response = await fetch(`/viste/${vistaId}/json`);
            const data = await response.json();
            
            if (data.colonne_visibili) {
                this.setVisibleColumns(data.colonne_visibili);
            }
            if (data.colonne_ordinamento) {
                this.setOrder(data.colonne_ordinamento);
            }
            if (data.filtri) {
                this.setFilters(data.filtri);
            }
            if (data.ricerca) {
                document.getElementById('table-search').value = data.ricerca;
                this.search(data.ricerca);
            }
        } catch (e) {
            console.error('Errore caricamento vista:', e);
        }
    }
    
    initEvents() {
        // Pulsante impostazioni tabella
        document.getElementById('table-settings-btn')?.addEventListener('click', () => this.showSettingsModal());
        
        // Ricerca
        document.getElementById('table-search')?.addEventListener('input', (e) => {
            this.search(e.target.value);
        });
        
        // Ordinamento click su header
        this.table?.querySelectorAll('th[data-sortable]').forEach(th => {
            th.addEventListener('click', () => this.toggleSort(th.dataset.column));
        });
        
        // Checkbox colonne
        document.querySelectorAll('.column-toggle').forEach(cb => {
            cb.addEventListener('change', () => this.toggleColumn(cb.dataset.column, cb.checked));
        });
        
        // Salva vista
        document.getElementById('save-vista-btn')?.addEventListener('click', () => this.saveVista());
        
        // Esporta
        document.getElementById('export-csv-btn')?.addEventListener('click', () => this.exportCSV());
        document.getElementById('export-pdf-btn')?.addEventListener('click', () => this.exportPDF());
        
        // Applica filtri
        document.getElementById('apply-filters-btn')?.addEventListener('click', () => this.applyFilters());
    }
    
    showSettingsModal() {
        const modal = document.getElementById('table-settings-modal');
        if (modal) {
            $(modal).modal('show');
        }
    }
    
    toggleColumn(column, visible) {
        const colIndex = this.columns.findIndex(c => c.key === column);
        if (colIndex >= 0) {
            this.columns[colIndex].visible = visible;
            this.render();
        }
    }
    
    setVisibleColumns(visibleColumns) {
        this.columns.forEach(col => {
            col.visible = visibleColumns.includes(col.key);
        });
        this.render();
    }
    
    render() {
        this.columns.forEach(col => {
            const th = this.table?.querySelector(`th[data-column="${col.key}"]`);
            const cells = this.table?.querySelectorAll(`td:nth-child(${this.columns.indexOf(col) + 1})`);
            
            if (th) th.style.display = col.visible ? '' : 'none';
            cells?.forEach(cell => cell.style.display = col.visible ? '' : 'none');
        });
    }
    
    toggleSort(column) {
        const currentOrder = this.options.order || [];
        const existing = currentOrder.find(o => o[0] === column);
        
        if (existing) {
            existing[1] = existing[1] === 'asc' ? 'desc' : 'asc';
        } else {
            currentOrder.push([column, 'asc']);
        }
        
        this.options.order = currentOrder;
        this.sort();
    }
    
    setOrder(order) {
        this.options.order = order;
        this.sort();
    }
    
    sort() {
        const tbody = this.table?.querySelector('tbody');
        if (!tbody) return;
        
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            for (const [col, dir] of (this.options.order || [])) {
                const colIndex = this.columns.findIndex(c => c.key === col);
                if (colIndex < 0) continue;
                
                const aVal = a.querySelectorAll('td')[colIndex]?.textContent?.trim() || '';
                const bVal = b.querySelectorAll('td')[colIndex]?.textContent?.trim() || '';
                
                let cmp = aVal.localeCompare(bVal, undefined, { numeric: true });
                if (cmp !== 0) return dir === 'asc' ? cmp : -cmp;
            }
            return 0;
        });
        
        rows.forEach(row => tbody.appendChild(row));
    }
    
    search(query) {
        this.options.search = query.toLowerCase();
        this.filter();
    }
    
    setFilters(filters) {
        this.options.filters = filters;
        this.filter();
    }
    
    applyFilters() {
        const filters = [];
        document.querySelectorAll('.filter-row').forEach(row => {
            const col = row.querySelector('.filter-column')?.value;
            const op = row.querySelector('.filter-operator')?.value;
            const val = row.querySelector('.filter-value')?.value;
            if (col && val) filters.push({ column: col, operator: op, value: val });
        });
        this.options.filters = filters;
        this.filter();
    }
    
    filter() {
        this.table?.querySelectorAll('tbody tr').forEach(row => {
            const text = row.textContent.toLowerCase();
            const matchesSearch = !this.options.search || text.includes(this.options.search);
            
            let matchesFilters = true;
            if (this.options.filters) {
                for (const f of this.options.filters) {
                    const colIndex = this.columns.findIndex(c => c.key === f.column);
                    if (colIndex < 0) continue;
                    
                    const cell = row.querySelectorAll('td')[colIndex]?.textContent?.trim() || '';
                    
                    switch (f.operator) {
                        case 'contains': matchesFilters = cell.toLowerCase().includes(f.value.toLowerCase()); break;
                        case 'equals': matchesFilters = cell === f.value; break;
                        case 'starts': matchesFilters = cell.startsWith(f.value); break;
                        case 'ends': matchesFilters = cell.endsWith(f.value); break;
                        case 'gt': matchesFilters = parseFloat(cell) > parseFloat(f.value); break;
                        case 'lt': matchesFilters = parseFloat(cell) < parseFloat(f.value); break;
                    }
                    if (!matchesFilters) break;
                }
            }
            
            row.style.display = (matchesSearch && matchesFilters) ? '' : 'none';
        });
    }
    
    async saveVista() {
        const nome = document.getElementById('vista-nome')?.value;
        if (!nome) {
            alert('Inserisci un nome per la vista');
            return;
        }
        
        const data = {
            nome: nome,
            tipo: this.options.type || 'individui',
            colonne_visibili: this.columns.filter(c => c.visible).map(c => c.key),
            colonne_ordinamento: this.options.order || [],
            filtri: this.options.filters || [],
            ricerca: this.options.search || ''
        };
        
        try {
            const response = await fetch('/viste', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify(data)
            });
            
            if (response.ok) {
                alert('Vista salvata!');
                $(document.getElementById('table-settings-modal')).modal('hide');
            }
        } catch (e) {
            alert('Errore salvataggio vista');
        }
    }
    
    exportCSV() {
        const rows = [];
        const headers = this.columns.filter(c => c.visible).map(c => c.label);
        rows.push(headers.join(','));
        
        this.table?.querySelectorAll('tbody tr').forEach(row => {
            const cells = this.columns.filter(c => c.visible).map((c, i) => {
                const val = row.querySelectorAll('td')[i]?.textContent?.trim() || '';
                return `"${val.replace(/"/g, '""')}"`;
            });
            rows.push(cells.join(','));
        });
        
        const blob = new Blob([rows.join('\n')], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `export_${this.options.type || 'data'}_${new Date().toISOString().slice(0,10)}.csv`;
        a.click();
    }
    
    exportPDF() {
        alert('Export PDF in sviluppo');
    }
}

// Funzioni globali per uso inline
window.TableUtils = TableUtils;
window.initTableUtils = function(tableId, options) {
    return new TableUtils(tableId, options);
};