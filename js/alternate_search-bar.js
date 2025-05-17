document.getElementById('toggle-search').addEventListener('click', function () {

        console.log("Alternando buscador...");

        const byName = document.getElementById('search-by-name');
        const byDir = document.getElementById('search-by-director');
        const toggleBt = this;

        if (byName.classList.contains('d-none')) {
            // Actualmente viendo director → pasar a nombre
            byName.classList.remove('d-none');
            byDir.classList.add('d-none');
            toggleBt.textContent = 'Buscar por director';
        } else {
            // Actualmente viendo nombre → pasar a director
            byName.classList.add('d-none');
            byDir.classList.remove('d-none');
            toggleBt.textContent = 'Buscar por nombre';
        }
    });
