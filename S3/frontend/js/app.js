// Helpdesk - Application Frontend

'use strict';

// ── Endpoints API Backend ─────────────────────────────────────────────────────
const CATEGORIES_API = '../backend/api/categories.php';
const TICKETS_API    = '../backend/api/tickets.php';
const USERS_API      = '../backend/api/utilisateurs.php';

// ── État Global ───────────────────────────────────────────────────────────────
let currentSection    = 'categories'; // 'categories' | 'tickets'
let currentDeleteType = null;         // 'category' | 'ticket'
let currentDeleteId   = null;
let allCategories     = [];
let allTickets        = [];

// ═══════════════════════════════════════════════════════════════════════════════
// NAVIGATION
// ═══════════════════════════════════════════════════════════════════════════════

function navigateTo(section) {
    currentSection = section;

    // Sidebar - Mise en évidence du bouton actif
    const btnCat = document.getElementById('nav-categories');
    const btnTick = document.getElementById('nav-tickets');

    if (section === 'categories') {
        btnCat.className = 'nav-btn w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold bg-indigo-600 text-white shadow-sm transition';
        btnTick.className = 'nav-btn w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition';

        document.getElementById('page-title').textContent = 'Gestion des Catégories';
        document.getElementById('btn-top-label').textContent = 'Nouvelle Catégorie';

        showView('view-categories-liste');
        loadCategories();
    } else {
        btnCat.className = 'nav-btn w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition';
        btnTick.className = 'nav-btn w-full flex items-center space-x-3 px-3 py-2.5 rounded-lg text-sm font-semibold bg-indigo-600 text-white shadow-sm transition';

        document.getElementById('page-title').textContent = 'Gestion des Tickets';
        document.getElementById('btn-top-label').textContent = 'Nouveau Ticket';

        showView('view-tickets-liste');
        loadTickets();
    }
}

function showView(viewId) {
    document.querySelectorAll('.view-section').forEach(el => el.classList.add('hidden'));
    const target = document.getElementById(viewId);
    if (target) target.classList.remove('hidden');
}

function onAddClick() {
    if (currentSection === 'categories') {
        showAddCategoryForm();
    } else {
        showAddTicketForm();
    }
}

// ═══════════════════════════════════════════════════════════════════════════════
// CATÉGORIES - CRUD
// ═══════════════════════════════════════════════════════════════════════════════

async function loadCategories() {
    try {
        const res = await fetch(CATEGORIES_API);
        const json = await res.json();
        if (!json.success) throw new Error(json.message);

        allCategories = json.data;
        updateCategoryStats(allCategories);
        renderCategories(allCategories);
    } catch (err) {
        document.getElementById('categoriesTableBody').innerHTML = `
            <tr><td colspan="4" class="px-6 py-12 text-center text-rose-500 font-medium">
                ⚠️ Erreur de chargement des catégories : ${escHtml(err.message)}
            </td></tr>
        `;
    }
}

function updateCategoryStats(list) {
    const total = list.length;
    const totalTickets = list.reduce((acc, c) => acc + (parseInt(c.nb_tickets, 10) || 0), 0);
    document.getElementById('statTotalCat').textContent = total;
    document.getElementById('statTicketsCat').textContent = totalTickets;
    document.getElementById('filteredCatCount').textContent = total;
}

