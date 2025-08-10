<?php
require_once 'ApiController.php';

if (isset($_GET['id'])) {
    $tmdbController = new ApiController();
    $movieDetails = $tmdbController->getMovieDetails($_GET['id']);

    // Formatear los datos para el formulario
    $response = [
        'name' => $movieDetails['title'],
        'year' => date('Y', strtotime($movieDetails['release_date'])),
        'synopsis' => $movieDetails['overview'],
        'genres' => implode(', ', array_column($movieDetails['genres'], 'name')),
        'poster_url' => isset($movieDetails['poster_path']) ? TMDBConfig::IMAGE_BASE_URL . $movieDetails['poster_path'] : null,
        'rating' => round($movieDetails['vote_average'], 1)
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
}
?>