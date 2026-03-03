<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;

class FuncionRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function guardar(array $datos): bool {
        // 1. Verificamos si la sala ya está ocupada
        $sqlCheck = "SELECT * FROM funciones WHERE idSala = :idSala AND fechaFuncion = :fecha AND horaInicio = :hora";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([
            ':idSala' => $datos['idSala'],
            ':fecha'  => $datos['fechaFuncion'],
            ':hora'   => $datos['horaInicio']
        ]);

        if ($stmtCheck->rowCount() > 0) {
            throw new \Exception("⛔ La sala ya está ocupada en esa fecha y horario.");
        }

        $sql = "INSERT INTO funciones (idPelicula, idSala, fechaFuncion, horaInicio, horaFin, precioBase) 
                VALUES (:idPelicula, :idSala, :fecha, :horaInicio, ADDTIME(:horaInicio2, '02:00:00'), :precio)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':idPelicula'  => $datos['idPelicula'],
            ':idSala'      => $datos['idSala'],
            ':fecha'       => $datos['fechaFuncion'],
            ':horaInicio'  => $datos['horaInicio'],
            ':horaInicio2' => $datos['horaInicio'],
            ':precio'      => $datos['precioBase']
        ]);
    }

    public function obtenerTodas(): array {
        $sql = "SELECT f.*, p.titulo, p.imagenPoster 
                FROM funciones f 
                INNER JOIN peliculas p ON f.idPelicula = p.idPelicula 
                ORDER BY f.fechaFuncion ASC, f.horaInicio ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function editar(array $datos): bool {
        $sqlCheck = "SELECT * FROM funciones WHERE idSala = :idSala AND fechaFuncion = :fecha AND horaInicio = :hora AND idFuncion != :id";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([
            ':idSala' => $datos['idSala'],
            ':fecha'  => $datos['fechaFuncion'],
            ':hora'   => $datos['horaInicio'],
            ':id'     => $datos['idFuncion']
        ]);

        if ($stmtCheck->rowCount() > 0) {
            throw new \Exception("⛔ La sala ya está ocupada en esa fecha y horario.");
        }

        $sql = "UPDATE funciones SET idPelicula = :idPelicula, idSala = :idSala, fechaFuncion = :fecha, 
                horaInicio = :horaInicio, horaFin = ADDTIME(:horaInicio2, '02:00:00'), precioBase = :precio 
                WHERE idFuncion = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':idPelicula'  => $datos['idPelicula'],
            ':idSala'      => $datos['idSala'],
            ':fecha'       => $datos['fechaFuncion'],
            ':horaInicio'  => $datos['horaInicio'],
            ':horaInicio2' => $datos['horaInicio'],
            ':precio'      => $datos['precioBase'],
            ':id'          => $datos['idFuncion']
        ]);
    }

    public function eliminar(int $id): bool {
        $sql = "DELETE FROM funciones WHERE idFuncion = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function obtenerCarteleraCajero(): array {
        $sql = "SELECT f.idFuncion as id, p.titulo, p.imagenPoster as imagen, 
                       p.sinopsis, p.genero, p.duracion, p.clasificacion, p.idioma,
                       f.horaInicio as hora, f.fechaFuncion as fecha, s.nombre as sala, 
                       s.filas, s.columnas, s.tipo as tipoSala,
                       f.boletos_vendidos, s.capacidadTotal, f.precioBase as precio,
                       f.asientos_vendidos
                FROM funciones f
                JOIN peliculas p ON f.idPelicula = p.idPelicula
                JOIN salas s ON f.idSala = s.idSala
                ORDER BY f.fechaFuncion ASC, f.horaInicio ASC";

        $stmt = $this->db->query($sql);
        $data = [];

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $vendidos = (int)($row['boletos_vendidos'] ?? 0);
            $capacidadTotal = !empty($row['capacidadTotal'])
                ? (int)$row['capacidadTotal']
                : ((int)$row['filas'] * (int)$row['columnas']);

            $row['disponibles'] = $capacidadTotal - $vendidos;
            $row['llena']       = ($row['disponibles'] <= 0);
            $row['imagen']      = str_starts_with($row['imagen'], 'http')
                ? $row['imagen']
                : 'img/' . $row['imagen'];

            $data[] = $row;
        }
        return $data;
    }

    public function registrarVenta(array $datos): bool {
        // --- FLUJO DEL CAJERO ---

        $stmt = $this->db->prepare("SELECT precioBase, asientos_vendidos, boletos_vendidos FROM funciones WHERE idFuncion = :id");
        $stmt->execute([':id' => $datos['idFuncion']]);
        $funcion = $stmt->fetch(\PDO::FETCH_ASSOC);

        $vendidosActuales = $funcion['asientos_vendidos'] ? explode(',', $funcion['asientos_vendidos']) : [];
        $nuevosAsientos   = $datos['asientos'];

        foreach ($nuevosAsientos as $asiento) {
            if (in_array($asiento, $vendidosActuales)) {
                throw new \Exception("El asiento $asiento ya fue vendido a otra persona.");
            }
        }

        $todosLosAsientos = array_merge($vendidosActuales, $nuevosAsientos);
        $cantidadTotal    = $funcion['boletos_vendidos'] + count($nuevosAsientos);

        $upd = $this->db->prepare("UPDATE funciones SET boletos_vendidos = :cant, asientos_vendidos = :asientos WHERE idFuncion = :id");
        $upd->execute([':cant' => $cantidadTotal, ':asientos' => implode(',', $todosLosAsientos), ':id' => $datos['idFuncion']]);

        $total       = count($nuevosAsientos) * $funcion['precioBase'];
        $codigoTicket = 'CJ-' . strtoupper(substr(uniqid(), -5));
        $ciCliente   = $datos['ciCliente'] ?? '0';

        $insCompra = $this->db->prepare("INSERT INTO compras (CI_cliente, idFuncion, asientos, total, codigo_ticket) VALUES (:ci, :idF, :asientos, :total, :codigo)");
        return $insCompra->execute([
            ':ci'      => $ciCliente,
            ':idF'     => $datos['idFuncion'],
            ':asientos' => implode(', ', $nuevosAsientos),
            ':total'   => $total,
            ':codigo'  => $codigoTicket
        ]);
    }

    public function registrarCompraCliente(array $datos): string {
        // --- FLUJO DEL CLIENTE ONLINE ---
        // Llena: funciones (update) + reservas + tickets + compras
        // Todo dentro de una transacción para que sea atómico.

        try {
            $this->db->beginTransaction();

            // 1. Obtener datos de la función
            $stmt = $this->db->prepare("SELECT idSala, asientos_vendidos, boletos_vendidos FROM funciones WHERE idFuncion = :id");
            $stmt->execute([':id' => $datos['idFuncion']]);
            $funcion = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$funcion) {
                throw new \Exception("La función seleccionada no existe.");
            }

            // 2. Validar que ningún asiento ya esté vendido
            $vendidosActuales = $funcion['asientos_vendidos'] ? explode(',', $funcion['asientos_vendidos']) : [];
            $nuevosAsientos   = $datos['asientos']; // ["A1", "B3"] etiquetas del frontend

            foreach ($nuevosAsientos as $asiento) {
                if (in_array($asiento, $vendidosActuales)) {
                    throw new \Exception("El asiento $asiento ya fue ocupado por otra persona.");
                }
            }

            // 3. Buscar los idAsiento numéricos en la tabla asientos
            //    El frontend manda "A1" => fila='A', numero=1
            $placeholders = implode(',', array_fill(0, count($nuevosAsientos), '?'));
            $stmtAsientos = $this->db->prepare(
                "SELECT idAsiento, CONCAT(fila, numero) as etiqueta
                 FROM asientos
                 WHERE idSala = ?
                 AND CONCAT(fila, numero) IN ($placeholders)"
            );
            $stmtAsientos->execute(array_merge([$funcion['idSala']], $nuevosAsientos));
            $asientosDB = $stmtAsientos->fetchAll(\PDO::FETCH_ASSOC);

            // Si la tabla asientos está vacía para esta sala, los creamos al vuelo
            if (count($asientosDB) !== count($nuevosAsientos)) {
                $asientosDB = $this->crearAsientosFaltantes($funcion['idSala'], $nuevosAsientos);
            }

            $asientosIds = array_column($asientosDB, 'idAsiento'); // [12, 17] IDs numéricos

            // 4. Actualizar tabla funciones
            $todosLosAsientos = array_merge($vendidosActuales, $nuevosAsientos);
            $cantidadTotal    = $funcion['boletos_vendidos'] + count($nuevosAsientos);

            $upd = $this->db->prepare("UPDATE funciones SET boletos_vendidos = :cant, asientos_vendidos = :asientos WHERE idFuncion = :id");
            $upd->execute([
                ':cant'     => $cantidadTotal,
                ':asientos' => implode(',', $todosLosAsientos),
                ':id'       => $datos['idFuncion']
            ]);

            // 5. Insertar en tabla reservas
            $insReserva = $this->db->prepare(
                "INSERT INTO reservas (CICliente, idFuncion, CIEmpleado, estado, montoTotal) 
                 VALUES (:ci, :idF, NULL, 'confirmada', :total)"
            );
            $insReserva->execute([
                ':ci'    => $datos['CI'],
                ':idF'   => $datos['idFuncion'],
                ':total' => $datos['total']
            ]);

            $idReserva = (int)$this->db->lastInsertId();

            // 6. Insertar en tabla tickets (uno por asiento) + marcar asiento como ocupado
            $precioPorBoleto = (float)$datos['total'] / count($asientosIds);

            $stmtTicket = $this->db->prepare(
                "INSERT INTO tickets (idReserva, idAsiento, codigoQR, precioFinal) 
                 VALUES (:reserva, :asiento, :qr, :precio)"
            );
            $stmtAsientoUpd = $this->db->prepare(
                "UPDATE asientos SET estado = 'ocupado' WHERE idAsiento = :idAsiento"
            );

            foreach ($asientosIds as $idAsiento) {
                $codigoQR = md5(uniqid("QR_", true) . $idAsiento);

                $stmtTicket->execute([
                    ':reserva'  => $idReserva,
                    ':asiento'  => $idAsiento,
                    ':qr'       => $codigoQR,
                    ':precio'   => $precioPorBoleto
                ]);

                $stmtAsientoUpd->execute([':idAsiento' => $idAsiento]);
            }

            // 7. Insertar en tabla compras (para el historial del cliente)
            $codigoTicket = 'TK-' . strtoupper(substr(uniqid(), -5));

            $insCompra = $this->db->prepare(
                "INSERT INTO compras (CI_cliente, idFuncion, asientos, total, codigo_ticket) 
                 VALUES (:ci, :idF, :asientos, :total, :codigo)"
            );
            $insCompra->execute([
                ':ci'      => $datos['CI'],
                ':idF'     => $datos['idFuncion'],
                ':asientos' => implode(', ', $nuevosAsientos),
                ':total'   => $datos['total'],
                ':codigo'  => $codigoTicket
            ]);

            // Todo OK → confirmar
            $this->db->commit();
            return $codigoTicket;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Error al procesar la compra: " . $e->getMessage());
        }
    }

    /**
     * Si la sala no tiene asientos pre-cargados en la tabla asientos,
     * los insertamos al vuelo y devolvemos los registros creados.
     * Esto evita que la compra falle por tabla asientos vacía.
     */
    private function crearAsientosFaltantes(int $idSala, array $etiquetas): array {
        $insStmt = $this->db->prepare(
            "INSERT IGNORE INTO asientos (idSala, fila, numero, estado) VALUES (:sala, :fila, :numero, 'disponible')"
        );

        foreach ($etiquetas as $etiqueta) {
            // "A1" => fila=A, numero=1  |  "B12" => fila=B, numero=12
            preg_match('/^([A-Z])(\d+)$/', $etiqueta, $partes);
            if (count($partes) === 3) {
                $insStmt->execute([
                    ':sala'   => $idSala,
                    ':fila'   => $partes[1],
                    ':numero' => (int)$partes[2]
                ]);
            }
        }

        // Volver a buscar ahora que existen
        $placeholders = implode(',', array_fill(0, count($etiquetas), '?'));
        $stmtBuscar   = $this->db->prepare(
            "SELECT idAsiento, CONCAT(fila, numero) as etiqueta
             FROM asientos
             WHERE idSala = ?
             AND CONCAT(fila, numero) IN ($placeholders)"
        );
        $stmtBuscar->execute(array_merge([$idSala], $etiquetas));
        return $stmtBuscar->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function obtenerHistorialCliente(string $ci): array {
        $sql = "SELECT c.codigo_ticket, c.asientos, c.total, 
                       DATE_FORMAT(c.fecha_compra, '%d/%m/%Y %H:%i') as fecha_compra, 
                       f.fechaFuncion, f.horaInicio, p.titulo, p.imagenPoster, s.nombre as sala
                FROM compras c
                JOIN funciones f ON c.idFuncion = f.idFuncion
                JOIN peliculas p ON f.idPelicula = p.idPelicula
                JOIN salas s ON f.idSala = s.idSala
                WHERE c.CI_cliente = :ci
                ORDER BY c.fecha_compra DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ci' => $ci]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