function renderCategories(list) {
    const tbody = document.getElementById('categoriesTableBody');
    if (!list.length) {
        tbody.innerHTML = `
            <tr id="emptyRow">
                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                    <span class="text-3xl block mb-2">📭</span>
                    Aucune catégorie trouvée.
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = list.map(c => {
        const nb = parseInt(c.nb_tickets, 10) || 0;
        const nbBadge = nb > 0
            ? `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${nb} ticket(s)</span>`
            : `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">0 ticket</span>`;

        const descHtml = c.description
            ? `<p class="text-xs text-slate-500 category-desc mt-0.5">${escHtml(c.description)}</p>`
            : `<p class="text-xs text-slate-400 italic category-desc mt-0.5">Aucune description</p>`;

        return `
            <tr id="row-cat-${c.id_categorie}" class="hover:bg-slate-50/80 transition duration-150">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-400">
                    #${c.id_categorie}
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-start">
                        <span class="h-8 w-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm mr-3 flex-shrink-0 mt-0.5">
                            📁
                        </span>
                        <div>
                            <span class="text-sm font-semibold text-slate-900 category-name">
                                ${escHtml(c.nom_categorie)}
                            </span>
                            ${descHtml}
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    ${nbBadge}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    <button onclick="showEditCategoryForm(${c.id_categorie})" 
                            class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold text-brand-600 bg-brand-50 hover:bg-brand-100 transition">
                        Modifier
                    </button>
                    <button onclick="confirmDeleteCategory(${c.id_categorie}, '${escAttr(c.nom_categorie)}', ${nb})" 
                            class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 transition">
                        Supprimer
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function filterCategories() {
    const input = document.getElementById('searchCategories').value.trim().toLowerCase();
    const filtered = input
        ? allCategories.filter(c =>
            c.nom_categorie.toLowerCase().includes(input) ||
            (c.description || '').toLowerCase().includes(input)
        )
        : allCategories;

    document.getElementById('filteredCatCount').textContent = filtered.length;
    renderCategories(filtered);
}

function showAddCategoryForm() {
    document.getElementById('formAddCategory').reset();
    hideAlert('addCatAlert');
    showView('view-categories-ajout');
    document.getElementById('addCatNom').focus();
}

async function showEditCategoryForm(id) {
    hideAlert('editCatAlert');
    showView('view-categories-modifier');

    try {
        const res = await fetch(`${CATEGORIES_API}?id=${id}`);
        const json = await res.json();
        if (!json.success) throw new Error(json.message);

        const c = json.data;
        document.getElementById('editCatId').value = c.id_categorie;
        document.getElementById('editCatNom').value = c.nom_categorie;
        document.getElementById('editCatDesc').value = c.description || '';
        document.getElementById('editCatBadge').textContent = `ID #${c.id_categorie} · ${c.nb_tickets || 0} ticket(s)`;
        document.getElementById('editCatNom').focus();
    } catch (err) {
        showAlert('editCatAlert', 'editCatAlertIcon', 'editCatAlertText', err.message, 'error');
    }
}

