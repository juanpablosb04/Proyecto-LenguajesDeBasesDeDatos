require 'db.php';

function asignarInstructorAClase($id_instructor, $id_clase) {
    try {
        global $pdo;

        $sql = "INSERT INTO instructores_clases (id_instructor, id_clase) VALUES (:id_instructor, :id_clase)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id_instructor' => $id_instructor,
            'id_clase' => $id_clase
        ]);

        return true;
    } catch (Exception $e) {
        return false;
    }
}


function obtenerClasesDeInstructor($id_instructor) {
    try {
        global $pdo;

        $sql = "SELECT c.* FROM clases c 
                JOIN instructores_clases ic ON c.id_clase = ic.id_clase
                WHERE ic.id_instructor = :id_instructor";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id_instructor' => $id_instructor]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}


function eliminarRelacion($id_instructor, $id_clase) {
    try {
        global $pdo;

        $sql = "DELETE FROM instructores_clases WHERE id_instructor = :id_instructor AND id_clase = :id_clase";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id_instructor' => $id_instructor,
            'id_clase' => $id_clase
        ]);

        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}
