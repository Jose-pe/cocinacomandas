<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla de Cocina - Pedidos Activos</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/js/app.js'])
    <style>
        body {
            background-color: #212529; /* Fondo oscuro ideal para pantallas de cocina */
            color: #ffffff;
        }
        .card-pedido {
            transition: background-color 0.5s ease, border-color 0.5s ease;
            color: #000000; /* Texto oscuro dentro de las tarjetas */
        }
        /* Clases personalizadas para estados intermedios si Bootstrap no te convence */
        .bg-alerta-naranja {
            background-color: #fd7e14 !important; /* Orange Bootstrap */
            border-color: #fd7e14 !important;
            color: white !important;
        }
        .bg-alerta-roja {
            background-color: #dc3545 !important; /* Red Bootstrap */
            border-color: #dc3545 !important;
            color: white !important;
            animation: pulse-border 2s infinite;
        }
        @keyframes pulse-border {
            0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(220, 53, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
        }

        @keyframes entradaSuave {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-entrada {
            animation: entradaSuave 0.5s forwards;
        }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3">
        <h1 class="fw-bold text-warning"><i class="bi bi-fire me-2"></i>Monitores de Cocina</h1>
        <span class="fs-4  success-color" id="reloj-sistema">00:00:00</span>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4" id="contenedor-pedidos">
        
        @foreach($pedidos as $pedido)
            <div class="col" id="pedido-{{ $pedido->id }}">
                <div class="card card-pedido h-100 bg-white" data-tiempo="{{ $pedido->created_at->toIso8601String() }}">
                    
                    <div class="card-header d-flex justify-content-between align-items-center fw-bold bg-transparent border-bottom border-dark border-opacity-10">
                        <span>Mesa #{{ $pedido->numero_mesa }}</span>
                        <span class="badge bg-dark">#{{ $pedido->id }}</span>
                    </div>

                    <div class="card-body">
                        <h6 class="card-subtitle mb-3 text-uppercase small opacity-75">Mesero: {{ $pedido->nombre_mesero }}</h6>
                        
                        <ul class="list-group list-group-flush border-radius-0">
                            @foreach($pedido->detalles as $detalle)
                                <li class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">({{ $detalle->cantidad }}) {{ $detalle->platillo }}</div>
                                        <small class="d-block text-muted-custom">🥗 {{ $detalle->ensalada }}</small>
                                        <small class="d-block text-muted-custom">🥤 {{ $detalle->bebida }}</small>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        @if($pedido->observaciones)
                            <div class="mt-3 p-2 bg-dark bg-opacity-10 rounded small">
                                <strong>Notas:</strong> {{ $pedido->observaciones }}
                            </div>
                        @endif
                    </div>

                    <div class="card-footer d-flex justify-content-between align-items-center bg-transparent border-top border-dark border-opacity-10">
                        <span class="fw-bold-timer"><i class="bi bi-clock me-1"></i> <span class="tiempo-transcurrido">00:00</span></span>
                        <button class="btn btn-sm btn-success rounded-pill px-3 btn-despachar" data-id="{{ $pedido->id }}">
                            Listo <i class="bi bi-check2-circle ms-1"></i>
                        </button>
                    </div>

                </div>
            </div>
        @endforeach
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const contenedor = document.getElementById('contenedor-pedidos');

        // Escuchar el servidor de WebSockets
        Echo.channel('cocina-canal')
            .listen('.nuevo-pedido', (e) => {
                const pedido = e.pedido;
                
                // 1. Crear el elemento HTML de la nueva tarjeta
                const nuevaColumna = document.createElement('div');
                nuevaColumna.className = 'col animate-entrada';
                nuevaColumna.id = `pedido-${pedido.id}`;
                
                // Construir los detalles (platillos) del pedido en un string
                let detallesHTML = '';
                pedido.detalles.forEach(detalle => {
                    detallesHTML += `
                        <li class="list-group-item bg-transparent px-0 py-2 d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">(${detalle.cantidad}) ${detalle.platillo}</div>
                                <small class="d-block text-muted-custom">🥗 ${detalle.ensalada}</small>
                                <small class="d-block text-muted-custom">🥤 ${detalle.bebida}</small>
                            </div>
                        </li>
                    `;
                });

                // Estructura completa de la tarjeta (idéntica a Blade pero en JS)
                nuevaColumna.innerHTML = `
                    <div class="card card-pedido h-100 bg-white" data-tiempo="${pedido.created_at}">
                        <div class="card-header d-flex justify-content-between align-items-center fw-bold bg-transparent border-bottom border-dark border-opacity-10">
                            <span>Mesa #${pedido.numero_mesa}</span>
                            <span class="badge bg-dark">#${pedido.id}</span>
                        </div>
                        <div class="card-body">
                            <h6 class="card-subtitle mb-3 text-uppercase small opacity-75">Mesero: ${pedido.nombre_mesero}</h6>
                            <ul class="list-group list-group-flush border-radius-0">
                                ${detallesHTML}
                            </ul>
                            ${pedido.observaciones ? `
                                <div class="mt-3 p-2 bg-dark bg-opacity-10 rounded small">
                                    <strong>Notas:</strong> ${pedido.observaciones}
                                </div>
                            ` : ''}
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center bg-transparent border-top border-dark border-opacity-10">
                            <span class="fw-bold-timer"><i class="bi bi-clock me-1"></i> <span class="tiempo-transcurrido">00:00</span></span>
                            <button class="btn btn-sm btn-success rounded-pill px-3 btn-despachar" data-id="${pedido.id}">
                                Listo <i class="bi bi-check2-circle ms-1"></i>
                            </button>
                        </div>
                    </div>
                `;

                // 2. Insertar el nuevo pedido al principio de la pantalla de la cocina
                contenedor.insertBefore(nuevaColumna, contenedor.firstChild);

                // 3. Volver a vincular el evento del botón "Listo" para esta nueva tarjeta
                asignarEventoDespachar(nuevaColumna.querySelector('.btn-despachar'));
                
                // Ejecutar sonido opcional de notificación para alertar a los cocineros
                const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-600.wav');
                audio.play().catch(e => console.log("Sonido bloqueado por el navegador"));
            });

        // Refactorización de la función despachar para reutilizarla con elementos nuevos
        function asignarEventoDespachar(boton) {
            boton.addEventListener('click', function() {
                const pedidoId = this.getAttribute('data-id');
                if(confirm(`¿Despachar pedido #${pedidoId}?`)) {
                    // Petición fetch asíncrona para actualizar el estado en BD sin recargar
                    fetch(`/pedidos/${pedidoId}/despachar`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    }).then(response => {
                        if(response.ok) {
                            const columna = document.getElementById(`pedido-${pedidoId}`);
                            columna.style.transition = 'all 0.5s ease';
                            columna.style.opacity = '0';
                            columna.style.transform = 'scale(0.8)';
                            setTimeout(() => columna.remove(), 500);
                        }
                    });
                }
            });
        }

        // Asignar el evento despachar a los elementos que ya venían cargados por Blade
        document.querySelectorAll('.btn-despachar').forEach(asignarEventoDespachar);
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        
        // 1. Actualizar el reloj general del sistema
        setInterval(() => {
            const ahora = new Date();
            document.getElementById('reloj-sistema').textContent = ahora.toLocaleTimeString();
        }, 1000);

        // 2. Función cronómetro para evaluar y colorear las tarjetas
        function actualizarTemporizadores() {
            const ahora = new Date();
            const tarjetas = document.querySelectorAll('.card-pedido');

            tarjetas.forEach(tarjeta => {
                const fechaCreacion = new Date(tarjeta.getAttribute('data-tiempo'));
                
                // Calcular diferencia en milisegundos y pasarla a minutos/segundos
                const diferenciaMs = ahora - fechaCreacion;
                const totalSegundos = Math.floor(diferenciaMs / 1000);
                const minutos = Math.floor(totalSegundos / 60);
                const segundos = totalSegundos % 60;

                // Formatear el texto del cronómetro (ej. 05:09)
                const tiempoTexto = `${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;
                tarjeta.querySelector('.tiempo-transcurrido').textContent = tiempoTexto;

                // 3. Lógica de cambio de colores por tiempo
                if (minutos >= 15) {
                    // Más de 15 minutos -> ROJO (Peligro/Retraso)
                    tarjeta.classList.remove('bg-white', 'bg-alerta-naranja');
                    tarjeta.classList.add('bg-alerta-roja');
                    
                    // Modificar estilos de los botones o texto interno para legibilidad
                    tarjeta.querySelectorAll('.text-muted-custom').forEach(el => el.style.color = '#ffc107');
                } else if (minutos >= 10) {
                    // Entre 10 y 14.59 minutos -> NARANJA (Advertencia)
                    tarjeta.classList.remove('bg-white', 'bg-alerta-roja');
                    tarjeta.classList.add('bg-alerta-naranja');
                    
                    tarjeta.querySelectorAll('.text-muted-custom').forEach(el => el.style.color = '#f8f9fa');
                } else {
                    // Menos de 10 minutos -> BLANCO (Normal)
                    tarjeta.classList.remove('bg-alerta-naranja', 'bg-alerta-roja');
                    tarjeta.classList.add('bg-white');
                }
            });
        }

        // Ejecutar la actualización cada 1 segundo
        setInterval(actualizarTemporizadores, 1000);
        actualizarTemporizadores(); // Ejecución inmediata al cargar

        // 4. Acción del botón "Listo" (Simulación de despacho)
        document.querySelectorAll('.btn-despachar').forEach(btn => {
            btn.addEventListener('click', function() {
                const pedidoId = this.getAttribute('data-id');
                if(confirm(`¿Despachar pedido #${pedidoId}?`)) {
                    // Aquí harías una petición Fetch/Axios a Laravel para cambiar el estado en la BD
                    // Ejemplo: fetch(`/pedidos/${pedidoId}/despachar`, {method: 'POST'})
                    
                    // Animación visual de salida
                    const columna = document.getElementById(`pedido-${pedidoId}`);
                    columna.style.transition = 'all 0.5s ease';
                    columna.style.opacity = '0';
                    columna.style.transform = 'scale(0.8)';
                    setTimeout(() => columna.remove(), 500);
                }
            });
        });
    });
</script>
</body>
</html>