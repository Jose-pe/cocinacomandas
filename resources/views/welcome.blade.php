<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toma de Pedidos</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
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
                            <input type="number" class="form-control form-control-lg" id="mesa" min="1" placeholder="Ej. 5" required>
                        </div>
                        <div class="col-md-6">
                            <label for="mesero" class="form-label fw-semibold">Nombre del Mesero</label>
                            <input type="text" class="form-control form-control-lg" id="mesero" placeholder="Tu nombre" required>
                        </div>
                    </div>

                    <h5 class="fw-bold text-secondary mb-3"><i class="bi bi-egg-fried me-2"></i>Detalle del Pedido</h5>
                    
                    <div id="contenedor-platillos">
                        </div>

                    <div class="mb-4">
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill" id="btn-agregar-platillo">
                            <i class="bi bi-plus-circle me-1"></i> Agregar otro platillo
                        </button>
                    </div>

                    <div class="mb-4">
                        <label for="observaciones" class="form-label fw-semibold">Notas Especiales / Alergias</label>
                        <textarea class="form-control" id="observaciones" rows="2" placeholder="Sin cebolla, término medio, etc..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-4">
                        <button type="button" class="btn btn-light btn-lg px-4" id="btn-limpiar">Limpiar</button>
                        <button type="submit" class="btn btn-primary btn-lg px-5">Enviar a Cocina <i class="bi bi-send ms-2"></i></button>
                    </div>
                </form>

            </div> </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const contenedor = document.getElementById('contenedor-platillos');
        const btnAgregar = document.getElementById('btn-agregar-platillo');
        const inputMesa = document.getElementById('mesa');
        const textMesa = document.getElementById('num-mesa-text');
        const formPedido = document.getElementById('form-pedido');
        const btnLimpiar = document.getElementById('btn-limpiar');

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
                        <label class="form-label small fw-bold text-muted">Ensalada / Acompañamiento</label>
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

            // Añadir el evento de eliminar a este botón específico
            divRow.querySelector('.btn-eliminar-fila').addEventListener('click', function() {
                // Evitamos borrar si es la única fila que queda
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

        // Envío del formulario con validación nativa de Bootstrap
        formPedido.addEventListener('submit', (e) => {
            e.preventDefault();

            if (!formPedido.checkValidity()) {
                e.stopPropagation();
                formPedido.classList.add('was-validated');
                return;
            }

            // Recolección de los datos dinámicos si la validación es correcta
            const datosPedido = {
                mesa: inputMesa.value,
                mesero: document.getElementById('mesero').value,
                observaciones: document.getElementById('observaciones').value,
                items: []
            };

            const filas = contenedor.querySelectorAll('.platillo-row');
            filas.forEach(fila => {
                datosPedido.items.push({
                    platillo: fila.querySelector('[name="platillo[]"]').value,
                    cantidad: fila.querySelector('[name="cantidad[]"]').value,
                    ensalada: fila.querySelector('[name="ensalada[]"]').value,
                    bebida: fila.querySelector('[name="bebida[]"]').value
                });
            });

            // Aquí puedes enviar "datosPedido" a tu backend o base de datos.
            console.log('Pedido listo para enviar:', datosPedido);
            alert(`¡Pedido de la Mesa ${datosPedido.mesa} enviado con éxito a la cocina! Verifica la consola del navegador para ver el objeto JSON.`);
            
            // Opcional: Reiniciar formulario
            formPedido.reset();
            formPedido.classList.remove('was-validated');
            contenedor.innerHTML = '';
            textMesa.textContent = '--';
            agregarFilaPlatillo();
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