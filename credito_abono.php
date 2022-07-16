<?php
/**
 * This file is part of the OpenPyme1.
 * 
 * (c) Open Solution Systems <operaciones@tumundolaboral.com.sv>
 * 
 * For the full copyright and license information, please refere to LICENSE file
 * that has been distributed with this source code.
 */
include ("_core.php");
function initial(){

	//Cliente seleccionado en la seccion de venta
	$id_cliente_seleccionado = $_REQUEST['id_cliente'];
	$sql_cliente = _query(
		"SELECT * FROM cliente WHERE id_cliente='$id_cliente_seleccionado'"
	);
	$row_cliente = _fetch_array($sql_cliente);
	$nombre_cliente = $row_cliente['nombre'];
	$dui_cliente = $row_cliente['dui'];

	$telefono_cliente = (($row_cliente['telefono1'] != "") ? 
		$row_cliente['telefono1'] : $row_cliente['telefono2']);

	//permiso del script
	$id_user=$_SESSION["id_usuario"];
	$id_sucursal=$_SESSION['id_sucursal'];
	date_default_timezone_set('America/El_Salvador');
	$uri = $_SERVER['SCRIPT_NAME'];
	$filename=get_name_script($uri);
	$links=permission_usr($id_user,$filename);

	$sql_apertura = _query("SELECT * FROM apertura_caja 
		WHERE vigente = 1 
		AND id_sucursal = $id_sucursal 
		AND id_empleado = $id_user");
	$cuenta = _num_rows($sql_apertura);

	if($cuenta > 0)
	{
		?>
		<div class="modal-header">
			<button type="button" 
				class="close" 
				data-dismiss="modal"
				aria-hidden="true">&times;</button>
			<h4 class="modal-title">Agregar nueva nota de abono</h4>
		</div>
		<div class="modal-body">
			
				<ul class="nav nav-tabs" id="myTab" role="tablist">
					<li id="cl_existente" class="nav-item active" style='margin-right:1%;'>
						<button class="btn btn-info" id="existente-tab" data-toggle="tab" href="#existente" role="tab" aria-controls="existente" aria-selected="true">Cliente existente</button>
					</li>
					<li id="cl_nuevo" class="nav-item">
						<button class="btn btn-success" id="nuevo-tab" data-toggle="tab" href="#nuevo" role="tab" aria-controls="nuevo" aria-selected="false">Cliente nuevo</button>
					</li>
				</ul>
				<div class="tab-content" id="myTabContent">

					<!-- Fomulario para cliente existente -->
					<div class="tab-pane fade active in" id="existente" role="tabpanel" aria-labelledby="existente-tab">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class='text-success'><a data-toggle="collapse" href="#collapse22" class="change" act="down">
									Cliente existente<i class="fa fa-angle-double-down pull-right"></i></a>
								</h4>
							</div>
							<div id="collapse22" class="collapse panel-collapse in">
								<div class="panel-body">
									<div class="widget-content">
									<?php
									if($id_cliente_seleccionado != -1){
									?>
										<!-- AQUI COLOCAR FORMULARIO PARA CLIENTE EXISTENTE -->
										<div class="row">
											<!-- Nombre del cliente -->
											<input type="hidden" name="cliente_seleccionado" 
												id="cliente_seleccionado"
												value="<?php echo $id_cliente_seleccionado; ?>">
											<div class="col-md-12">
												<div class="form-group has-info single-line">
												<label>Nombre del cliente</label>
												<input type="text" placeholder="Nombre del Cliente" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre_cliente; ?>" readonly>
												</div>
											</div>
										</div>
										<div class="row">
											<!-- Telefono del cliente -->
											<div class="col-md-6">
												<div class="form-group has-info single-line">
												<label>Telefono</label>
												<input type="text" placeholder="Nombre del Cliente" class="form-control" id="nombre" name="nombre" value="<?php echo $telefono_cliente; ?>" readonly>
												</div>
											</div>
											<!-- Dui del cliente -->
											<div class="col-md-6">
												<div class="form-group has-info single-line">
												<label>Dui</label>
												<input type="text" placeholder="Nombre del Cliente" class="form-control" id="nombre" name="nombre" value="<?php echo $dui_cliente; ?>" readonly>
												</div>
											</div>
										</div>
										<div class="row">
											<!-- Monto a abonar a esta cuenta por cobrar -->
											<div class="col-md-6">
												<div class="form-group has-info single-line">
												<label>Cantidad a abonar</label>
												<input type="number" placeholder="Digitar cantidad a abonar" 
													class="form-control cant_abonar" 
													id="cant_abonar_exis" 
													name="cant_abonar_exis" 
													value=""
													min="0">
												</div>
											</div>
										</div>
									<?php
									}else{
										echo "<div></div><br><br><div class='alert alert-warning text-center'> <h4>
											El cliente seleccionado no es un cliente válido. <br> 
											Si es un cliente nuevo, por favor regístrelo en el apartado superior. </h4></div>";
									}
									?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Formulario para cliente nuevo -->
					<div class="tab-pane fade" id="nuevo" role="tabpanel" aria-labelledby="nuevo-tab">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class='text-success'><a data-toggle="collapse" href="#collapse11" class="change" act="down">
									Cliente nuevo<i class="fa fa-angle-double-down pull-right"></i></a></h4>
							</div>
							<div id="collapse11" class="collapse panel-collapse in">
								<div class="panel-body">
									<div class="widget-content">
									<?php
									if($id_cliente_seleccionado == -1){
									?>
										<!-- AQUI COLOCAR FORMULARIO PARA CLIENTE NUEVO -->
										<div class="row">
											<!-- Nombre del cliente -->
											<input type="hidden" name="cliente_seleccionado" 
												id="cliente_seleccionado"
												value="<?php echo $id_cliente_seleccionado; ?>">
											<div class="col-md-12">
												<div class="form-group has-info single-line">
												<label>Nombre del cliente</label>
												<input type="text" placeholder="Nombre del Cliente" class="form-control input_text" id="nombre_new" name="nombre_new" value="">
												</div>
											</div>
										</div>
										<div class="row">
											<!-- Telefono del cliente -->
											<div class="col-md-6">
												<div class="form-group has-info single-line">
												<label>Telefono</label>
												<input type="text" placeholder="Telefono del Cliente" class="form-control tel" id="telefono_new" name="telefono_new" value="">
												</div>
											</div>
											<!-- Dui del cliente -->
											<div class="col-md-6">
												<div class="form-group has-info single-line">
												<label>Dui</label>
												<input type="text" placeholder="DUI del Cliente" class="form-control dui" id="dui_new" name="dui_new" value="">
												</div>
											</div>
										</div>
										<div class="row">
											<!-- Monto a abonar a esta cuenta por cobrar -->
											<div class="col-md-6">
												<div class="form-group has-info single-line">
												<label>Cantidad a abonar</label>
												<input type="number" placeholder="Digitar cantidad a abonar" 
													class="form-control cant_abonar" 
													id="cant_abonar" 
													name="cant_abonar" 
													value=""
													min="0">
												</div>
											</div>
										</div>
									<?php
									}else{
										echo "<div></div><br><br><div class='alert alert-warning text-center'> <h4>
											Ya ha seleccionado un cliente registrado.</h4></div>";
									}
									?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

		</div>
		<div class="modal-footer">
			<button type="button"  class="btn btn-primary" id="guardar_credito">Agregar</button>
			<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
		</div>
		<script type="text/javascript">
			$(document).ready(function () {
				/**
				 * VALIDACION DE CAMPO DE DUI 
				 */
				$('.dui').on('keydown', function(event) {
					if (event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 13 || event.keyCode == 37 || event.keyCode == 39) {
				
					} else {
						if ((event.keyCode > 47 && event.keyCode < 60) || (event.keyCode > 95 && event.keyCode < 106)) {
							inputval = $(this).val();
							var string = inputval.replace(/[^0-9]/g, "");
							var bloc1 = string.substring(0, 8);
							var bloc2 = string.substring(8, 8);
							var string = bloc1 + "-" + bloc2;
							$(this).val(string);
						} else {
							event.preventDefault();
						}
					}
				});

				/**
				 * VALIDACION DE CAMPO DE NUMERO TELEFONICO
				 */
				$('.tel').on('keydown', function(event) {
					if (event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 13 || event.keyCode == 37 || event.keyCode == 39) {

					} else {
						if ((event.keyCode > 47 && event.keyCode < 60) || (event.keyCode > 95 && event.keyCode < 106)) {
							inputval = $(this).val();
							var string = inputval.replace(/[^0-9]/g, "");
							var bloc1 = string.substring(0, 4);
							var bloc2 = string.substring(4, 7);
							var string = bloc1 + "-" + bloc2;
							$(this).val(string);
						} else {
							event.preventDefault();
						}
					}
				});

				/**
				 * VALIDACION PARA TRANSFORMAR TODO EL TEXTO INTRODUCIDO 
				 * EN UN INPUT A MAYUSCULAS
				 */
				$('.input_text').on('keyup', function(evt){
					$(this).val($(this).val().toUpperCase());
				});

				/**
				 *  VALIDACION PARA SOLO PERMITIR NUMEROS EN INPUTS NUMERICOS
				 */
				$('.cant_abonar').numeric({
					negative: false
				});

				/**
				 * CONSULTAMOS EN EL LOCALSTORAGE SI TENEMOS UN DATO DE CLIENTE
				 * NUEVO PREVIAMENTE ASIGNADO
				 */
				if(localStorage.getItem("cliente_nuevo")){

					let cliente_nuevo = JSON.parse(localStorage.getItem("cliente_nuevo"));

					/**
					 * SETEAMOS LO ENCONTRADO EN EL LOCALSTORAGE A LOS
					 * INPUTS DEL MODAL DE CLIENTE NUEVO
					 */
					$("#nombre_new").val(cliente_nuevo.nombre);
					$("#telefono_new").val(cliente_nuevo.telefono);
					$("#dui_new").val(cliente_nuevo.dui);
					$('#cant_abonar').val(cliente_nuevo.cant_abonar);

					//ACTIVANDO PAGE TAB DE CLIENTE NUEVO
					$("#cl_existente").removeClass("active");
					$("#cl_nuevo").addClass("active");

					//ACTIVANDO PAGE TAB DE CLIENTE NUEVO
					$("#existente").removeClass("active in");
					$("#nuevo").addClass("active in");
					
				}

				/**
				 * ALMACENANDO EN LOCALSTORAGE DATOS DE UN CLIENTE
				 * NUEVO
				 */
				$('#guardar_credito').click(function (e) { 
					e.preventDefault();

					$cliente_seleccionado = $('#cliente_seleccionado').val();

					if($cliente_seleccionado == -1)
					{
						let nombre = $('#nombre_new').val();
						let telefono = $('#telefono_new').val();
						let dui = $('#dui_new').val();
						let cant_abonar = $('#cant_abonar').val();

                        $.ajax({
                            type: "POST",
                            url: "credito_abono.php",
							dataType: 'JSON',
                            data: {
                                nombre,
                                dui,
								process: 'verificarCliente'
                            },
                            success: function (response) {
								console.log('entro al verificar');
								console.log(response.typeinfo);

                                if(response.typeinfo == 'Error')
								{
                                    swal({
                                        type: 'error',
                                        title: response.msg,
                                        showConfirmButton: false,
                                        timer: 2300
                                    })
                                }
								else
								{
									$.ajax({
										type: "POST",
										url: "credito_abono.php",
										dataType: 'JSON',
										data: {
											nombre, 
											telefono,
											dui,
											cant_abonar,
											process: 'insertLocal'
										},
										success: function (response) {
											
											/**
											 * COLOCAMOS EN EL LOCALSTORAGE LA INFORMACION CAPTURADA EN EL 
											 * FORMULARIO DE CLIENTE NUEVO
											 */
											let datos_cliente = localStorage.setItem("cliente_nuevo", JSON.stringify(response.cliente_nuevo));

											swal({
												type: 'success',
												title:'Datos de cliente asignados correctamente',
												showConfirmButton: false,
												timer: 2300
											})
											$('#creditoAbono').modal('hide');

											/**
											 * DESACTIVAMOS EL BOTON DE PAGAR YA QUE LO QUE 
											 * SE ESTA REALIZANDO CON ESTA ACCION ES UNA REFERENCIA
											 * POR LO TANTO PARA EVITAR UN MAL PASO EN EL PROCESO
											 * SE DESACTIVA ESTE BOTON PARA LIMITAR AL USUARIO QUE 
											 * REALIZE LA REFERENCIA
											 */
											$('#submit1').prop('disabled', true);
											/** 
											 * A su vez, dentro de un input decimos que este 
											 * cliente es un cliente nuevo
											 */
											$('#cliente_nuevo').val(1);
										}
									});
								}
                            },
							error: function (response) {
								
							}
                        });
					}
					else
					{
						/**
						 * Si es un cliente existente solo obtenemos la
						 * cantidad a abonar y la seteamos en un input oculto
						 * en la seccion de ventas
						 */
						let cant_abonar_exis = $('#cant_abonar_exis').val();
						$('#cant_abonar_cl_exis').val(cant_abonar_exis);

						swal({
							type: 'success',
							title:'Datos de cliente asignados correctamente',
							showConfirmButton: false,
							timer: 2300
						})
						$('#creditoAbono').modal('hide');

						/**
						 * DESACTIVAMOS EL BOTON DE PAGAR YA QUE LO QUE 
						 * SE ESTA REALIZANDO CON ESTA ACCION ES UNA REFERENCIA
						 * POR LO TANTO PARA EVITAR UN MAL PASO EN EL PROCESO
						 * SE DESACTIVA ESTE BOTON PARA LIMITAR AL USUARIO QUE 
						 * REALIZE LA REFERENCIA
						 */
						$('#submit1').prop('disabled', true);
					}
				});
			});
		</script>
		<?php
	}
	else
	{
		echo "<div></div><br><br><div class='alert alert-warning text-center'>No se ha encontrado una apertura vigente.</div>";
	}
}

function guardarLocal() 
{
    if(
		isset($_POST["nombre"]) 
		&& isset($_POST["telefono"])
		&& isset($_POST["dui"])
		&& isset($_POST["cant_abonar"])
	)	
	{
        $nombre_cliente = $_POST["nombre"];
        $telefono_cliente = $_POST["telefono"];
        $dui_cliente = $_POST["dui"];
		$cant_abonar = $_POST["cant_abonar"];
        
        $json = array(
            'nombre' => $nombre_cliente,
            'telefono' => $telefono_cliente,
            'dui' => $dui_cliente,
			'cant_abonar' => $cant_abonar
        );
        $xdatos['cliente_nuevo'] = $json;
    }
    else{
        $xdatos ['typeinfo'] = 'Error';
	    $xdatos ['msg'] = 'Datos no pudieron ser cargados';
    }

	echo json_encode ($xdatos);
}

function verificarCliente(){

	$nombre=$_POST["nombre"];
  	$dui=$_POST["dui"];
	$xdatos = [];
	
	$id_sucursal = $_SESSION["id_sucursal"];
	$sql_exis=_query("SELECT id_cliente FROM cliente WHERE id_sucursal='$id_sucursal' AND (dui='$dui' OR nombre ='$nombre')");
	$num_exis = _num_rows($sql_exis);
	if($num_exis > 0)
	{
		$xdatos['typeinfo']='Error';
		$xdatos['msg']='Ya se registro un cliente con estos datos!';
	}

	echo json_encode ($xdatos);
}

if (! isset ( $_REQUEST ['process'] )) {
	initial();
} else {
	if (isset ( $_REQUEST ['process'] )) {
		switch ($_REQUEST ['process']) {
			case 'insertLocal' :
				guardarLocal();
			break;
			case 'propina' :
				insertar_propina();
			break;
			case 'verificarCliente' :
				verificarCliente();
			break;
		}
	}
}

?>
