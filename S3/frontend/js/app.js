// S3/frontend/js/app.js
// Architecture 3 Tiers — Tier 1 Présentation (Fetch API + DOM)

'use strict';

// ── URLs API ──────────────────────────────────────────────────────────────────
const CATEGORIES_API = '../backend/api/categories.php';
const TICKETS_API    = '../backend/api/tickets.php';
const USERS_API      = '../backend/api/utilisateurs.php';

// ── État global ───────────────────────────────────────────────────────────────
let currentSection    = 'categories';
let currentDeleteType = null;   // 'category' | 'ticket'
let currentDeleteId   = null;
let currentDeleteName = '';
let allCategories     = [];     // cache pour filtre
let allTickets        = [];     // cache pour filtre

// ═══════════════════════════════════════════════════════════════════════════════
// NAVIGATION
// ═══════════════════════════════════════════════════════════════════════════════

function navigateTo(section) {
  currentSection = section;

  // Sidebar — bouton actif
  document.querySelectorAll('.nav-btn').forEach(btn => {
    btn.classList.remove('bg-indigo-600', 'text-white');
    btn.classList.add('text-slate-300');
  });
  const activeBtn = document.getElementById('nav-' + section);
  if (activeBtn) {
    activeBtn.classList.add('bg-indigo-600', 'text-white');
    activeBtn.classList.remove('text-slate-300');
  }

  // Topbar
  if (section === 'categories') {
    document.getElementById('page-title').textContent    = 'Catégories';
    document.getElementById('page-subtitle').textContent = 'Gérez vos catégories de tickets';
    document.getElementById('btn-ajouter-label').textContent = 'Nouvelle catégorie';
    showView('view-categories-liste');
    loadCategories();
  } else {
    document.getElementById('page-title').textContent    = 'Tickets';
    document.getElementById('page-subtitle').textContent = 'Gérez vos tickets de support';
    document.getElementById('btn-ajouter-label').textContent = 'Nouveau ticket';
    showView('view-tickets-liste');
    loadTickets();
  }
}

/** Affiche une seule section, cache les autres */
function showView(viewId) {
  document.querySelectorAll('.view-section').forEach(s => s.classList.add('hidden'));
  const target = document.getElementById(viewId);
  if (target) target.classList.remove('hidden');
}

/** Appelé par le bouton "+ Ajouter" de la topbar */
function onAddClick() {
  if (currentSection === 'categories') {
    showAddCategoryForm();
  } else {
    showAddTicketForm();
  }
}

// ═══════════════════════════════════════════════════════════════════════════════
// CATÉGORIES — CRUD
// ═══════════════════════════════════════════════════════════════════════════════

async function loadCategories() {
  try {
    const res  = await fetch(CATEGORIES_API);
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    allCategories = json.data;
    document.getElementById('cat-count').textContent = `${allCategories.length} catégorie(s)`;
    renderCategories(allCategories);
  } catch (err) {
    document.getElementById('categories-tbody').innerHTML =
      `<tr><td colspan="4" class="px-5 py-10 text-center text-red-400">${escHtml(err.message)}</td></tr>`;
  }
}

