const TMDB_API_URL = 'https://api.themoviedb.org/3';
const TMDB_IMAGE_BASE = 'https://image.tmdb.org/t/p/w500';

/**
 * Buscar series en TMDb API (en español)
 */
function searchTMDb() {
    const searchInput = document.getElementById('tmdb_search');
    const searchTerm = searchInput.value.trim();

    if (searchTerm === '') {
        showTMDbError('Por favor, escribe el nombre de una serie');
        return;
    }

    // Mostrar loading
    showTMDbLoading(true);
    hideTMDbError();
    clearTMDbResults();

    // Hacer petición a TMDb API en español para SERIES
    fetch(`${TMDB_API_URL}/search/tv?api_key=${TMDB_API_KEY}&language=es-ES&query=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            showTMDbLoading(false);

            if (data.results && data.results.length > 0) {
                displayTMDbResults(data.results);
            } else {
                showTMDbError('No se encontraron resultados');
            }
        })
        .catch(error => {
            showTMDbLoading(false);
            showTMDbError('Error al conectar con TMDb: ' + error.message);
            console.error('Error:', error);
        });
}

/**
 * Mostrar resultados de búsqueda
 */
function displayTMDbResults(series) {
    const resultsContainer = document.getElementById('tmdb_results');
    resultsContainer.innerHTML = '';

    if (!series || series.length === 0) {
        showTMDbError('No se encontraron series');
        return;
    }

    const resultsHTML = `
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
            ${series.map(serie => `
                <div class="col">
                    <div class="card h-100 serie-result-card" style="cursor: pointer;" 
                         onclick="selectTMDbSerie(${serie.id})">
                        <img src="${serie.poster_path ? TMDB_IMAGE_BASE + serie.poster_path : '../../img/no-poster.jpg'}" 
                             class="card-img-top" alt="${serie.name}" 
                             style="height: 300px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="card-title">${serie.name}</h6>
                            <p class="card-text text-muted mb-0">
                                <small>${serie.first_air_date ? serie.first_air_date.split('-')[0] : 'N/A'}</small>
                            </p>
                            ${serie.vote_average ? `
                                <p class="card-text">
                                    <small class="text-warning">
                                        <i class="fas fa-star"></i> ${serie.vote_average.toFixed(1)}/10
                                    </small>
                                </p>
                            ` : ''}
                        </div>
                        <div class="card-footer bg-transparent">
                            <button type="button" class="btn btn-sm btn-primary w-100">
                                <i class="fas fa-check me-1"></i>Seleccionar
                            </button>
                        </div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;

    resultsContainer.innerHTML = resultsHTML;
}

/**
 * Seleccionar una serie y rellenar el formulario
 */
function selectTMDbSerie(serieId) {
    showTMDbLoading(true);
    clearTMDbResults();

    // Obtener detalles completos de la serie EN ESPAÑOL
    fetch(`${TMDB_API_URL}/tv/${serieId}?api_key=${TMDB_API_KEY}&language=es-ES`)
        .then(response => response.json())
        .then(data => {
            showTMDbLoading(false);
            fillSerieForm(data);

            // Scroll suave al formulario
            document.querySelector('form').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

            // Mostrar mensaje de éxito
            showSuccessMessage('Datos cargados correctamente desde TMDb');
        })
        .catch(error => {
            showTMDbLoading(false);
            showTMDbError('Error al obtener detalles: ' + error.message);
            console.error('Error:', error);
        });
}

/**
 * Rellenar el formulario con datos de TMDb
 */
function fillSerieForm(serie) {
    // Nombre (en español)
    document.getElementById('name').value = serie.name || '';

    // Año (fecha del primer episodio)
    const year = serie.first_air_date ? serie.first_air_date.split('-')[0] : '';
    document.getElementById('year').value = year;

    // Género (en español)
    const genres = serie.genres ? serie.genres.map(g => g.name).join(', ') : '';
    document.getElementById('gender').value = genres;

    // Calificación (de TMDb, sobre 10)
    if (serie.vote_average) {
        document.getElementById('rating').value = serie.vote_average.toFixed(1);
    }

    // Poster (desde TMDb)
    if (serie.poster_path) {
        const posterUrl = TMDB_IMAGE_BASE + serie.poster_path;
        downloadAndSetPoster(posterUrl, serie.name);
    }

    // Resaltar campos rellenados
    highlightFilledFields();
}

/**
 * Descargar poster y establecerlo en el input file
 */
async function downloadAndSetPoster(posterUrl, serieName) {
    try {
        // Usar el proxy PHP para descargar la imagen
        const proxyUrl = `../../php/series/download_poster.php?url=${encodeURIComponent(posterUrl)}`;

        const response = await fetch(proxyUrl);
        const result = await response.json();

        if (!result.success) {
            throw new Error(result.error || 'Error al descargar la imagen');
        }

        // Convertir base64 a blob
        const byteCharacters = atob(result.data);
        const byteNumbers = new Array(byteCharacters.length);
        for (let i = 0; i < byteCharacters.length; i++) {
            byteNumbers[i] = byteCharacters.charCodeAt(i);
        }
        const byteArray = new Uint8Array(byteNumbers);
        const blob = new Blob([byteArray], { type: 'image/jpeg' });

        // Crear un archivo desde el blob
        const fileName = `${serieName.replace(/[^a-z0-9]/gi, '_').toLowerCase()}_poster.jpg`;
        const file = new File([blob], fileName, { type: 'image/jpeg' });

        // Crear un DataTransfer para simular la selección de archivo
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);

        // Establecer el archivo en el input
        const posterInput = document.getElementById('poster');
        posterInput.files = dataTransfer.files;

        // Disparar evento change para actualizar la UI
        posterInput.dispatchEvent(new Event('change', { bubbles: true }));

        console.log('✅ Poster descargado correctamente:', fileName);

    } catch (error) {
        console.warn('⚠️ No se pudo descargar el poster:', error);
        showTMDbError('No se pudo descargar el poster automáticamente. Puedes subirlo manualmente.');
    }
}

/**
 * Resaltar campos rellenados
 */
function highlightFilledFields() {
    const fields = ['name', 'year', 'gender', 'rating'];

    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field && field.value) {
            field.classList.add('border-success');
            setTimeout(() => {
                field.classList.remove('border-success');
            }, 2000);
        }
    });
}

/**
 * Mostrar/ocultar loading
 */
function showTMDbLoading(show) {
    const loadingDiv = document.getElementById('tmdb_loading');
    if (show) {
        loadingDiv.classList.remove('d-none');
    } else {
        loadingDiv.classList.add('d-none');
    }
}

/**
 * Mostrar error
 */
function showTMDbError(message) {
    const errorDiv = document.getElementById('tmdb_error');
    const errorMessage = document.getElementById('tmdb_error_message');

    errorMessage.textContent = message;
    errorDiv.classList.remove('d-none');
}

/**
 * Ocultar error
 */
function hideTMDbError() {
    const errorDiv = document.getElementById('tmdb_error');
    errorDiv.classList.add('d-none');
}

/**
 * Limpiar resultados
 */
function clearTMDbResults() {
    const resultsContainer = document.getElementById('tmdb_results');
    resultsContainer.innerHTML = '';
}

/**
 * Mostrar mensaje de éxito temporal
 */
function showSuccessMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success alert-dismissible fade show';
    alertDiv.innerHTML = `
        <i class="fas fa-check-circle me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.querySelector('.card-body').insertBefore(
        alertDiv,
        document.querySelector('form')
    );

    // Auto-cerrar después de 3 segundos
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

/**
 * Permitir búsqueda con Enter
 */
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('tmdb_search');
    if (searchInput) {
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchTMDb();
            }
        });
    }
});