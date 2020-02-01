<?php 
include('../Note.php');
if(isset($_REQUEST['id'])) {
    $id = $_REQUEST['id'];

    $note = new Note();
    $noteData = $note->findNote($id);
    echo json_encode(['status' => 'success', 'data' => $noteData]);
}