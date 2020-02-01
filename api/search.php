<?php

include '../Note.php';
$note = new Note();

if (isset($_REQUEST['search'])) {
    $notes = $note->searchNotes($_REQUEST['search']);
}

echo json_encode(['success' => true, 'data' => $notes]);