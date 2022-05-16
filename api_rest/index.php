<?php
    include 'config.php';
    include 'tools.php';
    $db=new Database();
    
    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        if(isset($_GET['date'])){
            $consulta=$db->connect()->prepare("select `start_time` from `appoinment` where `date`=:date  order by `start_time`; ");
            $consulta->execute(['date' => $_GET['date']]);
            header("HTTP/1.1 200 OK");
            echo json_encode($consulta->fetchAll(PDO::FETCH_ASSOC));
            exit();
        }
    } 

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if(isset($_POST['date']) && isset($_POST['hora'])){
            $fecha=$_POST['date'];
            $hora=$_POST['hora'];
            $correo=$_POST['correo'];
            $resultado=verificarHoraLibre($fecha,$hora);
            if($resultado==true){
                //verificar que el usuario no tenga otra hora en el día
                $respuesta=verificarUsuarioRegistrado($fecha,$correo);
                if($respuesta==false){
                    //insertar hora
                    $final=registrarCita($fecha,$hora,$correo);
                    if($final==true){
                        echo json_encode('El registro fue exitoso.');
                    }else{
                        echo json_encode('No se pudo registrar la cita.');
                    }
                }else{
                    echo json_encode('El usuario ya registró una hora con la muerte en este día.');
                }
            }else{
                echo json_encode('La hora ya se encuentra ocupada');
            }
        }
        exit();
    }
    header("HTTP/1.1 400 Bad Request");

    function verificarHoraLibre($fecha,$hora){
        
        $db=new Database();
        $consulta=$db->connect()->prepare("select `start_time` from `appoinment` where `date`=:date  and `start_time`=:hora; ");
        $consulta->execute(['date' => $fecha,'hora' => $hora]);
        $resultado=$consulta->fetchAll(PDO::FETCH_ASSOC);
        if($resultado!=null){
            //si hay hora registrada la hora no está libre
            return false;
        }else{
            return true;
        }
    }

    function verificarUsuarioRegistrado($fecha,$correo){
        $db=new Database();
        $consulta=$db->connect()->prepare("select `start_time` from `appoinment` where `date`=:date  and `contact`=:correo; ");
        $consulta->execute(['date' => $fecha,'correo' => $correo]);
        $resultado=$consulta->fetchAll(PDO::FETCH_ASSOC);
        if($resultado!=null){
            //ya está el usuario registrado este día, no puede reservar más horas
            return true;
        }else{
            return false;
        }
    }


    function registrarCita($fecha,$hora,$correo){
        try {
            $db=new Database();
            $consulta=$db->connect()->prepare("INSERT INTO `appoinment`(`date`, `start_time`, `contact`) 
            VALUES(:fecha, :hora, :email);");
            $consulta->execute(['fecha' => $fecha,'hora' => $hora,'email' => $correo]);
            return true;
        } catch (PDOException $e) {
            print_r('Error connection'.$e->getMessage());
            return false;
        }
    }

?>