```
███╗   ███╗██╗   ██╗██╗  ████████╗██╗ ██████╗██╗███╗   ██╗███████╗
████╗ ████║██║   ██║██║  ╚══██╔══╝██║██╔════╝██║████╗  ██║██╔════╝
██╔████╔██║██║   ██║██║     ██║   ██║██║     ██║██╔██╗ ██║█████╗
██║╚██╔╝██║██║   ██║██║     ██║   ██║██║     ██║██║╚██╗██║██╔══╝
██║ ╚═╝ ██║╚██████╔╝███████╗██║   ██║╚██████╗██║██║ ╚████║███████╗
╚═╝     ╚═╝ ╚═════╝ ╚══════╝╚═╝   ╚═╝ ╚═════╝╚═╝╚═╝  ╚═══╝╚══════╝
```

## ╔══════════════════╗

## ║ DESCRIPCION ║

## ╚══════════════════╝

Multicine es un sistema web de gestion de reservas para salas de cine, desarrollado con PHP.
Permite visualizar la disponibilidad de butacas en tiempo real, registrar reservas por funcion
y administrar la informacion de clientes de forma sencilla e intuitiva, todo desde una interfaz
inspirada en la identidad visual de Multicine.

---

## ╔════════════════════════════╗

## ║ PROBLEMA QUE RESUELVE ║

## ╚════════════════════════════╝

La venta y reserva manual de entradas genera errores de duplicacion de asientos, perdida de
informacion y procesos lentos en taquilla. **MulticineReserva** centraliza y digitaliza este proceso:

```
[+] Elimina conflictos de asignacion de butacas
[+] Reduce los tiempos de atencion al cliente
[+] Brinda un registro claro y ordenado de cada funcion
```

---

## ╔════════════════════════╗

## ║ USUARIOS OBJETIVO ║

## ╚════════════════════════╝

| Rol           | Descripcion                                |
| ------------- | ------------------------------------------ |
| Cajero        | Gestiona ventas y reservas en taquilla     |
| Administrador | Administra funciones, peliculas y reportes |
| Cliente       | Reserva asientos y realiza pagos en linea  |

---

## ╔══════════════════════════╗

## ║ EQUIPO DE DESARROLLO ║

## ╚══════════════════════════╝

```
Grupo        :  4
Equipo       :  Proyecto Eddys
Iteracion    :  Segunda Iteracion
```

| #   | Nombre                            |
| --- | --------------------------------- |
| 1   | Michael Gonzalo Salvatierra Lopez |
| 2   | Favio Estefano Sandy Gonzales     |
| 3   | Eddy Limber Vargas Apaza          |
| 4   | Cristhian Alan Vega Ramirez       |
| 5   | Diego Joaquin Yampasi Morales     |

---

## ╔══════════════════════════════════════╗

## ║ HISTORIAS DE USUARIO ║

## ║ Segunda Iteracion ║

## ╚══════════════════════════════════════╝

### ┌───────────────────────────────────────────┐

### │ US-01 · Registro de Cuenta de Cliente │

### └───────────────────────────────────────────┘

| Campo       | Detalle                                                                                                                                  |
| ----------- | ---------------------------------------------------------------------------------------------------------------------------------------- |
| ID          | US-01                                                                                                                                    |
| Descripcion | Como cliente, quiero crear una cuenta con mis datos basicos para que mis preferencias e historial de compras se guarden automaticamente. |
| Prioridad   | Alta                                                                                                                                     |
| Estado      | Completado                                                                                                                               |

**Criterios de Aceptacion:**

| #   | Criterio                                                                                  |
| --- | ----------------------------------------------------------------------------------------- |
| 1   | El sistema debe validar campos obligatorios: Nombre, Correo, Contrasena y Telefono.       |
| 2   | No debe permitir registros con correos electronicos o cedulas de identidad ya existentes. |
| 3   | Tras el login exitoso, el usuario debe ser redirigido a su perfil o cartelera.            |

---

### ┌────────────────────────────────────────────────┐

### │ US-03 · Seleccion Visual de Asientos │

### └────────────────────────────────────────────────┘

| Campo       | Detalle                                                                                                                            |
| ----------- | ---------------------------------------------------------------------------------------------------------------------------------- |
| ID          | US-03                                                                                                                              |
| Descripcion | Como cliente, quiero seleccionar mis asientos preferidos en un mapa visual de la sala para asegurar la mejor vista de la pantalla. |
| Prioridad   | Alta                                                                                                                               |
| Estado      | Completado                                                                                                                         |

**Criterios de Aceptacion:**

| #   | Criterio                                                                           |
| --- | ---------------------------------------------------------------------------------- |
| 1   | Los asientos deben mostrar 3 estados: `Disponible`, `Ocupado` y `Seleccionado`.    |
| 2   | Al seleccionar un asiento, se debe actualizar el contador de asientos disponibles. |

---

### ┌──────────────────────────────────────────┐

### │ US-04 · Pago Seguro de Entradas │

### └──────────────────────────────────────────┘

| Campo       | Detalle                                                                                                            |
| ----------- | ------------------------------------------------------------------------------------------------------------------ |
| ID          | US-04                                                                                                              |
| Descripcion | Como cliente, quiero pagar mis entradas de forma segura con tarjeta o QR para recibir mi confirmacion al instante. |
| Prioridad   | Alta                                                                                                               |
| Estado      | Completado                                                                                                         |

**Criterios de Aceptacion:**

| #   | Criterio                                                                          |
| --- | --------------------------------------------------------------------------------- |
| 1   | Debe mostrar el desglose total antes de pagar.                                    |
| 2   | Debe validar los datos de la tarjeta.                                             |
| 3   | Al finalizar, debe mostrar un mensaje de "Pago Exitoso" y numero de confirmacion. |

---

### ┌──────────────────────────────────────┐

### │ US-05 · Codigo QR de Ingreso │

### └──────────────────────────────────────┘

| Campo       | Detalle                                                                                   |
| ----------- | ----------------------------------------------------------------------------------------- |
| ID          | US-05                                                                                     |
| Descripcion | Como cliente, quiero recibir un codigo QR en la app para ingresar directamente a la sala. |
| Prioridad   | Alta                                                                                      |
| Estado      | Completado                                                                                |

**Criterios de Aceptacion:**

| #   | Criterio                                                        |
| --- | --------------------------------------------------------------- |
| 1   | Debe generar un codigo QR unico con el codigo del ticket.       |
| 2   | El codigo QR debe estar disponible en el historial del cliente. |
| 3   | El QR debe poder ser escaneado.                                 |

---

### ┌──────────────────────────────────────┐

### │ US-06 · Historial de Reservas │

### └──────────────────────────────────────┘

| Campo       | Detalle                                                                                           |
| ----------- | ------------------------------------------------------------------------------------------------- |
| ID          | US-06                                                                                             |
| Descripcion | Como cliente, quiero ver mis reservas para llevar un control de mis consumos o realizar reclamos. |
| Prioridad   | Baja                                                                                              |
| Estado      | Completado                                                                                        |

**Criterios de Aceptacion:**

| #   | Criterio                                                    |
| --- | ----------------------------------------------------------- |
| 1   | Debe listar las compras en orden cronologico inverso.       |
| 2   | Cada registro debe mostrar: Pelicula, fecha y monto pagado. |

---

```
═════════════════════════════════════════════════
  MULTICINE  ·  Sistema de Reservas  ·  Grupo 4  ·
═════════════════════════════════════════════════
```
