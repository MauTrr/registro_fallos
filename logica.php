<?php
require 'vendor/autoload.php'; // Cargar Composer

try 
{
    // Conexión al servidor MongoDB
    $cliente = new MongoDB\Client(getenv("MONGO_URI"));
    
    $db = $cliente->reportes_tecnicos;
    $coleccion = $db->incidentes;
    
    // Insertar el reporte de incidente
    $insertar = $coleccion->insertOne([
        "nombre" => $_POST["nombre"],
        "email" => $_POST["email"],
        "departamento" => $_POST["departamento"],
        "fecha_incidente" => $_POST["fecha_incidente"],
        "prioridad" => $_POST["prioridad"],
        "tipo_incidente" => $_POST["tipo_incidente"],
        "titulo" => $_POST["titulo"],
        "descripcion" => $_POST["descripcion"],
        "impacto" => $_POST["impacto"],
        "estado" => "Pendiente"
    ]);
    
    echo "<center><h3 style='border:1px solid #00ff00;background-color:#1a4d2e;color:#ffffff;padding:1%;border-radius:8px;'>✓ Reporte registrado exitosamente con código ". $insertar->getInsertedId()."</h3></center>";

    // Consultar todos los reportes
    $consulta = $coleccion->find([], ['sort' => ['fecha_registro' => -1]]);
    
    echo "<style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0000 100%);
            color: #fff;
            padding: 20px;
        }
        .table-container {
            max-width: 1400px;
            margin: 30px auto;
            overflow-x: auto;
            background: #1a1a1a;
            border: 1px solid #ff0000;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 8px 32px rgba(255, 0, 0, 0.2);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background: linear-gradient(135deg, #cc0000 0%, #660000 100%);
            color: #ffffff;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #ff0000;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }
        td {
            padding: 12px 15px;
            border: 1px solid #660000;
            background: #0d0d0d;
            color: #ccc;
        }
        tr:hover td {
            background: #1a0000;
        }
        .prioridad-baja {
            color: #4ade80;
            font-weight: bold;
        }
        .prioridad-media {
            color: #fbbf24;
            font-weight: bold;
        }
        .prioridad-alta {
            color: #fb923c;
            font-weight: bold;
        }
        .prioridad-critica {
            color: #ef4444;
            font-weight: bold;
            text-shadow: 0 0 5px rgba(239, 68, 68, 0.5);
        }
        .estado-pendiente {
            background: #991b1b;
            color: #fca5a5;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 11px;
            display: inline-block;
        }
        .btn-volver {
            display: inline-block;
            margin: 20px auto;
            padding: 12px 30px;
            background: linear-gradient(135deg, #cc0000 0%, #660000 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 1px solid #ff0000;
        }
        .btn-volver:hover {
            background: linear-gradient(135deg, #ff0000 0%, #cc0000 100%);
            box-shadow: 0 5px 20px rgba(255, 0, 0, 0.4);
            transform: translateY(-2px);
        }
        h2 {
            color: #ff3333;
            text-align: center;
            margin: 30px 0;
            text-shadow: 0 0 10px rgba(255, 0, 0, 0.5);
        }
        .descripcion-corta {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>";
    
    echo "<div class='table-container'>";
    echo "<h2>📋 Registro de Incidentes Técnicos</h2>";
    echo "<table>";
    echo "<thead><tr>
            <th>CÓDIGO</th>
            <th>FECHA INCIDENTE</th>
            <th>PRIORIDAD</th>
            <th>TIPO</th>
            <th>TÍTULO</th>
            <th>REPORTADO POR</th>
            <th>DEPARTAMENTO</th>
            <th>DESCRIPCIÓN</th>
            <th>ESTADO</th>
          </tr></thead>";
    echo "<tbody>";
    
    foreach ($consulta as $fila) {
        // Determinar clase de prioridad
        $clasePrioridad = "prioridad-" . strtolower($fila["prioridad"]);
        $prioridadTexto = ucfirst($fila["prioridad"]);
        
        // Formatear fecha
        $fechaIncidente = date('d/m/Y H:i', strtotime($fila["fecha_incidente"]));
        
        echo "<tr>";
        echo "<td style='font-family:monospace;color:#ff6666;'>". substr($fila["_id"], -8) ."</td>";
        echo "<td>". $fechaIncidente ."</td>";
        echo "<td class='$clasePrioridad'>". $prioridadTexto ."</td>";
        echo "<td>". ucfirst($fila["tipo_incidente"]) ."</td>";
        echo "<td style='font-weight:600;color:#fff;'>". $fila["titulo"] ."</td>";
        echo "<td>". $fila["nombre"] ."</td>";
        echo "<td>". ucfirst($fila["departamento"]) ."</td>";
        echo "<td class='descripcion-corta' title='". htmlspecialchars($fila["descripcion"]) ."'>". $fila["descripcion"] ."</td>";
        echo "<td><span class='estado-pendiente'>". $fila["estado"] ."</span></td>";
        echo "</tr>";
    }
    
    echo "</tbody></table>";
    echo "<center><a href='index.html' class='btn-volver'>← Volver al Formulario</a></center>";
    echo "</div>";

} 
catch (Exception $e) 
{
    echo "<style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a0000 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .error-container {
            text-align: center;
        }
        .btn-volver {
            display: inline-block;
            margin: 20px auto;
            padding: 12px 30px;
            background: linear-gradient(135deg, #cc0000 0%, #660000 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 1px solid #ff0000;
        }
    </style>";
    
    echo "<div class='error-container'>";
    echo "<center><h3 style='border:1px solid #ff0000;background-color:#991b1b;color:#ffffff;padding:2%;border-radius:8px;max-width:600px;'>⚠️ Error de conexión: Servidor ocupado, intente más tarde</h3></center>";
    echo "<center><a href='index.html' class='btn-volver'>← Volver al Formulario</a></center>";
    echo "</div>";
}
?>