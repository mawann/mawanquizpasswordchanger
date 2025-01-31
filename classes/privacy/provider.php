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

namespace local_mawanquizpasswordchanger\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\user_preference_provider;
use core_privacy\local\request\writer;

/**
 * Privacy Subsystem implementation for local_mawanquizpasswordchanger.
 *
 * @package    local_mawanquizpasswordchanger
 * @copyright  2025 Mawan Agus Nugroho <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\user_preference_provider {

    public static function get_metadata(collection $collection): collection {
        $collection->add_external_location('mawan_quiz_password', [
            'serial_number' => 'privacy:metadata:serialnumber',
            'valid_until' => 'privacy:metadata:validuntil'
        ], 'privacy:metadata:mawanquizpasswordchanger');
        return $collection;
    }

    public static function export_user_preferences(int $userid) {
        // Jika ada preferensi pengguna yang terkait
    }
}
