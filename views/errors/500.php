<?php
/**
 * 500.php
 * ---------------------------------------------------------------------
 * A generic, user-friendly error page. Shown whenever an unexpected
 * error occurs, INSTEAD of a raw PHP error message that could leak
 * sensitive details (file paths, database structure, query text, etc.)
 * to whoever is looking at the screen.
 * ---------------------------------------------------------------------
 */
if (!defined('BASE_URL')) {
    // If this page is somehow reached before config.php loaded, define
    // a safe fallback so the link below still works.
    define('BASE_URL', '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Something went wrong</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height:100vh;">
    <div class="container text-center">
        <h1 class="display-5">Something went wrong</h1>
        <p class="text-muted">
            An unexpected error occurred while processing your request.<br>
            The issue has been logged. Please try again, or contact the system administrator if the problem continues.
        </p>
        <a href="<?php echo BASE_URL; ?>" class="btn btn-primary mt-3">Return to Home</a>
    </div>
</body>
</html>
