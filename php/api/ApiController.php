<?php

// Traer el archivo de configuración
require_once 'api_config.php';

class ApiController {
    
    public function searchMovie($query) {
        $url = TMDBConfig::BASE_URL . '/search/movie?api_key=' . TMDBConfig::API_KEY . '&query=' . urlencode($query) . '&language=es-ES';
        
        $response = file_get_contents($url);
        return json_decode($response, true);
    }
    
    public function getMovieDetails($movieId) {
        $url = TMDBConfig::BASE_URL . '/movie/' . $movieId . '?api_key=' . TMDBConfig::API_KEY . '&language=es-ES';
        
        $response = file_get_contents($url);
        return json_decode($response, true);
    }
}
?>