function renderCategories(list) {
  const tbody = document.getElementById('categories-tbody');
  if (!list.length) {
    tbody.innerHTML = `<tr><td colspan="4" class="px-5 py-10 text-center text-slate-400">Aucune catégorie trouvée.</td></tr>`;
    return;
  }
  tbody.innerHTML = list.map(c => {
    const descHtml = c.description
      ? `<div class="text-xs text-slate-400 mt-0.5">${escHtml(c.description)}</div>`
      : '';
    const nb = parseInt(c.nb_tickets, 10) || 0;
    const nbBadge = nb > 0
      ? `<span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">${nb}</span>`
      : `<span class="text-slate-400 text-xs">0</span>`;
    const canDelete = nb === 0;
    return `<tr class="hover:bg-slate-50 transition-colors">
      <td class="px-5 py-3 text-slate-400 font-mono text-xs">#${c.id_categorie}</td>
      <td class="px-5 py-3">
        <div class="font-medium text-slate-800">${escHtml(c.nom_categorie)}</div>
        ${descHtml}
      </td>
      <td class="px-5 py-3 text-center">${nbBadge}</td>
      <td class="px-5 py-3 text-center">
        <div class="flex items-center justify-center gap-2">
          <button onclick="showEditCategoryForm(${c.id_categorie})"
                  title="Modifier"
                  class="p-1.5 rounded-lg text-amber-500 hover:bg-amber-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
          </button>
          <button onclick="confirmDeleteCategory(${c.id_categorie}, '${escAttr(c.nom_categorie)}', ${nb})"
                  title="${canDelete ? 'Supprimer' : 'Impossible : tickets liés'}"
                  ${canDelete ? '' : 'disabled'}
                  class="p-1.5 rounded-lg transition-colors ${canDelete
                    ? 'text-red-500 hover:bg-red-50'
                    : 'text-slate-300 cursor-not-allowed'}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </div>
      </td>
    </tr>`;
  }).join('');
}

function filterCategories() {
  const q = document.getElementById('search-categories').value.trim().toLowerCase();
  const filtered = q
    ? allCategories.filter(c =>
        c.nom_categorie.toLowerCase().includes(q) ||
        (c.description || '').toLowerCase().includes(q)
      )
    : allCategories;
  document.getElementById('cat-count').textContent = `${filtered.length} catégorie(s)`;
  renderCategories(filtered);
}

function showAddCategoryForm() {
  document.getElementById('add-cat-nom').value  = '';
  document.getElementById('add-cat-desc').value = '';
  hideFormAlert('add-cat-alert');
  showView('view-categories-ajout');
  document.getElementById('add-cat-nom').focus();
}

async function showEditCategoryForm(id) {
  showView('view-categories-modifier');
  hideFormAlert('edit-cat-alert');
  try {
    const res  = await fetch(`${CATEGORIES_API}?id=${id}`);
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    const c = json.data;
    document.getElementById('edit-cat-id').value   = c.id_categorie;
    document.getElementById('edit-cat-nom').value  = c.nom_categorie;
    document.getElementById('edit-cat-desc').value = c.description || '';
    document.getElementById('edit-cat-nom').focus();
  } catch (err) {
    showFormAlert('edit-cat-alert', 'edit-cat-icon', 'edit-cat-text', err.message, 'error');
  }
}

async function submitAddCategory(e) {
  e.preventDefault();
  hideFormAlert('add-cat-alert');
  setLoading('btn-submit-add-cat', 'spin-add-cat', true);
  try {
    const body = {
      nom_categorie: document.getElementById('add-cat-nom').value.trim(),
      description:   document.getElementById('add-cat-desc').value.trim() || null,
    };
    const res  = await fetch(CATEGORIES_API, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body),
    });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    showToast('Catégorie créée avec succès !', 'success');
    navigateTo('categories');
  } catch (err) {
    showFormAlert('add-cat-alert', 'add-cat-icon', 'add-cat-text', err.message, 'error');
  } finally {
    setLoading('btn-submit-add-cat', 'spin-add-cat', false);
  }
}

