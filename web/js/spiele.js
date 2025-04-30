document.addEventListener('DOMContentLoaded', function () {
    setupAwesomplete('club1Text', 'club1ID', '/club/search');
    setupAwesomplete('club2Text', 'club2ID', '/club/search');
    setupAwesomplete('wettbewerbText', 'wettbewerbID', '/turnier/search');

    setupEditableTimeFields();
    setupInlineEditing();
    setupDeleteButtons();
});

function setupAwesomplete(inputId, hiddenInputId, url) {
    const input = document.getElementById(inputId);
    const hidden = document.getElementById(hiddenInputId);

    if (!input || !hidden) return;

    const awesomplete = new Awesomplete(input, { minChars: 2, autoFirst: true });

    input.addEventListener('input', () => {
        fetch(url + '?term=' + input.value)
            .then(res => res.json())
            .then(data => {
                awesomplete.list = (data || []).map(item => ({
                    label: item.value,
                    value: item.id
                }));
            });
    });

    input.addEventListener('awesomplete-selectcomplete', (event) => {
        hidden.value = event.text.value;
        input.value = event.text.label;
    });
}

function setupEditableTimeFields() {
    document.querySelectorAll('.view-time-editable').forEach(span => {
        span.addEventListener('click', () => {
            span.style.display = 'none';
            const wrapper = document.querySelector(`.edit-wrapper[data-spiel-id="${span.dataset.spielId}"]`);
            if (wrapper) {
                wrapper.style.display = 'block';
                const input = wrapper.querySelector('.edit-datetime');
                if (input) input.focus();
            }
        });
    });

    document.querySelectorAll('.edit-datetime').forEach(input => {
        input.addEventListener('blur', () => {
            const spielId = input.dataset.spielId;
            const value = input.value;
            const oldDate = input.dataset.oldDate || "";

            fetch('/spiele/update-datetime', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ spielId, datetime: value })
            })
            .then(res => res.json())
            .then(data => {
                const wrapper = document.querySelector(`.edit-wrapper[data-spiel-id="${spielId}"]`);
                const span = document.querySelector(`.view-time[data-spiel-id="${spielId}"]`);
                if (data.success && span) {
                    const newDate = value.split('T')[0];
                    span.textContent = new Date(value).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    span.style.display = 'block';
                    if (newDate !== oldDate) location.reload();
                }
                if (wrapper) wrapper.style.display = 'none';
            });
        });

        input.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                const wrapper = input.closest('.edit-wrapper');
                const span = document.querySelector(`.view-time[data-spiel-id="${input.dataset.spielId}"]`);
                if (wrapper) wrapper.style.display = 'none';
                if (span) span.style.display = 'block';
            }
        });
    });
}

function setupInlineEditing() {
    document.querySelectorAll('.editable').forEach(cell => {
        cell.addEventListener('click', () => {
            const field = cell.dataset.field;
            const id = cell.id.split('-')[1];
            const input = document.createElement('input');
            input.value = cell.innerText.trim();
            cell.innerHTML = '';
            cell.appendChild(input);
            input.focus();
            input.setSelectionRange(input.value.length, input.value.length);

            input.addEventListener('blur', () => {
                fetch('/spiele/update', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id, field, value: input.value })
                })
                .then(res => res.json())
                .then(data => {
                    cell.innerHTML = data.success ? input.value : 'Fehler';
                });
            });
        });
    });
}

function setupDeleteButtons() {
    let deleteSpielId = null;

    document.querySelectorAll('.delete-game').forEach(btn => {
        btn.addEventListener('click', () => {
            deleteSpielId = btn.dataset.spielId;
        });
    });

    const confirmBtn = document.getElementById('confirmDeleteButton');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', () => {
            if (!deleteSpielId) return;

            fetch('/spiele/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ spielId: deleteSpielId })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) location.reload();
                else alert(data.error);
            })
            .finally(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                if (modal) modal.hide();
            });
        });
    }
}