async function submitAddCategory(e) {
    e.preventDefault();
    hideAlert('addCatAlert');
    setLoading('btnSubmitAddCat', 'spinAddCat', true);

    const payload = {
        nom_categorie: document.getElementById('addCatNom').value.trim(),
        description:   document.getElementById('addCatDesc').value.trim() || null
    };

    try {
        const res = await fetch(CATEGORIES_API, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();

        if (res.ok && json.success) {
            showToast("Catégorie créée avec succès !", 'success');
            navigateTo('categories');
        } else {
            showAlert('addCatAlert', 'addCatAlertIcon', 'addCatAlertText', json.message || "Erreur de création.", 'error');
        }
    } catch (err) {
        showAlert('addCatAlert', 'addCatAlertIcon', 'addCatAlertText', "Erreur réseau avec le serveur.", 'error');
    } finally {
        setLoading('btnSubmitAddCat', 'spinAddCat', false);
    }
}

async function submitEditCategory(e) {
    e.preventDefault();
    hideAlert('editCatAlert');
    setLoading('btnSubmitEditCat', 'spinEditCat', true);

    const id = document.getElementById('editCatId').value;
    const payload = {
        nom_categorie: document.getElementById('editCatNom').value.trim(),
        description:   document.getElementById('editCatDesc').value.trim() || null
    };

    try {
        const res = await fetch(`${CATEGORIES_API}?id=${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();

        if (res.ok && json.success) {
            showToast("Catégorie mise à jour avec succès !", 'success');
            navigateTo('categories');
        } else {
            showAlert('editCatAlert', 'editCatAlertIcon', 'editCatAlertText', json.message || "Erreur de mise à jour.", 'error');
        }
    } catch (err) {
        showAlert('editCatAlert', 'editCatAlertIcon', 'editCatAlertText', "Erreur réseau avec le serveur.", 'error');
    } finally {
        setLoading('btnSubmitEditCat', 'spinEditCat', false);
    }
}

function confirmDeleteCategory(id, name, ticketCount) {
    if (ticketCount > 0) {
        showToast(`Impossible de supprimer "${name}" : ${ticketCount} ticket(s) y sont rattachés.`, 'error');
        return;
    }

    currentDeleteType = 'category';
    currentDeleteId   = id;

    document.getElementById('deleteModalTitle').textContent = 'Supprimer la catégorie';
    document.getElementById('deleteModalText').textContent = 
        `Êtes-vous sûr de vouloir supprimer la catégorie "${name}" ? Cette action est irréversible.`;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('confirmDeleteBtn').onclick = executeDelete;
}

// ═══════════════════════════════════════════════════════════════════════════════
// TICKETS - CRUD (Intégration Session 1)
// ═══════════════════════════════════════════════════════════════════════════════

async function loadTickets() {
    try {
        const res = await fetch(TICKETS_API);
        const json = await res.json();
        if (!json.success) throw new Error(json.message);

        allTickets = json.data;
        updateTicketStats(allTickets);
        renderTickets(allTickets);
    } catch (err) {
        document.getElementById('ticketsTableBody').innerHTML = `
            <tr><td colspan="7" class="px-6 py-12 text-center text-rose-500 font-medium">
                ⚠️ Erreur de chargement des tickets : ${escHtml(err.message)}
            </td></tr>
        `;
    }
}

function updateTicketStats(list) {
    const total = list.length;
    const statuts = list.reduce((acc, t) => {
        acc[t.statut] = (acc[t.statut] || 0) + 1;
        return acc;
    }, {});

    document.getElementById('statTotalTickets').textContent = total;
    document.getElementById('statOuverts').textContent = statuts['Ouvert'] || 0;
    document.getElementById('statEnCours').textContent = statuts['En cours'] || 0;
    document.getElementById('statResolus').textContent = (statuts['Résolu'] || 0) + (statuts['Fermé'] || 0);
    document.getElementById('filteredTicketCount').textContent = total;
}

const STATUT_CLASSES = {
    'Ouvert':   'bg-emerald-100 text-emerald-800',
    'En cours': 'bg-blue-100 text-blue-800',
    'Résolu':   'bg-violet-100 text-violet-800',
    'Fermé':    'bg-slate-100 text-slate-600',
};

function renderTickets(list) {
    const tbody = document.getElementById('ticketsTableBody');
    if (!list.length) {
        tbody.innerHTML = `
            <tr id="emptyRowTickets">
                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                    <span class="text-3xl block mb-2">📭</span>
                    Aucun ticket trouvé.
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = list.map(t => {
        const cls = STATUT_CLASSES[t.statut] || 'bg-slate-100 text-slate-600';
        const dateStr = t.date_creation ? t.date_creation.substring(0, 10) : '—';

        return `
            <tr id="row-ticket-${t.id_ticket}" class="hover:bg-slate-50/80 transition duration-150">
                <td class="px-5 py-4 whitespace-nowrap text-sm font-semibold text-slate-400">
                    #${t.id_ticket}
                </td>
                <td class="px-5 py-4">
                    <span class="text-sm font-semibold text-slate-900 block ticket-titre">
                        ${escHtml(t.titre)}
                    </span>
                    <p class="text-xs text-slate-500 mt-0.5 max-w-sm truncate ticket-desc">
                        ${escHtml(t.description)}
                    </p>
                </td>
                <td class="px-5 py-4 whitespace-nowrap text-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ticket-statut ${cls}">
                        ${escHtml(t.statut)}
                    </span>
                </td>
                <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-700 ticket-cat">
                    <span class="inline-flex items-center">
                        <span class="mr-1.5 text-xs">📂</span>
                        ${escHtml(t.nom_categorie || '—')}
                    </span>
                </td>
                <td class="px-5 py-4 whitespace-nowrap text-sm text-slate-700 ticket-user">
                    ${escHtml(t.nom_complet_utilisateur || '—')}
                </td>
                <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500">
                    ${dateStr}
                </td>
                <td class="px-5 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    <button onclick="showEditTicketForm(${t.id_ticket})" 
                            class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold text-brand-600 bg-brand-50 hover:bg-brand-100 transition">
                        Modifier
                    </button>
                    <button onclick="confirmDeleteTicket(${t.id_ticket}, '${escAttr(t.titre)}')" 
                            class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 transition">
                        Supprimer
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function filterTickets() {
    const input = document.getElementById('searchTickets').value.trim().toLowerCase();
    const filtered = input
        ? allTickets.filter(t =>
            t.titre.toLowerCase().includes(input) ||
            t.description.toLowerCase().includes(input) ||
            t.statut.toLowerCase().includes(input) ||
            (t.nom_categorie || '').toLowerCase().includes(input) ||
            (t.nom_complet_utilisateur || '').toLowerCase().includes(input)
        )
        : allTickets;

    document.getElementById('filteredTicketCount').textContent = filtered.length;
    renderTickets(filtered);
}

async function populateSelects(catSelectId, userSelectId, selectedCat = null, selectedUser = null) {
    try {
        const [catRes, userRes] = await Promise.all([
            fetch(CATEGORIES_API),
            fetch(USERS_API),
        ]);
        const [catJson, userJson] = await Promise.all([catRes.json(), userRes.json()]);

        const catSelect = document.getElementById(catSelectId);
        const userSelect = document.getElementById(userSelectId);

        catSelect.innerHTML = '<option value="">— Choisir la catégorie —</option>';
        userSelect.innerHTML = '<option value="">— Choisir l\'utilisateur —</option>';

        if (catJson.success) {
            catJson.data.forEach(c => {
                const opt = new Option(c.nom_categorie, c.id_categorie, false, String(c.id_categorie) === String(selectedCat));
                catSelect.add(opt);
            });
        }
        if (userJson.success) {
            userJson.data.forEach(u => {
                const label = `${u.prenom} ${u.nom} (${u.role})`;
                const opt = new Option(label, u.id_utilisateur, false, String(u.id_utilisateur) === String(selectedUser));
                userSelect.add(opt);
            });
        }
    } catch (err) {
        console.error("Erreur chargement des selects :", err);
    }
}

async function showAddTicketForm() {
    document.getElementById('formAddTicket').reset();
    hideAlert('addTicketAlert');
    showView('view-tickets-ajout');
    await populateSelects('addTicketCat', 'addTicketUser');
    document.getElementById('addTicketTitre').focus();
}

async function showEditTicketForm(id) {
    hideAlert('editTicketAlert');
    showView('view-tickets-modifier');

    try {
        const res = await fetch(`${TICKETS_API}?id=${id}`);
        const json = await res.json();
        if (!json.success) throw new Error(json.message);

        const t = json.data;
        document.getElementById('editTicketId').value = t.id_ticket;
        document.getElementById('editTicketTitre').value = t.titre;
        document.getElementById('editTicketDesc').value = t.description;

        await populateSelects('editTicketCat', 'editTicketUser', t.id_categorie, t.id_utilisateur);

        document.getElementById('editTicketStatut').value = t.statut;
        document.getElementById('editTicketTitre').focus();
    } catch (err) {
        showAlert('editTicketAlert', 'editTicketAlertIcon', 'editTicketAlertText', err.message, 'error');
    }
}

async function submitAddTicket(e) {
    e.preventDefault();
    hideAlert('addTicketAlert');
    setLoading('btnSubmitAddTicket', 'spinAddTicket', true);

    const payload = {
        titre:          document.getElementById('addTicketTitre').value.trim(),
        description:    document.getElementById('addTicketDesc').value.trim(),
        id_categorie:   parseInt(document.getElementById('addTicketCat').value, 10),
        statut:         document.getElementById('addTicketStatut').value,
        id_utilisateur: parseInt(document.getElementById('addTicketUser').value, 10)
    };

    try {
        const res = await fetch(TICKETS_API, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();

        if (res.ok && json.success) {
            showToast("Ticket créé avec succès !", 'success');
            navigateTo('tickets');
        } else {
            showAlert('addTicketAlert', 'addTicketAlertIcon', 'addTicketAlertText', json.message || "Erreur de création.", 'error');
        }
    } catch (err) {
        showAlert('addTicketAlert', 'addTicketAlertIcon', 'addTicketAlertText', "Erreur réseau avec le serveur.", 'error');
    } finally {
        setLoading('btnSubmitAddTicket', 'spinAddTicket', false);
    }
}

async function submitEditTicket(e) {
    e.preventDefault();
    hideAlert('editTicketAlert');
    setLoading('btnSubmitEditTicket', 'spinEditTicket', true);

    const id = document.getElementById('editTicketId').value;
    const payload = {
        titre:          document.getElementById('editTicketTitre').value.trim(),
        description:    document.getElementById('editTicketDesc').value.trim(),
        id_categorie:   parseInt(document.getElementById('editTicketCat').value, 10),
        statut:         document.getElementById('editTicketStatut').value,
        id_utilisateur: parseInt(document.getElementById('editTicketUser').value, 10)
    };

    try {
        const res = await fetch(`${TICKETS_API}?id=${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();

        if (res.ok && json.success) {
            showToast("Ticket mis à jour !", 'success');
            navigateTo('tickets');
        } else {
            showAlert('editTicketAlert', 'editTicketAlertIcon', 'editTicketAlertText', json.message || "Erreur de modification.", 'error');
        }
    } catch (err) {
        showAlert('editTicketAlert', 'editTicketAlertIcon', 'editTicketAlertText', "Erreur réseau avec le serveur.", 'error');
    } finally {
        setLoading('btnSubmitEditTicket', 'spinEditTicket', false);
    }
}

function confirmDeleteTicket(id, titre) {
    currentDeleteType = 'ticket';
    currentDeleteId   = id;

    document.getElementById('deleteModalTitle').textContent = 'Supprimer le ticket';
    document.getElementById('deleteModalText').textContent = 
        `Êtes-vous sûr de vouloir supprimer le ticket "${titre}" ? Cette action est irréversible.`;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('confirmDeleteBtn').onclick = executeDelete;
}

// ═══════════════════════════════════════════════════════════════════════════════
// SUPPRESSION PARTAGÉE (Modale)
// ═══════════════════════════════════════════════════════════════════════════════

async function executeDelete() {
    if (!currentDeleteId) return;

    const btn = document.getElementById('confirmDeleteBtn');
    const spinner = document.getElementById('deleteBtnSpinner');
    btn.disabled = true;
    spinner.classList.remove('hidden');

    const url = currentDeleteType === 'category'
        ? `${CATEGORIES_API}?id=${currentDeleteId}`
        : `${TICKETS_API}?id=${currentDeleteId}`;

    try {
        const res = await fetch(url, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json' }
        });
        const json = await res.json();

        if (json.success) {
            const label = currentDeleteType === 'category' ? 'Catégorie' : 'Ticket';
            showToast(`${label} supprimé(e) avec succès !`, 'success');
            closeDeleteModal();

            if (currentDeleteType === 'category') {
                loadCategories();
            } else {
                loadTickets();
            }
        } else {
            closeDeleteModal();
            showToast(json.message || "Erreur de suppression.", 'error');
        }
    } catch (err) {
        closeDeleteModal();
        showToast("Erreur réseau.", 'error');
    } finally {
        btn.disabled = false;
        spinner.classList.add('hidden');
    }
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    currentDeleteType = null;
    currentDeleteId   = null;
}

// ═══════════════════════════════════════════════════════════════════════════════
// UTILITAIRES & ALERTES
// ═══════════════════════════════════════════════════════════════════════════════

function showToast(message, type = 'success') {
    const toast = document.getElementById('toastNotification');
    const icon = document.getElementById('toastIcon');
    const text = document.getElementById('toastMessage');

    toast.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-800', 'border-emerald-200', 'bg-rose-50', 'text-rose-800', 'border-rose-200');

    if (type === 'success') {
        toast.classList.add('bg-emerald-50', 'text-emerald-800', 'border-emerald-200');
        icon.textContent = '✅';
    } else {
        toast.classList.add('bg-rose-50', 'text-rose-800', 'border-rose-200');
        icon.textContent = '⚠️';
    }

    text.textContent = message;
    setTimeout(() => { hideToast(); }, 4000);
}

function hideToast() {
    document.getElementById('toastNotification').classList.add('hidden');
}

function showAlert(boxId, iconId, textId, msg, type = 'error') {
    const box = document.getElementById(boxId);
    const icon = document.getElementById(iconId);
    const text = document.getElementById(textId);
    if (!box) return;

    box.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-800', 'border-emerald-200', 'bg-rose-50', 'text-rose-800', 'border-rose-200');
    box.classList.add('border');

    if (type === 'success') {
        box.classList.add('bg-emerald-50', 'text-emerald-800', 'border-emerald-200');
        if (icon) icon.textContent = '✅';
    } else {
        box.classList.add('bg-rose-50', 'text-rose-800', 'border-rose-200');
        if (icon) icon.textContent = '⚠️';
    }
    if (text) text.textContent = msg;
}

function hideAlert(boxId) {
    const box = document.getElementById(boxId);
    if (box) box.classList.add('hidden');
}

function setLoading(btnId, spinnerId, loading) {
    const btn = document.getElementById(btnId);
    const spin = document.getElementById(spinnerId);
    if (btn) btn.disabled = loading;
    if (spin) loading ? spin.classList.remove('hidden') : spin.classList.add('hidden');
}

function escHtml(s) {
    if (!s) return '';
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function escAttr(s) {
    if (!s) return '';
    return String(s).replace(/'/g, "\\'").replace(/"/g, '&quot;');
}

// ═══════════════════════════════════════════════════════════════════════════════
// INITIALISATION
// ═══════════════════════════════════════════════════════════════════════════════

function init() {
    const modal = document.getElementById('deleteModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });
    }
    navigateTo('categories');
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}

