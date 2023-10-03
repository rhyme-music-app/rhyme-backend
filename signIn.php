<?php
namespace RhymeMusicApp\RhymeMusicApp;

require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/view_begin.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Rhyme</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- boostrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>

    <!-- css -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="login-page">
    <section class="vh-100 gradient-custom">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card bg-dark text-white rounded-4">
                        <div class="card-body p-5 text-center">

                            <div class="mb-md-5 mt-md-4 pb-5">

                                <h2 class="fw-bold mb-5">Login</h2>

                                <div class="form-outline form-white mb-4">
                                    <input type="email" placeholder="email" id="typeEmailX"
                                        class="form-control form-control-lg" />
                                </div>

                                <div class="form-outline form-white mb-4">
                                    <input type="password" placeholder="password" id="typePasswordX"
                                        class="form-control form-control-lg" />
                                </div>

                                <p class="small mb-5 pb-lg-2"><a class="text-white-50" href="forgotPassword.php">Forgot
                                        password?</a>
                                </p>

                                <button class="btn btn-outline-light btn-lg px-5" type="submit">Login</button>
                            </div>

                            <div>
                                <p class="mb-0">Don't have an account? <a href="signUp.php"
                                        class="text-white-50 fw-bold">Sign
                                        Up</a>
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/view_end.php';
?>