async function submitEditCategory(e) {
  e.preventDefault();
  hideFormAlert('edit-cat-alert');
  setLoading('btn-submit-edit-cat', 'spin-edit-cat', true);
  const id = document.getElementById('edit-cat-id').value;
  try {
    const body = {
      nom_categorie: document.getElementById('edit-cat-nom').value.trim(),
      description:   document.getElementById('edit-cat-desc').value.trim() || null,
    };
    const res  = await fetch(`${CATEGORIES_API}?id=${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body),
    });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    showToast('Catégorie mise à jour !', 'success');
    navigateTo('categories');
  } catch (err) {
    showFormAlert('edit-cat-alert', 'edit-cat-icon', 'edit-cat-text', err.message, 'error');
  } finally {
    setLoading('btn-submit-edit-cat', 'spin-edit-cat', false);
  }
}

function confirmDeleteCategory(id, name, nb) {
  if (nb > 0) return;
  currentDeleteType = 'category';
  currentDeleteId   = id;
  currentDeleteName = name;
  document.getElementById('modal-delete-title').textContent = 'Supprimer la catégorie';
  document.getElementById('modal-delete-body').textContent  =
    `Voulez-vous supprimer "${name}" ? Cette action est irréversible.`;
  document.getElementById('modal-delete').classList.remove('hidden');
}

// ═══════════════════════════════════════════════════════════════════════════════
// TICKETS — CRUD
// ═══════════════════════════════════════════════════════════════════════════════

async function loadTickets() {
  try {
    const res  = await fetch(TICKETS_API);
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    allTickets = json.data;
    document.getElementById('ticket-count').textContent = `${allTickets.length} ticket(s)`;
    renderTickets(allTickets);
  } catch (err) {
    document.getElementById('tickets-tbody').innerHTML =
      `<tr><td colspan="7" class="px-5 py-10 text-center text-red-400">${escHtml(err.message)}</td></tr>`;
  }
}

const STATUT_CLASSES = {
  'Ouvert':   'bg-emerald-100 text-emerald-800',
  'En cours': 'bg-blue-100 text-blue-800',
  'Résolu':   'bg-violet-100 text-violet-800',
  'Fermé':    'bg-slate-100 text-slate-600',
};

function renderTickets(list) {
  const tbody = document.getElementById('tickets-tbody');
  if (!list.length) {
    tbody.innerHTML = `<tr><td colspan="7" class="px-5 py-10 text-center text-slate-400">Aucun ticket trouvé.</td></tr>`;
    return;
  }
  tbody.innerHTML = list.map(t => {
    const cls  = STATUT_CLASSES[t.statut] || 'bg-slate-100 text-slate-600';
    const date = t.date_creation ? t.date_creation.substring(0, 10) : '—';
    return `<tr class="hover:bg-slate-50 transition-colors">
      <td class="px-4 py-3 text-slate-400 font-mono text-xs">#${t.id_ticket}</td>
      <td class="px-4 py-3 font-medium text-slate-800 max-w-xs truncate">${escHtml(t.titre)}</td>
      <td class="px-4 py-3">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ${cls}">${escHtml(t.statut)}</span>
      </td>
      <td class="px-4 py-3 text-sm text-slate-600">${escHtml(t.nom_categorie || '—')}</td>
      <td class="px-4 py-3 text-sm text-slate-600">${escHtml(t.nom_complet_utilisateur || '—')}</td>
      <td class="px-4 py-3 text-xs text-slate-400">${date}</td>
      <td class="px-4 py-3 text-center">
        <div class="flex items-center justify-center gap-2">
          <button onclick="showEditTicketForm(${t.id_ticket})"
                  title="Modifier"
                  class="p-1.5 rounded-lg text-amber-500 hover:bg-amber-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
          </button>
          <button onclick="confirmDeleteTicket(${t.id_ticket}, '${escAttr(t.titre)}')"
                  title="Supprimer"
                  class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </div>
      </td>
    </tr>`;
  }).join('');
}

function filterTickets() {
  const q = document.getElementById('search-tickets').value.trim().toLowerCase();
  const filtered = q
    ? allTickets.filter(t =>
        t.titre.toLowerCase().includes(q) ||
        (t.statut || '').toLowerCase().includes(q) ||
        (t.nom_categorie || '').toLowerCase().includes(q) ||
        (t.nom_complet_utilisateur || '').toLowerCase().includes(q)
      )
    : allTickets;
  document.getElementById('ticket-count').textContent = `${filtered.length} ticket(s)`;
  renderTickets(filtered);
}

/** Charge catégories + utilisateurs dans les <select> du formulaire ticket */
async function loadFormSelectData(catSelectId, userSelectId, selectedCat = null, selectedUser = null) {
  try {
    const [catRes, userRes] = await Promise.all([
      fetch(CATEGORIES_API),
      fetch(USERS_API),
    ]);
    const [catJson, userJson] = await Promise.all([catRes.json(), userRes.json()]);

    const catSelect  = document.getElementById(catSelectId);
    const userSelect = document.getElementById(userSelectId);

    catSelect.innerHTML  = '<option value="">— Choisir —</option>';
    userSelect.innerHTML = '<option value="">— Choisir —</option>';

    if (catJson.success) {
      catJson.data.forEach(c => {
        const opt = new Option(c.nom_categorie, c.id_categorie, false, String(c.id_categorie) === String(selectedCat));
        catSelect.add(opt);
      });
    }
    if (userJson.success) {
      userJson.data.forEach(u => {
        const label = `${u.prenom} ${u.nom} (${u.role})`;
        const opt   = new Option(label, u.id_utilisateur, false, String(u.id_utilisateur) === String(selectedUser));
        userSelect.add(opt);
      });
    }
  } catch (err) {
    console.error('Erreur chargement selects :', err);
  }
}

async function showAddTicketForm() {
  showView('view-tickets-ajout');
  hideFormAlert('add-ticket-alert');
  document.getElementById('form-add-ticket').reset();
  await loadFormSelectData('add-ticket-categorie', 'add-ticket-utilisateur');
  document.getElementById('add-ticket-titre').focus();
}

async function showEditTicketForm(id) {
  showView('view-tickets-modifier');
  hideFormAlert('edit-ticket-alert');
  try {
    const res  = await fetch(`${TICKETS_API}?id=${id}`);
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    const t = json.data;

    document.getElementById('edit-ticket-id').value    = t.id_ticket;
    document.getElementById('edit-ticket-titre').value = t.titre;
    document.getElementById('edit-ticket-desc').value  = t.description;

    await loadFormSelectData(
      'edit-ticket-categorie',
      'edit-ticket-utilisateur',
      t.id_categorie,
      t.id_utilisateur
    );

    document.getElementById('edit-ticket-statut').value = t.statut;
    document.getElementById('edit-ticket-titre').focus();
  } catch (err) {
    showFormAlert('edit-ticket-alert', 'edit-ticket-icon', 'edit-ticket-text', err.message, 'error');
  }
}

async function submitAddTicket(e) {
  e.preventDefault();
  hideFormAlert('add-ticket-alert');
  setLoading('btn-submit-add-ticket', 'spin-add-ticket', true);
  try {
    const body = {
      titre:          document.getElementById('add-ticket-titre').value.trim(),
      description:    document.getElementById('add-ticket-desc').value.trim(),
      statut:         document.getElementById('add-ticket-statut').value,
      id_categorie:   parseInt(document.getElementById('add-ticket-categorie').value, 10),
      id_utilisateur: parseInt(document.getElementById('add-ticket-utilisateur').value, 10),
    };
    const res  = await fetch(TICKETS_API, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body),
    });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    showToast('Ticket créé avec succès !', 'success');
    navigateTo('tickets');
  } catch (err) {
    showFormAlert('add-ticket-alert', 'add-ticket-icon', 'add-ticket-text', err.message, 'error');
  } finally {
    setLoading('btn-submit-add-ticket', 'spin-add-ticket', false);
  }
}

async function submitEditTicket(e) {
  e.preventDefault();
  hideFormAlert('edit-ticket-alert');
  setLoading('btn-submit-edit-ticket', 'spin-edit-ticket', true);
  const id = document.getElementById('edit-ticket-id').value;
  try {
    const body = {
      titre:          document.getElementById('edit-ticket-titre').value.trim(),
      description:    document.getElementById('edit-ticket-desc').value.trim(),
      statut:         document.getElementById('edit-ticket-statut').value,
      id_categorie:   parseInt(document.getElementById('edit-ticket-categorie').value, 10),
      id_utilisateur: parseInt(document.getElementById('edit-ticket-utilisateur').value, 10),
    };
    const res  = await fetch(`${TICKETS_API}?id=${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body),
    });
    const json = await res.json();
    if (!json.success) throw new Error(json.message);
    showToast('Ticket mis à jour !', 'success');
    navigateTo('tickets');
  } catch (err) {
    showFormAlert('edit-ticket-alert', 'edit-ticket-icon', 'edit-ticket-text', err.message, 'error');
  } finally {
    setLoading('btn-submit-edit-ticket', 'spin-edit-ticket', false);
  }
}

