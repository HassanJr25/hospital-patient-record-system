<?php
/**
 * error_handler.php
 * ---------------------------------------------------------------------
 * CENTRALIZED ERROR HANDLING (Functional Requirement: Error Handling)
 *
 * PHP normally does one of two things when something goes wrong:
 *   1. In development: prints a scary raw error message with file
 *      paths and code snippets straight onto the page.
 *   2. In production (if display_errors is off): shows a blank white
 *      page with no explanation at all.
 *
 * Neither is acceptable for a real system. This file registers TWO
 * custom handlers so that EVERY error/exception in the whole app:
 *   - Gets written to a private log file (for developers to debug)
 *   - Shows the user a friendly, generic error page (500.php)
 *     that reveals NOTHING about the internal cause.
 * ---------------------------------------------------------------------
 */

// Where we write error details for developers only (never shown to users).
define('ERROR_LOG_FILE', __DIR__ . '/../logs/app_errors.log');

/**
 * logAppError()
 * -----------------------------------------------------------------
 * Writes a timestamped line to our private log file.
 * -----------------------------------------------------------------
 */
function logAppError(string $message): void
{
    $logDir = dirname(ERROR_LOG_FILE);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    file_put_contents(ERROR_LOG_FILE, $line, FILE_APPEND);
}

/**
 * showFriendlyErrorPage()
 * -----------------------------------------------------------------
 * Stops whatever was happening and shows the generic error page.
 * -----------------------------------------------------------------
 */
function showFriendlyErrorPage(): void
{
    // Clear any partial HTML/output that may have already started,
    // so the error page isn't mixed with a half-broken page.
    if (ob_get_length()) {
        ob_clean();
    }
    http_response_code(500);
    require __DIR__ . '/../views/errors/500.php';
    exit;
}

/**
 * Custom EXCEPTION handler.
 * Runs whenever a thrown Exception is never caught by a try/catch
 * anywhere else in the app (a "last resort" safety net).
 */
set_exception_handler(function (Throwable $exception) {
    logAppError('Uncaught exception: ' . $exception->getMessage() . ' in ' . $exception->getFile() . ':' . $exception->getLine());
    showFriendlyErrorPage();
});

/**
 * Custom ERROR handler.
 * Catches PHP warnings/notices/errors (not just Exceptions) and
 * routes them through the same logging + friendly page pattern.
 */
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    logAppError("PHP error [$errno]: $errstr in $errfile:$errline");

    // Only show the friendly error page for SERIOUS errors. Minor
    // notices/warnings get logged quietly so the page can keep working.
    if (in_array($errno, [E_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR], true)) {
        showFriendlyErrorPage();
    }

    // Returning true tells PHP "we've handled this" - stops it from
    // ALSO printing its own default error message.
    return true;
});
