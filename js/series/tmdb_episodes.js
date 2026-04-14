// ── Importar temporada completa desde TMDB ──────────────────────

async function importarTemporada() {
    const temporada  = document.getElementById('import_temporada').value;
    const id_quality = document.getElementById('import_quality').value;
    const size       = document.getElementById('import_size').value;
    const backup     = document.getElementById('import_backup').value;
    const server     = document.getElementById('import_server').value;

    const errorDiv   = document.getElementById('import_error');
    const loadingDiv = document.getElementById('import_loading');
    const successDiv = document.getElementById('import_success');

    errorDiv.classList.add('d-none');
    successDiv.classList.add('d-none');

    // Validaciones básicas
    if (!temporada || temporada < 1) {
        mostrarError(errorDiv, 'Indica un número de temporada válido.');
        return;
    }
    if (!id_quality) {
        mostrarError(errorDiv, 'La calidad es obligatoria.');
        return;
    }
    if (!size || parseFloat(size) <= 0) {
        mostrarError(errorDiv, 'El tamaño por episodio debe ser mayor que 0.');
        return;
    }

    loadingDiv.classList.remove('d-none');

    try {
        // 1. Obtener episodios de TMDB
        const url = `https://api.themoviedb.org/3/tv/${SERIE_TMDB_ID}/season/${temporada}?api_key=${TMDB_API_KEY}&language=es-ES`;
        const res = await fetch(url);

        if (!res.ok) {
            throw new Error(`La temporada ${temporada} no existe en TMDB o no se pudo obtener.`);
        }

        const data = await res.json();
        const episodios = data.episodes;

        if (!episodios || episodios.length === 0) {
            throw new Error('TMDB no devolvió episodios para esta temporada.');
        }

        // 2. Enviar al servidor para guardar en BD
        const formData = new FormData();
        formData.append('serie_id',   SERIE_ID);
        formData.append('temporada',  temporada);
        formData.append('id_quality', id_quality);
        formData.append('size',       size);
        formData.append('backup',     backup);
        formData.append('server',     server);
        formData.append('episodios',  JSON.stringify(episodios));

        const saveRes = await fetch('../../php/series/importar_temporada.php', {
            method: 'POST',
            body: formData
        });

        const result = await saveRes.json();
        loadingDiv.classList.add('d-none');

        if (result.success) {
            successDiv.textContent = `✅ ${result.importados} episodios importados, ${result.omitidos} ya existían.`;
            successDiv.classList.remove('d-none');
            setTimeout(() => location.reload(), 2000);
        } else {
            mostrarError(errorDiv, result.message || 'Error al guardar los episodios.');
        }

    } catch (err) {
        loadingDiv.classList.add('d-none');
        mostrarError(errorDiv, err.message);
    }
}

// ── Editar episodio ─────────────────────────────────────────────

function abrirEditarEpisodio(ep) {
    document.getElementById('edit_id_episodio').value = ep.id_episodio;
    document.getElementById('edit_episodio_label').textContent =
        `T${ep.temporada} — Ep. ${ep.episodio}`;
    document.getElementById('edit_quality').value = ep.id_quality;
    document.getElementById('edit_size').value    = ep.size;
    document.getElementById('edit_backup').value  = ep.backup || '';
    document.getElementById('edit_server').value  = ep.server;
    document.getElementById('edit_error').classList.add('d-none');

    new bootstrap.Modal(document.getElementById('editarEpisodioModal')).show();
}

async function guardarEpisodio() {
    const id         = document.getElementById('edit_id_episodio').value;
    const id_quality = document.getElementById('edit_quality').value;
    const size       = document.getElementById('edit_size').value;
    const backup     = document.getElementById('edit_backup').value;
    const server     = document.getElementById('edit_server').value;
    const errorDiv   = document.getElementById('edit_error');

    errorDiv.classList.add('d-none');

    if (!size || parseFloat(size) <= 0) {
        mostrarError(errorDiv, 'El tamaño debe ser mayor que 0.');
        return;
    }

    const formData = new FormData();
    formData.append('id_episodio', id);
    formData.append('id_quality',  id_quality);
    formData.append('size',        size);
    formData.append('backup',      backup);
    formData.append('server',      server);

    const res    = await fetch('../../php/series/editar_episodio.php', { method: 'POST', body: formData });
    const result = await res.json();

    if (result.success) {
        location.reload();
    } else {
        mostrarError(errorDiv, result.message || 'Error al guardar el episodio.');
    }
}

// ── Borrar episodio individual ──────────────────────────────────

function confirmarBorrarEpisodio(idEpisodio) {
    if (!confirm('¿Seguro que quieres borrar este episodio?')) return;

    const formData = new FormData();
    formData.append('id_episodio', idEpisodio);

    fetch('../../php/series/borrar_episodio.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(result => {
            if (result.success) location.reload();
            else alert('Error al borrar el episodio: ' + (result.message || ''));
        });
}

// ── Borrar temporada completa ───────────────────────────────────

function confirmarBorrarTemporada(serieId, temporada) {
    if (!confirm(`¿Seguro que quieres borrar todos los episodios de la temporada ${temporada}?`)) return;

    const formData = new FormData();
    formData.append('serie_id',  serieId);
    formData.append('temporada', temporada);

    fetch('../../php/series/borrar_temporada.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(result => {
            if (result.success) location.reload();
            else alert('Error al borrar la temporada: ' + (result.message || ''));
        });
}

// ── Utilidad ────────────────────────────────────────────────────

function mostrarError(div, msg) {
    div.textContent = msg;
    div.classList.remove('d-none');
}
