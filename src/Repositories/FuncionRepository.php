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

        // 2. Usamos :horaInicio2 para no confundir a PDO
        $sql = "INSERT INTO funciones (idPelicula, idSala, fechaFuncion, horaInicio, horaFin, precioBase) 
                VALUES (:idPelicula, :idSala, :fecha, :horaInicio, ADDTIME(:horaInicio2, '02:00:00'), :precio)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':idPelicula'  => $datos['idPelicula'],
            ':idSala'      => $datos['idSala'],
            ':fecha'       => $datos['fechaFuncion'],
            ':horaInicio'  => $datos['horaInicio'],
            ':horaInicio2' => $datos['horaInicio'], // Le pasamos el mismo dato, pero con el segundo nombre
            ':precio'      => $datos['precioBase']
        ]);
    }
    public function obtenerTodas(): array {
        // Traemos las funciones y las cruzamos con las películas para tener el título y la foto
        $sql = "SELECT f.*, p.titulo, p.imagenPoster 
                FROM funciones f 
                INNER JOIN peliculas p ON f.idPelicula = p.idPelicula 
                ORDER BY f.fechaFuncion ASC, f.horaInicio ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function editar(array $datos): bool {
        // Verificamos si la sala choca con otra función (excluyendo esta misma función)
        $sqlCheck = "SELECT * FROM funciones WHERE idSala = :idSala AND fechaFuncion = :fecha AND horaInicio = :hora AND idFuncion != :id";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([
            ':idSala' => $datos['idSala'], ':fecha' => $datos['fechaFuncion'],
            ':hora' => $datos['horaInicio'], ':id' => $datos['idFuncion']
        ]);

        if ($stmtCheck->rowCount() > 0) {
            throw new \Exception("⛔ La sala ya está ocupada en esa fecha y horario.");
        }

        $sql = "UPDATE funciones SET idPelicula = :idPelicula, idSala = :idSala, fechaFuncion = :fecha, 
                horaInicio = :horaInicio, horaFin = ADDTIME(:horaInicio2, '02:00:00'), precioBase = :precio 
                WHERE idFuncion = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':idPelicula'  => $datos['idPelicula'], ':idSala'      => $datos['idSala'],
            ':fecha'       => $datos['fechaFuncion'], ':horaInicio'  => $datos['horaInicio'],
            ':horaInicio2' => $datos['horaInicio'], ':precio'      => $datos['precioBase'],
            ':id'          => $datos['idFuncion']
        ]);
    }

    public function eliminar(int $id): bool {
        // Borrado físico de la función
        $sql = "DELETE FROM funciones WHERE idFuncion = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function obtenerCarteleraCajero(): array {
        // Traemos todo: película, sala, dimensiones y asientos vendidos
        $sql = "SELECT f.idFuncion as id, p.titulo, p.imagenPoster as imagen, 
                       f.horaInicio as hora, f.fechaFuncion as fecha, s.nombre as sala, 
                       s.filas, s.columnas, s.tipo as tipoSala,
                       f.boletos_vendidos, s.capacidad, f.precioBase as precio,
                       f.asientos_vendidos
                FROM funciones f
                JOIN peliculas p ON f.idPelicula = p.idPelicula
                JOIN salas s ON f.idSala = s.idSala
                ORDER BY f.fechaFuncion ASC, f.horaInicio ASC";
        
        $stmt = $this->db->query($sql);
        $data = [];
        
        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $vendidos = (int)$row['boletos_vendidos'];
            $capacidad = (int)$row['capacidad'];
            
            $row['disponibles'] = $capacidad - $vendidos;
            $row['llena'] = ($row['disponibles'] <= 0);
            $row['imagen'] = str_starts_with($row['imagen'], 'http') ? $row['imagen'] : 'img/' . $row['imagen'];
            
            $data[] = $row;
        }
        return $data;
    }

    public function registrarVenta(array $datos): bool {
        // 1. Buscamos los asientos que ya estaban vendidos
        $stmt = $this->db->prepare("SELECT asientos_vendidos, boletos_vendidos FROM funciones WHERE idFuncion = :id");
        $stmt->execute([':id' => $datos['idFuncion']]);
        $funcion = $stmt->fetch(\PDO::FETCH_ASSOC);

        $vendidosActuales = $funcion['asientos_vendidos'] ? explode(',', $funcion['asientos_vendidos']) : [];
        $nuevosAsientos = $datos['asientos'];

        // 2. Verificamos que nadie nos haya ganado el asiento en el último segundo
        foreach ($nuevosAsientos as $asiento) {
            if (in_array($asiento, $vendidosActuales)) {
                throw new \Exception("El asiento $asiento ya fue vendido a otra persona.");
            }
        }

        // 3. Unimos los viejos con los nuevos y sumamos la cantidad
        $todosLosAsientos = array_merge($vendidosActuales, $nuevosAsientos);
        $asientosStr = implode(',', $todosLosAsientos);
        $cantidadTotal = $funcion['boletos_vendidos'] + count($nuevosAsientos);

        // 4. Actualizamos la base de datos
        $upd = $this->db->prepare("UPDATE funciones SET boletos_vendidos = :cant, asientos_vendidos = :asientos WHERE idFuncion = :id");
        return $upd->execute([
            ':cant' => $cantidadTotal,
            ':asientos' => $asientosStr,
            ':id' => $datos['idFuncion']
        ]);
    }
    
    public function registrarCompraCliente(array $datos): string {
        // 1. Verificamos y ocupamos los asientos en la función
        $stmt = $this->db->prepare("SELECT asientos_vendidos, boletos_vendidos FROM funciones WHERE idFuncion = :id");
        $stmt->execute([':id' => $datos['idFuncion']]);
        $funcion = $stmt->fetch(\PDO::FETCH_ASSOC);

        $vendidosActuales = $funcion['asientos_vendidos'] ? explode(',', $funcion['asientos_vendidos']) : [];
        $nuevosAsientos = $datos['asientos'];

        foreach ($nuevosAsientos as $asiento) {
            if (in_array($asiento, $vendidosActuales)) {
                throw new \Exception("El asiento $asiento ya fue ocupado por otra persona.");
            }
        }

        $todosLosAsientos = array_merge($vendidosActuales, $nuevosAsientos);
        $asientosStr = implode(',', $todosLosAsientos);
        $cantidadTotal = $funcion['boletos_vendidos'] + count($nuevosAsientos);

        $upd = $this->db->prepare("UPDATE funciones SET boletos_vendidos = :cant, asientos_vendidos = :asientos WHERE idFuncion = :id");
        $upd->execute([':cant' => $cantidadTotal, ':asientos' => $asientosStr, ':id' => $datos['idFuncion']]);

        // 2. Generamos el Código Único Aleatorio (Ej. TK-A4F8B)
        $codigoTicket = 'TK-' . strtoupper(substr(uniqid(), -5)); 
        $asientosCompradosStr = implode(', ', $nuevosAsientos);
        
        // 3. Guardamos la compra en el historial del cliente
        $ins = $this->db->prepare("INSERT INTO compras (CI_cliente, idFuncion, asientos, total, codigo_ticket) VALUES (:ci, :idF, :asientos, :total, :codigo)");
        $ins->execute([
            ':ci' => $datos['CI'],
            ':idF' => $datos['idFuncion'],
            ':asientos' => $asientosCompradosStr,
            ':total' => $datos['total'],
            ':codigo' => $codigoTicket
        ]);

        return $codigoTicket;
    }

    public function obtenerHistorialCliente(string $ci): array {
        $sql = "SELECT c.codigo_ticket, c.asientos, c.total, DATE_FORMAT(c.fecha_compra, '%d/%m/%Y %H:%i') as fecha_compra, 
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