function confirmDeleteTicket(id, titre) {
  currentDeleteType = 'ticket';
  currentDeleteId   = id;
  currentDeleteName = titre;
  document.getElementById('modal-delete-title').textContent = 'Supprimer le ticket';
  document.getElementById('modal-delete-body').textContent  =
    `Voulez-vous supprimer le ticket "${titre}" ? Cette action est irréversible.`;
  document.getElementById('modal-delete').classList.remove('hidden');
}

// ═══════════════════════════════════════════════════════════════════════════════
// SUPPRESSION PARTAGÉE
// ═══════════════════════════════════════════════════════════════════════════════

async function executeDelete() {
  setLoading('btn-confirm-delete', 'spin-delete', true);
  try {
    const url    = currentDeleteType === 'category'
                   ? `${CATEGORIES_API}?id=${currentDeleteId}`
                   : `${TICKETS_API}?id=${currentDeleteId}`;
    const res    = await fetch(url, { method: 'DELETE' });
    const json   = await res.json();
    if (!json.success) throw new Error(json.message);

    const label  = currentDeleteType === 'category' ? 'Catégorie' : 'Ticket';
    showToast(`${label} supprimé(e) avec succès !`, 'success');
    closeDeleteModal();

    if (currentDeleteType === 'category') {
      navigateTo('categories');
    } else {
      navigateTo('tickets');
    }
  } catch (err) {
    showToast(err.message, 'error');
    closeDeleteModal();
  } finally {
    setLoading('btn-confirm-delete', 'spin-delete', false);
  }
}

