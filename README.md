```
███╗   ███╗██╗   ██╗██╗  ████████╗██╗ ██████╗██╗███╗   ██╗███████╗
████╗ ████║██║   ██║██║  ╚══██╔══╝██║██╔════╝██║████╗  ██║██╔════╝
██╔████╔██║██║   ██║██║     ██║   ██║██║     ██║██╔██╗ ██║█████╗
██║╚██╔╝██║██║   ██║██║     ██║   ██║██║     ██║██║╚██╗██║██╔══╝
██║ ╚═╝ ██║╚██████╔╝███████╗██║   ██║╚██████╗██║██║ ╚████║███████╗
╚═╝     ╚═╝ ╚═════╝ ╚══════╝╚═╝   ╚═╝ ╚═════╝╚═╝╚═╝  ╚═══╝╚══════╝
```
> 🎬  *Sistema integral de gestión y reservas cinematográficas*

---

## ╔══════════════════════════════════════╗
## ║         ¿QUÉ ES MULTICINE?          ║
## ╚══════════════════════════════════════╝

**MULTICINE** es una plataforma completa para la gestión de cines que
unifica tres experiencias en una sola solución:

```
  ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
  │   CLIENTE   │     │   CAJERO    │     │    ADMIN    │
  │             │     │             │     │             │
  │  Reserva    │     │  Vende en   │     │  Controla   │
  │  entradas   │────▶│  taquilla   │────▶│  todo el    │
  │  desde web  │     │  y aplica   │     │  negocio    │
  │             │     │  descuentos │     │             │
  └─────────────┘     └─────────────┘     └─────────────┘
```

---

## ╔══════════════════════════════════════╗
## ║        PROBLEMA QUE RESUELVE        ║
## ╚══════════════════════════════════════╝

```
  ANTES ❌                          DESPUÉS ✅
  ─────────────────────────         ─────────────────────────
  Procesos 100% manuales     ──▶    Automatización total
  Precios inconsistentes     ──▶    Tarifas unificadas
  Sin datos de ocupación     ──▶    Reportes en tiempo real
  Web y taquilla separadas   ──▶    Un solo sistema integrado
  Gestión de empleados caos  ──▶    Roles y permisos claros
```

---

## ╔══════════════════════════════════════╗
## ║          USUARIOS OBJETIVO          ║
## ╚══════════════════════════════════════╝

```
  ╭──────────────────┬───────────────────────────────────────╮
  │  🧑‍💻  CLIENTE    │  Reserva entradas cómodamente desde   │
  │                  │  la web, elige asientos y paga online. │
  ├──────────────────┼───────────────────────────────────────┤
  │  🏦  CAJERO      │  Atiende en taquilla, busca socios,   │
  │                  │  aplica descuentos e imprime tickets.  │
  ├──────────────────┼───────────────────────────────────────┤
  │  🛠️  ADMIN       │  Configura precios, gestiona empleados │
  │                  │  y analiza reportes de rentabilidad.   │
  ╰──────────────────┴───────────────────────────────────────╯
```

---

## ╔══════════════════════════════════════════════════════════╗
## ║       HISTORIAS DE USUARIO  ——  ITERACIÓN 3             ║
## ╚══════════════════════════════════════════════════════════╝

