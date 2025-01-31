<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
//
// @package local_mawanquizpasswordchanger
// @copyright 2025 Mawan Agus Nugroho
// @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

// Prevent direct access to this script.
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../config.php');
require_login();

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

/**
 * Sanitize input data.
 *
 * @param string $data The data to sanitize.
 * @return string The sanitized data.
 * @package local_mawanquizpasswordchanger
 *
 * @copyright 2025 Mawan Agus Nugroho
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['token'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Token required.']);
    exit;
}

$token = sanitize_input($input['token']);

try {
    // Rate limiting.
    session_start();
    $ip = $_SERVER['REMOTE_ADDR'];
    $requestcount = $_SESSION[$ip] ?? 0;

    if ($requestcount > 10) {
        http_response_code(429);
        echo json_encode(['error' => 'Too many requests.']);
        exit;
    }

    $_SESSION[$ip] = $requestcount + 1;

    // Validate token.
    $validation = validateToken($token);

    if (!$validation['valid']) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token.', 'detail' => $validation['error']]);
        exit;
    }

    // Database interaction with prepared statement.
    $conn = secureDBConnection();
    $stmt = $conn->prepare('SELECT * FROM tokens WHERE token = ?');
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Token not registered.');
    }

    echo json_encode(['status' => 'success', 'message' => 'Token valid.']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: '.$e->getMessage()]);
} finally {
    $conn->close();
}
