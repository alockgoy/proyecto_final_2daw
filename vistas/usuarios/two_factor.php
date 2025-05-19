<?php

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación en 2 pasos</title>

    <!-- Enlace al css de bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Enlace al archivo CSS -->
    <link rel="stylesheet" type="text/css" href="../../css/usuarios/two_factor.css">
</head>

<body>
    <div class="container">
        <br>
        <div class="row">
            <div class="col-lg-5 col-md-7 mx-auto my-auto">
                <div class="card">
                    <div class="card-body px-lg-5 py-lg-5 text-center">
                        <img src="https://bootdey.com/img/Content/avatar/avatar7.png"
                            class="rounded-circle avatar-lg img-thumbnail mb-4" alt="profile-image">
                        <h2 class="text-info">2FA Security</h2>
                        <p class="mb-4">Enter 6-digits code from your athenticatior app.</p>
                        <form>
                            <div class="row mb-4">
                                <div class="col-lg-2 col-md-2 col-2 ps-0 ps-md-2">
                                    <input type="text" class="form-control text-lg text-center" placeholder="_"
                                        aria-label="2fa">
                                </div>
                                <div class="col-lg-2 col-md-2 col-2 ps-0 ps-md-2">
                                    <input type="text" class="form-control text-lg text-center" placeholder="_"
                                        aria-label="2fa">
                                </div>
                                <div class="col-lg-2 col-md-2 col-2 ps-0 ps-md-2">
                                    <input type="text" class="form-control text-lg text-center" placeholder="_"
                                        aria-label="2fa">
                                </div>
                                <div class="col-lg-2 col-md-2 col-2 pe-0 pe-md-2">
                                    <input type="text" class="form-control text-lg text-center" placeholder="_"
                                        aria-label="2fa">
                                </div>
                                <div class="col-lg-2 col-md-2 col-2 pe-0 pe-md-2">
                                    <input type="text" class="form-control text-lg text-center" placeholder="_"
                                        aria-label="2fa">
                                </div>
                                <div class="col-lg-2 col-md-2 col-2 pe-0 pe-md-2">
                                    <input type="text" class="form-control text-lg text-center" placeholder="_"
                                        aria-label="2fa">
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="button" class="btn bg-info btn-lg my-4">Continue</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>