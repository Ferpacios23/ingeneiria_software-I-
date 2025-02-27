<?php
session_start();

// Verifica si el usuario ha iniciado sesión y tiene el rol de administrador (id_rol == 2)
if (!isset($_SESSION['id_rol']) || $_SESSION['id_rol'] != 2) {
    // Si no tiene el rol correcto, redirigir al usuario a la página principal
    header("Location: ../index.php");
    exit();
}
include ("./conexion.php");



// Obtener todos los usuarios
$usuarios = $conexion->query("SELECT * FROM Usuarios");

// Obtener todos los roles
$roles = $conexion->query("SELECT * FROM roles");


// Eliminar un usuario
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM Usuarios WHERE id_usuario = $id");
    header('Location: usuarios.php');
    exit();
}

// Obtener el usuario a editar
if (isset($_GET['editar'])) {
    $id_editar = $_GET['editar'];
    $usuario_editar = $conexion->query("SELECT * FROM Usuarios WHERE id_usuario = $id_editar")->fetch_assoc();
}

// Actualizar un usuario
if (isset($_POST['actualizar'])) {
    $id_usuario = $_POST['id_usuario'];
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $email = mysqli_real_escape_string($conexion, $_POST['email']);
    $rol = mysqli_real_escape_string($conexion, $_POST['rol']);
    
    // Verificar si el rol existe en la tabla 'roles'
    $result = $conexion->query("SELECT * FROM roles WHERE id_rol = '$rol'");
    if ($result->num_rows > 0) {
        // Actualizar el usuario solo si el rol es válido
        $conexion->query("UPDATE usuarios SET nombre='$nombre', email='$email', id_rol='$rol' WHERE id_usuario='$id_usuario'");
        header('Location: usuarios.php');
        exit();
    } else {
        echo "<script>alert('El rol seleccionado no es válido.');</script>";
    }
}


?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Admin-Esencias-Chocoanas</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../assets/css/styles2.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="./admin.php">Administrator</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>

        </nav>
        <div id="layoutSidenav">
            <?php include("./nav.php"); ?>
                <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Gestionar Usuarios
                            </div>
                            <?php if (isset($usuario_editar)): ?>
                                <!-- Formulario mejorado -->
                                <form method="POST" class="container p-4 bg-light rounded shadow">
                                    <input type="hidden" name="id_usuario" value="<?php echo $usuario_editar['id_usuario']; ?>">

                                    <!-- Nombre -->
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $usuario_editar['nombre']; ?>" required>
                                    </div>

                                    <!-- Email -->
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Correo Electrónico</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $usuario_editar['email']; ?>" required>
                                    </div>

                                    <!-- Rol -->
                                    <div class="mb-3">
                                        <label for="rol" class="form-label">Rol</label>
                                        <select class="form-select" id="rol" name="rol" required>
                                            <?php while ($rol = $roles->fetch_assoc()): ?>
                                                <option value="<?php echo $rol['id_rol']; ?>" <?php echo ($usuario_editar['id_rol'] == $rol['id_rol']) ? 'selected' : ''; ?>>
                                                    <?php echo $rol['descripcion']; ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>

                                    <!-- Botones de acción -->
                                    <div class="d-flex justify-content-between">
                                        <button type="submit" name="actualizar" class="btn btn-success">
                                            <i class="fas fa-save"></i> Actualizar Usuario
                                        </button>
                                        <a href="usuarios.php" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Cancelar
                                        </a>
                                    </div>
                                </form>
                            <?php endif; ?>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Nombre</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Rol</th>
                                            <th scope="col">Telefono</th>
                                            <th scope="col">Fecha de registro</th>
                                            <th scope="col">Acciones</th>                 
                                        </tr>
                                    </thead>           
                                    <tbody>                                                             
                                        <?php
                                            $conexion=mysqli_connect("localhost","root","","tienda");               
                                            $SQL="SELECT *FROM usuarios";
                                            $dato = mysqli_query($conexion, $SQL);

                                            if($dato -> num_rows >0){

                                                while($fila=mysqli_fetch_array($dato)){
                                                                        
                                            ?>
                                            <tr>
                                            <td><?php echo $fila['id_usuario']; ?></td>
                                            <td><?php echo $fila['nombre']; ?></td>
                                            <td><?php echo $fila['email']; ?></td>
                                            <td><?php echo $fila['id_rol']; ?></td>
                                            <td><?php echo $fila['telefono']; ?></td>
                                            <td><?php echo $fila['fecha_registro']; ?></td>

                                            <td>
                                            <a  class="btn btn-warning" href="usuarios.php?editar=<?php echo $fila['id_usuario']?>   ">Editar </a>

                                            <a class="btn btn-danger" href="usuarios.php?eliminar=<?php echo $fila['id_usuario']?>" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>                                    
                                            </td>
                                            </tr>


                                            <?php
                                            }
                                            }else{

                                                ?>
                                                <tr class="text-center">
                                                <td colspan="16">No existen registros</td>
                                                </tr>

                                                                        
                                                <?php
                                                                        
                                            }
                                            ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2023</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
                </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../assets/js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
    </body>
</html>