```
  ┌─────────────────────────────────────────────────────────┐
  │  US-09  │  Descuentos de Lealtad por CI          🟡 MED │
  ├─────────────────────────────────────────────────────────┤
  │                                                         │
  │  "Como cajero, quiero ingresar el CI del cliente        │
  │   para aplicar descuentos de lealtad en el precio."     │
  │                                                         │
  │  ✔  Campo de búsqueda por CI o Número de Socio          │
  │  ✔  Descuento aplicado automáticamente al total         │
  │  ✔  Resumen del ahorro visible en pantalla de cobro     │
  │                                                         │
  │                               Estado: [██████████] ✅   │
  └─────────────────────────────────────────────────────────┘

  ┌─────────────────────────────────────────────────────────┐
  │  US-10  │  Impresión de Ticket Físico            🟡 MED │
  ├─────────────────────────────────────────────────────────┤
  │                                                         │
  │  "Como cajero, quiero imprimir un ticket físico         │
  │   por requerimiento fiscal o solicitud del cliente."    │
  │                                                         │
  │  ✔  Botón de impresión tras confirmar la venta          │
  │  ✔  Ticket incluye: Cine, Película, Sala,               │
  │     Asientos y Código de Acceso                         │
  │  ✔  Re-impresión disponible ante fallas técnicas        │
  │                                                         │
  │                               Estado: [██████████] ✅   │
  └─────────────────────────────────────────────────────────┘

  ┌─────────────────────────────────────────────────────────┐
  │  US-13  │  Tarifas Diferenciadas y Promociones   🔴 ALT │
  ├─────────────────────────────────────────────────────────┤
  │                                                         │
  │  "Como admin, quiero configurar tarifas Niño/Adulto     │
  │   y promociones por días específicos."                  │
  │                                                         │
  │  ✔  Formulario de precios base por categoría de edad    │
  │  ✔  Precios especiales por día (ej. Miércoles)          │
  │  ✔  Cambios reflejados en Web y Taquilla al instante    │
  │                                                         │
  │                               Estado: [██████████] ✅   │
  └─────────────────────────────────────────────────────────┘

  ┌─────────────────────────────────────────────────────────┐
  │  US-14  │  Reportes de Ocupación y Recaudación   🟡 MED │
  ├─────────────────────────────────────────────────────────┤
  │                                                         │
  │  "Como admin, quiero generar reportes diarios de        │
  │   ocupación y recaudación para analizar rentabilidad."  │
  │                                                         │
  │  ✔  Filtros por fecha: día / semana / mes               │
  │  ✔  % de ocupación por sala y película                  │
  │  ✔  Exportación a Excel o PDF                           │
  │                                                         │
  │                               Estado: [██████████] ✅   │
  └─────────────────────────────────────────────────────────┘

  ┌─────────────────────────────────────────────────────────┐
  │  US-15  │  Gestión de Empleados y Permisos       🟡 MED │
  ├─────────────────────────────────────────────────────────┤
  │                                                         │
  │  "Como admin, quiero gestionar cuentas de empleados     │
  │   y sus permisos para proteger el sistema."             │
  │                                                         │
  │  ✔  Crear usuarios con roles: Cajero / Admin            │
  │  ✔  Desactivar cuentas de empleados inactivos           │
  │  ✔  Log de actividad por usuario                        │
  │                                                         │
  │                               Estado: [██████████] ✅   │
  └─────────────────────────────────────────────────────────┘
```

---

## ╔══════════════════════════════════════╗
## ║         PROGRESO DEL SPRINT         ║
## ╚══════════════════════════════════════╝

```
  Iteración 3  ──────────────────────────────────  100%

  US-09  Descuentos de lealtad       [██████████]  ✅
  US-10  Impresión de tickets        [██████████]  ✅
  US-13  Tarifas y promociones       [██████████]  ✅
  US-14  Reportes de ocupación       [██████████]  ✅
  US-15  Gestión de empleados        [██████████]  ✅

  Completadas: 5 / 5   ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓  🎉
```

---

## ╔══════════════════════════════════════╗
## ║           EQUIPO DE TRABAJO         ║
## ╚══════════════════════════════════════╝

```
  ╭─────────────────────────────────────────╮
  │           🎬  PROYECTO EDDYS            │
  ├─────────────────────────────────────────┤
  │                                         │
  │  👤  Michael Gonzalo Salvatierra Lopez  │
  │  👤  Favio Estefano Sandy Gonzales      │
  │  👤  Eddy Limber Vargas Apaza           │
  │  👤  Cristhian Alan Vega Ramirez        │
  │  👤  Diego Joaquin Yampasi Morales      │
  │                                         │
  ╰─────────────────────────────────────────╯
```

---

```
  ════════════════════════════════════════════════════════════
    MULTICINE  —  Llevando la experiencia del cine al
                  siguiente nivel  🎥🍿
  ════════════════════════════════════════════════════════════
```