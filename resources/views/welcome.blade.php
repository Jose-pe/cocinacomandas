<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Toma de Pedidos</title>
    @vite(['resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #000000;
        }
        .card-pedido {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .platillo-row {
            transition: all 0.3s ease;
            background-color: #ffffff;
            border-radius: 10px;
        }
        .platillo-row:hover {
            background-color: #f1f3f5;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="card card-pedido p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <div>
                        <h2 class="fw-bold text-primary mb-1"><i class="bi bi-receipt-cutoff me-2"></i>Nuevo Pedido</h2>
                        <p class="text-muted mb-0">Comanda digital para meseros</p>
                    </div>
                    <span class="badge bg-secondary p-2 fs-6" id="badge-mesa">Mesa # <span id="num-mesa-text">--</span></span>
                </div>

                <form id="form-pedido" novalidate>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="mesa" class="form-label fw-semibold">Número de Mesa</label>
                            <input type="number" class="form-control form-control-lg" id="mesa" name="mesa" min="1" placeholder="Ej. 5" required>
                        </div>
                        <div class="col-md-6">
                            <label for="mesero" class="form-label fw-semibold">Nombre del Mesero</label>
                            <input type="text" class="form-control form-control-lg" id="mesero" name="mesero" placeholder="Tu nombre" required>
                        </div>
                    </div>

                    <h5 class="fw-bold text-secondary mb-3"><i class="bi bi-egg-fried me-2"></i>Detalle del Pedido</h5>
                    
                    <div id="contenedor-platillos"></div>

                    <div class="mb-4">
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" id="btn-agregar-platillo">
                            <i class="bi bi-plus-circle me-1"></i> Agregar otro platillo
                        </button>
                    </div>

                    <div class="mb-4">
                        <label for="observaciones" class="form-label fw-semibold">Notas Especiales / Alergias</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="2" placeholder="Sin cebolla, término medio, etc..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <button type="button" class="btn btn-light btn-lg px-4" id="btn-limpiar">Limpiar</button>
                        <button type="submit" class="btn btn-primary btn-lg px-5" id="btn-enviar">
                            <span>Enviar a Cocina</span> <i class="bi bi-send ms-2"></i>
                        </button>
                    </div>
                </form>

            </div> 
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const contenedor = document.getElementById('contenedor-platillos');
        const btnAgregar = document.getElementById('btn-agregar-platillo');
        const inputMesa = document.getElementById('mesa');
        const textMesa = document.getElementById('num-mesa-text');
        const formPedido = document.getElementById('form-pedido');
        const btnLimpiar = document.getElementById('btn-limpiar');
        const btnEnviar = document.getElementById('btn-enviar');

        let contadorPlatillos = 0;

        // Actualizar el número de mesa en el badge superior en tiempo real
        inputMesa.addEventListener('input', (e) => {
            textMesa.textContent = e.target.value || '--';
        });

        // Función para agregar una fila de platillo
        function agregarFilaPlatillo() {
            contadorPlatillos++;
            
            const divRow = document.createElement('div');
            divRow.className = 'platillo-row p-3 mb-3 border align-items-center position-relative';
            divRow.id = `platillo-row-${contadorPlatillos}`;

            divRow.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Platillo</label>
                        <select class="form-select" name="platillo[]" required>
                            <option value="" disabled selected>Selecciona un platillo...</option>
                            <option value="Corte de Carne Asada">Corte de Carne Asada</option>
                            <option value="Pechuga de Pollo a la Plancha">Pechuga de Pollo a la Plancha</option>
                            <option value="Hamburguesa de la Casa">Hamburguesa de la Casa</option>
                            <option value="Pasta Alfredo">Pasta Alfredo</option>
                            <option value="Tacos de Barbacoa">Tacos de Barbacoa</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold text-muted">Cantidad</label>
                        <input type="number" class="form-control" name="cantidad[]" min="1" value="1" required>
                    </div>

                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-bold text-muted">Complemento</label>
                        <select class="form-select" name="ensalada[]">
                            <option value="Ninguno">Ninguno</option>
                            <option value="Ensalada César">Ensalada César</option>
                            <option value="Ensalada de la Casa">Ensalada de la Casa</option>
                            <option value="Papas a la francesa">Papas a la francesa</option>
                            <option value="Puré de papa">Puré de papa</option>
                        </select>
                    </div>

                    <div class="col-10 col-md-2">
                        <label class="form-label small fw-bold text-muted">Bebida</label>
                        <select class="form-select" name="bebida[]">
                            <option value="Ninguno">Ninguno</option>
                            <option value="Refresco de Cola">Refresco de Cola</option>
                            <option value="Agua del Día">Agua del Día</option>
                            <option value="Cerveza Nacional">Cerveza Nacional</option>
                            <option value="Té Frío">Té Frío</option>
                        </select>
                    </div>

                    <div class="col-2 col-md-1 d-flex align-items-end justify-content-center">
                        <button type="button" class="btn btn-outline-danger border-0 btn-eliminar-fila" data-id="${contadorPlatillos}">
                            <i class="bi bi-trash3-fill fs-5"></i>
                        </button>
                    </div>
                </div>
            `;

            contenedor.appendChild(divRow);

            divRow.querySelector('.btn-eliminar-fila').addEventListener('click', function() {
                if (contenedor.children.length > 1) {
                    divRow.remove();
                } else {
                    alert('El pedido debe contener al menos un platillo.');
                }
            });
        }

        // Inicializar con un platillo obligatorio al cargar la página
        agregarFilaPlatillo();

        // Evento para el botón de agregar
        btnAgregar.addEventListener('click', agregarFilaPlatillo);

        // Envío asíncrono con FETCH a Laravel
        formPedido.addEventListener('submit', (e) => {
            e.preventDefault();

            // Validación de Bootstrap 5
            if (!formPedido.checkValidity()) {
                e.stopPropagation();
                formPedido.classList.add('was-validated');
                return;
            }

            // Bloquear botón para evitar doble clic accidental
            btnEnviar.disabled = true;
            btnEnviar.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando...`;

            // Recolección nativa de datos mediante FormData
            const formData = new FormData(formPedido);

            // Obtener el Token CSRF desde el meta tag
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Envío por AJAX mediante Fetch API apuntando al endpoint de Laravel
            fetch('/pedido_guardar', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('¡Pedido enviado con éxito a la cocina!');
                    
                    // Resetear el formulario al estado original
                    formPedido.reset();
                    formPedido.classList.remove('was-validated');
                    contenedor.innerHTML = '';
                    textMesa.textContent = '--';
                    agregarFilaPlatillo();
                } else {
                    alert('Hubo un problema al procesar el pedido en el servidor.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión. No se pudo enviar el pedido.');
            })
            .finally(() => {
                // Restaurar el botón de envío
                btnEnviar.disabled = false;
                btnEnviar.innerHTML = `Enviar a Cocina <i class="bi bi-send ms-2"></i>`;
            });
        });

        // Botón de Limpiar todo
        btnLimpiar.addEventListener('click', () => {
            if(confirm('¿Estás seguro de que deseas borrar todo el formulario?')) {
                formPedido.reset();
                formPedido.classList.remove('was-validated');
                contenedor.innerHTML = '';
                textMesa.textContent = '--';
                agregarFilaPlatillo();
            }
        });
    });
</script>
</body>
</html>