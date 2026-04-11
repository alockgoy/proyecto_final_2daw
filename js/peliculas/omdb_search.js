const TMDB_API_URL = 'https://api.themoviedb.org/3';
const TMDB_IMAGE_BASE = 'https://image.tmdb.org/t/p/w500'; // Para posters

/**
 * Buscar películas en TMDb API (en español)
 */
function searchOMDb() {
    const searchInput = document.getElementById('omdb_search');
    const searchTerm = searchInput.value.trim();

    if (searchTerm === '') {
        showOMDbError('Por favor, escribe el nombre de una película');
        return;
    }

    // Mostrar loading
    showOMDbLoading(true);
    hideOMDbError();
    clearOMDbResults();

    // Hacer petición a TMDb API en español
    fetch(`${TMDB_API_URL}/search/movie?api_key=${TMDB_API_KEY}&language=es-ES&query=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            showOMDbLoading(false);

            if (data.results && data.results.length > 0) {
                displayOMDbResults(data.results);
            } else {
                showOMDbError('No se encontraron resultados');
            }
        })
        .catch(error => {
            showOMDbLoading(false);
            showOMDbError('Error al conectar con TMDb: ' + error.message);
            console.error('Error:', error);
        });
}

/**
 * Mostrar resultados de búsqueda
 */
function displayOMDbResults(movies) {
    const resultsContainer = document.getElementById('omdb_results');
    resultsContainer.innerHTML = '';

    if (!movies || movies.length === 0) {
        showOMDbError('No se encontraron películas');
        return;
    }

    const resultsHTML = `
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
            ${movies.map(movie => `
                <div class="col">
                    <div class="card h-100 movie-result-card" style="cursor: pointer;" 
                         onclick="selectOMDbMovie(${movie.id})">
                        <img src="${movie.poster_path ? TMDB_IMAGE_BASE + movie.poster_path : '../../img/no-poster.jpg'}" 
                             class="card-img-top" alt="${movie.title}" 
                             style="height: 300px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="card-title">${movie.title}</h6>
                            <p class="card-text text-muted mb-0">
                                <small>${movie.release_date ? movie.release_date.split('-')[0] : 'N/A'}</small>
                            </p>
                            ${movie.vote_average ? `
                                <p class="card-text">
                                    <small class="text-warning">
                                        <i class="fas fa-star"></i> ${movie.vote_average.toFixed(1)}/10
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
 * Seleccionar una película y rellenar el formulario
 */
function selectOMDbMovie(movieId) {
    showOMDbLoading(true);
    clearOMDbResults();

    // Obtener detalles completos de la película en español
    fetch(`${TMDB_API_URL}/movie/${movieId}?api_key=${TMDB_API_KEY}&language=es-ES&append_to_response=credits`)
        .then(response => response.json())
        .then(data => {
            showOMDbLoading(false);
            fillMovieForm(data);

            // Scroll suave al formulario
            document.querySelector('form').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

            // Mostrar mensaje de éxito
            showSuccessMessage('Datos cargados correctamente desde TMDb');
        })
        .catch(error => {
            showOMDbLoading(false);
            showOMDbError('Error al obtener detalles: ' + error.message);
            console.error('Error:', error);
        });
}

/**
 * Rellenar el formulario con datos de TMDb
 */
function fillMovieForm(movie) {
    // Nombre (en español)
    document.getElementById('name').value = movie.title || '';

    // Año
    const year = movie.release_date ? movie.release_date.split('-')[0] : '';
    document.getElementById('year').value = year;

    // Director (buscar en el crew)
    let director = '';
    if (movie.credits && movie.credits.crew) {
        const directorObj = movie.credits.crew.find(person => person.job === 'Director');
        director = directorObj ? directorObj.name : '';
    }
    document.getElementById('director').value = director;

    // Género (en español)
    const genres = movie.genres ? movie.genres.map(g => g.name).join(', ') : '';
    document.getElementById('gender').value = genres;

    // Calificación (de TMDb, sobre 10)
    if (movie.vote_average) {
        document.getElementById('rating').value = movie.vote_average.toFixed(1);
    }

    // Sinopsis (en español)
    document.getElementById('synopsis').value = movie.overview || '';

    // Poster (desde TMDb)
    if (movie.poster_path) {
        const posterUrl = TMDB_IMAGE_BASE + movie.poster_path;
        downloadAndSetPoster(posterUrl, movie.title);
    }

    // Resaltar campos rellenados
    highlightFilledFields();
}

/**
 * Descargar poster y establecerlo en el input file
 */
async function downloadAndSetPoster(posterUrl, movieTitle) {
    try {
        // Usar el proxy PHP para descargar la imagen
        const proxyUrl = `../../php/peliculas/download_poster.php?url=${encodeURIComponent(posterUrl)}`;

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
        const fileName = `${movieTitle.replace(/[^a-z0-9]/gi, '_').toLowerCase()}_poster.jpg`;
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
        // Mostrar mensaje al usuario (opcional)
        showOMDbError('No se pudo descargar el poster automáticamente. Puedes subirlo manualmente.');
    }
}

/**
 * Resaltar campos rellenados
 */
function highlightFilledFields() {
    const fields = ['name', 'year', 'director', 'gender', 'rating', 'synopsis'];

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
function showOMDbLoading(show) {
    const loadingDiv = document.getElementById('omdb_loading');
    if (show) {
        loadingDiv.classList.remove('d-none');
    } else {
        loadingDiv.classList.add('d-none');
    }
}

/**
 * Mostrar error
 */
function showOMDbError(message) {
    const errorDiv = document.getElementById('omdb_error');
    const errorMessage = document.getElementById('omdb_error_message');

    errorMessage.textContent = message;
    errorDiv.classList.remove('d-none');
}

/**
 * Ocultar error
 */
function hideOMDbError() {
    const errorDiv = document.getElementById('omdb_error');
    errorDiv.classList.add('d-none');
}

/**
 * Limpiar resultados
 */
function clearOMDbResults() {
    const resultsContainer = document.getElementById('omdb_results');
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
    const searchInput = document.getElementById('omdb_search');
    if (searchInput) {
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchOMDb();
            }
        });
    }
});