function closeDeleteModal() {
  document.getElementById('modal-delete').classList.add('hidden');
  currentDeleteType = null;
  currentDeleteId   = null;
}

// ═══════════════════════════════════════════════════════════════════════════════
// UTILITAIRES
// ═══════════════════════════════════════════════════════════════════════════════

function showToast(msg, type = 'success') {
  const toast = document.getElementById('toast');
  toast.textContent = msg;
  toast.className   = `fixed top-5 right-5 z-50 max-w-sm px-5 py-3 rounded-xl shadow-xl text-sm font-medium text-white transition-all duration-300 ${
    type === 'success' ? 'bg-emerald-600' : 'bg-red-600'
  }`;
  toast.classList.remove('hidden');
  setTimeout(() => toast.classList.add('hidden'), 3500);
}

function setLoading(btnId, spinnerId, loading) {
  const btn  = document.getElementById(btnId);
  const spin = document.getElementById(spinnerId);
  if (!btn || !spin) return;
  btn.disabled = loading;
  loading ? spin.classList.remove('hidden') : spin.classList.add('hidden');
}

function showFormAlert(boxId, iconId, textId, msg, type) {
  const box  = document.getElementById(boxId);
  const icon = document.getElementById(iconId);
  const text = document.getElementById(textId);
  if (!box) return;
  box.classList.remove('hidden', 'bg-red-50', 'text-red-700', 'bg-green-50', 'text-green-700');
  if (type === 'error') {
    box.classList.add('bg-red-50', 'text-red-700');
    if (icon) icon.textContent = '⚠️';
  } else {
    box.classList.add('bg-green-50', 'text-green-700');
    if (icon) icon.textContent = '✅';
  }
  if (text) text.textContent = msg;
}

function hideFormAlert(boxId) {
  const box = document.getElementById(boxId);
  if (box) box.classList.add('hidden');
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
// INIT
// ═══════════════════════════════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {
  // Fermer la modale si clic en dehors
  document.getElementById('modal-delete').addEventListener('click', function (e) {
    if (e.target === this) closeDeleteModal();
  });
  navigateTo('categories');
});
