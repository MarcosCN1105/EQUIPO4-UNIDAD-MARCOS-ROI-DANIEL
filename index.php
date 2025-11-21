<?php
require_once 'conexion.class.php';
require_once 'cochesDao.class.php';

$cochesDao = new cochesDao();
$marcas = $cochesDao->getMarcas();

$modelos = [];
$modeloSeleccionado = null;
$valoraciones = [];
$combustibles = [];
$tipoVehiculo = null;
$puntuacionMedia = 0;
$mostrarResultados = false;

// Solo procesar cuando se envíe el formulario con el botón buscar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar'])) {
    if ($_POST['marca_id'] ?? false) {
        $marcaId = (int)$_POST['marca_id'];
        $modelos = $cochesDao->getModelos($marcaId);
    }

    if ($_POST['modelo_id'] ?? false) {
        $modeloId = (int)$_POST['modelo_id'];
        $modeloSeleccionado = $cochesDao->getModeloCompleto($modeloId);
        $valoraciones = $cochesDao->getValoracionesPorModelo($modeloId);
        $combustibles = $cochesDao->getCombustiblesPorModelo($modeloId);
        $tipoVehiculo = $cochesDao->getTipoVehiculoPorModelo($modeloId);
        $puntuacionMedia = $cochesDao->getPuntuacionMedia($modeloId);
        $mostrarResultados = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CarReviews - Valoraciones de Coches</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            color: white;
        }

        .header h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
            font-weight: 300;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #4a5568;
            font-size: 0.95rem;
        }

        select, input, textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        select:focus, input:focus, textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn-buscar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 10px;
        }

        .btn-buscar:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-buscar:disabled {
            background: #cbd5e0;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .modelo-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .modelo-info h2 {
            font-size: 2rem;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .modelo-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .detail-item {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .detail-item strong {
            display: block;
            margin-bottom: 5px;
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .puntuacion-media {
            text-align: center;
            padding: 20px;
            background: rgba(255,255,255,0.2);
            border-radius: 15px;
            margin-top: 20px;
        }

        .puntuacion-numero {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .estrellas {
            font-size: 2rem;
            letter-spacing: 5px;
        }

        .estrella {
            color: #ffd700;
        }

        .valoraciones-grid {
            display: grid;
            gap: 20px;
            margin-top: 30px;
        }

        .valoracion-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-left: 4px solid #667eea;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .valoracion-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .valoracion-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .puntuacion-valoracion {
            font-size: 1.5rem;
            font-weight: 600;
            color: #ffc107;
        }

        .fecha-valoracion {
            color: #718096;
            font-size: 0.9rem;
        }

        .comentario {
            line-height: 1.6;
            color: #4a5568;
        }

        .form-valoracion {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .form-valoracion h3 {
            color: #2d3748;
            margin-bottom: 25px;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .btn-publicar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-publicar:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }

        .empty-state .icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .logo {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #fff, #e2e8f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .modelo-details {
                grid-template-columns: 1fr;
            }
            
            .card {
                padding: 20px;
            }
            
            .valoracion-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }

        .success-message {
            background: #48bb78;
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .combustible-tag {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 5px 12px;
            border-radius: 20px;
            margin: 2px;
            font-size: 0.85rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">CarReviews</div>
            <h1>Descubre y Comparte Valoraciones</h1>
            <p>Encuentra el coche perfecto basado en experiencias reales</p>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                ¡Valoración agregada correctamente!
            </div>
        <?php endif; ?>

        <!-- Formulario de selección -->
        <div class="card">
            <form method="post" id="form-buscar">
                <div class="form-row">
                    <div class="form-group">
                        <label for="marca_id">Selecciona una marca</label>
                        <select name="marca_id" id="marca_id">
                            <option value="">-- Elige una marca --</option>
                            <?php foreach ($marcas as $marca): ?>
                                <option value="<?= $marca->getId() ?>" 
                                    <?= ($_POST['marca_id'] ?? '') == $marca->getId() ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($marca->getNombre()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="modelo_id">Selecciona un modelo</label>
                        <select name="modelo_id" id="modelo_id" <?= empty($modelos) ? 'disabled' : '' ?>>
                            <option value="">-- Elige un modelo --</option>
                            <?php if (!empty($modelos)): ?>
                                <?php foreach ($modelos as $modelo): ?>
                                    <option value="<?= $modelo->getId() ?>" 
                                        <?= ($_POST['modelo_id'] ?? '') == $modelo->getId() ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($modelo->getNombre()) ?> 
                                        (<?= $modelo->getAnno()->format('Y') ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                
                <button type="submit" name="buscar" class="btn-buscar" id="btn-buscar" disabled>
                    Buscar Valoraciones
                </button>
            </form>
        </div>

        <!-- Información del modelo seleccionado -->
        <?php if ($mostrarResultados && $modeloSeleccionado): ?>
            <div class="modelo-info">
                <h2><?= htmlspecialchars($modeloSeleccionado->getNombre()) ?></h2>
                
                <div class="modelo-details">
                    <div class="detail-item">
                        <strong>Año</strong>
                        <span><?= $modeloSeleccionado->getAnno()->format('Y') ?></span>
                    </div>
                    
                    <?php if ($tipoVehiculo): ?>
                    <div class="detail-item">
                        <strong>Tipo de Vehículo</strong>
                        <span><?= $tipoVehiculo->value ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($combustibles)): ?>
                    <div class="detail-item">
                        <strong>Combustibles</strong>
                        <div>
                            <?php foreach ($combustibles as $combustible): ?>
                                <span class="combustible-tag"><?= $combustible->getCombustible()->value ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="puntuacion-media">
                    <div class="puntuacion-numero"><?= $puntuacionMedia ?></div>
                    <div class="estrellas">
                        <?= str_repeat('★', round($puntuacionMedia)) ?><?= str_repeat('☆', 10 - round($puntuacionMedia)) ?>
                    </div>
                    <div>Puntuación media</div>
                </div>
            </div>

            <!-- Lista de valoraciones -->
            <div class="card">
                <h3 style="margin-bottom: 25px; color: #2d3748;">Valoraciones de usuarios</h3>
                
                <?php if (empty($valoraciones)): ?>
                    <div class="empty-state">
                        <div class="icon">📝</div>
                        <h3 style="color: #4a5568; margin-bottom: 10px;">No hay valoraciones aún</h3>
                        <p style="color: #718096;">Sé el primero en compartir tu experiencia con este modelo</p>
                    </div>
                <?php else: ?>
                    <div class="valoraciones-grid">
                        <?php foreach ($valoraciones as $valoracion): ?>
                            <div class="valoracion-card">
                                <div class="valoracion-header">
                                    <div class="puntuacion-valoracion">
                                        <?= str_repeat('★', $valoracion->getPuntuacion()) ?><?= str_repeat('☆', 10 - $valoracion->getPuntuacion()) ?>
                                        <span style="font-size: 1rem; color: #718096; margin-left: 10px;">
                                            (<?= $valoracion->getPuntuacion() ?>/10)
                                        </span>
                                    </div>
                                    <div class="fecha-valoracion">
                                        <?= $valoracion->getFecha()->format('d/m/Y H:i') ?>
                                    </div>
                                </div>
                                <div class="comentario">
                                    <?= nl2br(htmlspecialchars($valoracion->getComentario() ?? 'Sin comentario')) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Formulario para agregar valoración -->
            <div class="form-valoracion">
                <h3>Agregar tu valoración</h3>
                <form method="post" action="agregar_valoracion.php">
                    <input type="hidden" name="modelo_id" value="<?= $modeloSeleccionado->getId() ?>">
                    
                    <div class="form-group">
                        <label for="usuario_id">Selecciona tu usuario</label>
                        <select name="usuario_id" id="usuario_id" required>
                            <?php 
                            $usuarios = $cochesDao->getUsuarios();
                            foreach ($usuarios as $usuario): ?>
                                <option value="<?= $usuario->getId() ?>"><?= htmlspecialchars($usuario->getNombre()) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="puntuacion">Puntuación (1-10)</label>
                        <input type="number" name="puntuacion" id="puntuacion" min="1" max="10" required 
                               placeholder="Escribe una puntuación del 1 al 10">
                    </div>

                    <div class="form-group">
                        <label for="comentario">Comentario (opcional)</label>
                        <textarea name="comentario" id="comentario" rows="4" 
                                  placeholder="Comparte tu experiencia con este vehículo..."></textarea>
                    </div>

                    <button type="submit" class="btn-publicar">
                        Publicar Valoración
                    </button>
                </form>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="card">
                <div class="empty-state">
                    <div class="icon">🔍</div>
                    <h3 style="color: #4a5568; margin-bottom: 10px;">Selecciona un modelo</h3>
                    <p style="color: #718096;">Por favor, elige una marca y un modelo para ver las valoraciones</p>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="empty-state">
                    <div class="icon">🚗</div>
                    <h3 style="color: #4a5568; margin-bottom: 10px;">Busca valoraciones</h3>
                    <p style="color: #718096;">Selecciona una marca y modelo para comenzar</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const marcaSelect = document.getElementById('marca_id');
            const modeloSelect = document.getElementById('modelo_id');
            const btnBuscar = document.getElementById('btn-buscar');

            // Actualizar modelos cuando cambia la marca
            marcaSelect.addEventListener('change', function() {
                const marcaId = this.value;
                
                if (marcaId) {
                    // Habilitar el select de modelos
                    modeloSelect.disabled = false;
                    
                    // Cargar modelos via AJAX
                    fetch('get_modelos.php?marca_id=' + marcaId)
                        .then(response => response.json())
                        .then(data => {
                            modeloSelect.innerHTML = '<option value="">-- Elige un modelo --</option>';
                            data.forEach(modelo => {
                                const option = document.createElement('option');
                                option.value = modelo.id;
                                option.textContent = `${modelo.nombre} (${modelo.anno})`;
                                modeloSelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                } else {
                    // Deshabilitar y limpiar modelos si no hay marca seleccionada
                    modeloSelect.disabled = true;
                    modeloSelect.innerHTML = '<option value="">-- Elige un modelo --</option>';
                }
                
                updateBuscarButton();
            });

            // Actualizar estado del botón buscar
            modeloSelect.addEventListener('change', updateBuscarButton);

            function updateBuscarButton() {
                if (marcaSelect.value && modeloSelect.value) {
                    btnBuscar.disabled = false;
                } else {
                    btnBuscar.disabled = true;
                }
            }

            // Efectos de hover mejorados
            const cards = document.querySelectorAll('.card, .valoracion-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transition = 'all 0.3s ease';
                });
            });

            // Animación para el formulario de valoración
            const formValoracion = document.querySelector('.form-valoracion');
            if (formValoracion) {
                formValoracion.style.opacity = '0';
                formValoracion.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    formValoracion.style.transition = 'all 0.5s ease';
                    formValoracion.style.opacity = '1';
                    formValoracion.style.transform = 'translateY(0)';
                }, 300);
            }

            // Inicializar estado del botón
            updateBuscarButton();
        });
    </script>
</body>
</html>