<?php

use App\BlChoferNotificationSent;
use App\BlDriver;
use App\Clients\EfactClient;
use App\Clients\EfactCordClient;
use App\Mail\NotificacionBlDriver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('/', 'Backend\BillingController@test')->name('home');

Route::get('/preventa/start', 'Backend\PreventaController@start')->name('dashboard.preventa.start');
Route::get('/pdd/preventa', 'Backend\PreventaController@index')->name('dashboard.preventa.index');
Route::post('/pdd/preventa/acceder', 'Backend\PreventaController@login')->name('dashboard.preventa.login');
Route::post('/pdd/preventa/acceder/listar-clientes', 'Backend\PreventaController@getClients')->name('dashboard.preventa.get_clients');
Route::post('/pdd/preventa/acceder/obtener-precio', 'Backend\PreventaController@getArticlePrice')->name('dashboard.preventa.get_price');
Route::post('/pdd/preventa/acceder/listar-metodos-pago', 'Backend\PreventaController@getPaymentMethod')->name('dashboard.preventa.get_payment_method');
Route::post('/pdd/preventa/acceder/verificar-credito', 'Backend\PreventaController@getVerifyCredit')->name('dashboard.preventa.verify_credit');
Route::post('/pdd/preventa/acceder/listar-guias', 'Backend\PreventaController@getGuias')->name('dashboard.preventa.guias');
Route::post('/pdd/preventa/acceder/finalizar-preventa', 'Backend\PreventaController@store')->name('dashboard.preventa.store');
Route::post('/pdd/preventa/acceder/finalizar-preventa-pre', 'Backend\PreventaController@store_pre')->name('dashboard.preventa.store_pre');
Route::post('/pdd/preventa/acceder/listar-clientes-cobranza', 'Backend\PreventaController@getClientsCobranza')->name('dashboard.preventa.get_clients_cobranza');
Route::post('/pdd/preventa/acceder/listar-ventas-cobranza', 'Backend\PreventaController@getSaleCobranza')->name('dashboard.preventa.get_sale_cobranza');
Route::post('/pdd/preventa/acceder/actualizar-ventas-cobranza', 'Backend\PreventaController@updateCobranza')->name('dashboard.preventa.update_cobranza');
Route::post('/pdd/preventa/acceder/listar-empleados', 'Backend\PreventaController@getEmployees')->name('dashboard.preventa.get_employees');
Route::post('/pdd/preventa/acceder/registrar-voucher', 'Backend\PreventaController@registerVoucher')->name('dashboard.preventa.register_voucher');
Route::post('/pdd/preventa/acceder/obtener-depositos', 'Backend\PreventaController@getDeposit')->name('dashboard.preventa.get_deposit');
Route::post('/pdd/preventa/acceder/listar-cobranzas-pagadas', 'Backend\PreventaController@listCobranzasPagadas')->name('dashboard.preventa.list_cobranzas_pagadas');

Route::get('/', 'Backend\UserController@index')->name('login');
Route::post('/iniciar-sesion/auth', 'Backend\UserController@login')->name('login.auth');
Route::get('/cerrar-sesion', 'Backend\UserController@logout')->name('logout');

Route::middleware(['auth'])->namespace('Backend')->group(function () {


	/*
	Route::get('/notificaciones/choferes', function () {


		// 👇 Retorna la vista con los datos
		return view('backend.mail.drivers'	);
	});
	*/


	/** PREVENTA CHOFER */
	Route::get('/pdd/liquidacion-preventa', 'LiquidationPreventaController@index')->name('dashboard.liquidation_preventa.index');
	Route::post('/pdd/liquidacion-preventa/listar-guias', 'LiquidationPreventaController@getWarehouseMovements')->name('dashboard.liquidation_preventa.get_warehouse_movements');
	Route::post('/pdd/liquidacion-preventa/validar-formulario', 'LiquidationPreventaController@validateForm')->name('dashboard.liquidation_preventa.validate_form');
	Route::post('/pdd/liquidacion-preventa/listar-ventas', 'LiquidationPreventaController@listSales')->name('dashboard.liquidation_preventa.list_sales');
	Route::post('/pdd/liquidacion-preventa/listar-ventas-detalle', 'LiquidationPreventaController@listSaleDetail')->name('dashboard.liquidation_preventa.list_sale_detail');
	Route::post('/pdd/liquidacion-preventa/listar-liquidacion-detalle', 'LiquidationPreventaController@listLiquidationDetail')->name('dashboard.liquidation_preventa.list_liquidation_detail');
	Route::post('/pdd/liquidacion-preventa/asignar-scop', 'LiquidationPreventaController@asignScop')->name('dashboard.liquidation_preventa.asign_scop');
	Route::post('/pdd/liquidacion-preventa/actualizar-scop', 'LiquidationPreventaController@updateScop')->name('dashboard.liquidation_preventa.update_scop');
	Route::post('/pdd/liquidacion-preventa/registrar-monedas', 'LiquidationPreventaController@registerMoney')->name('dashboard.liquidation_preventa.register_money');
	Route::post('/pdd/liquidacion-preventa/detalle-monedas', 'LiquidationPreventaController@detailMoney')->name('dashboard.liquidation_preventa.detail_money');
	Route::post('/pdd/liquidacion-preventa/finalizar', 'LiquidationPreventaController@finishLiquidation')->name('dashboard.liquidation_preventa.finish_liquidation');




	/** Operaciones > REGISTRO DE PEDIDOS - COORDINADOR DE ENVASADO */
	Route::get('/operaciones/registro-pedidos-cor-existencias', 'PlantOrderCorController@index')->name('dashboard.operations.plant_order_cor_register');
	Route::post('/operaciones/registro-pedidos-cor-existencias/validar-formulario', 'PlantOrderCorController@store')->name('dashboard.operations.plant_order_cor_register.store');
	Route::post('/operaciones/registro-pedidos-cor-existencias/listar-pedidos', 'PlantOrderCorController@list')->name('dashboard.operations.plant_order_cor_register.list');
	Route::post('/operaciones/registro-pedidos-cor-existencias/actualizar-pedido', 'PlantOrderCorController@update')->name('dashboard.operations.plant_order_cor_register.update');
	Route::post('/operaciones/registro-pedidos-cor-existencias/detalle', 'PlantOrderCorController@detail')->name('dashboard.operations.plant_order_cor_register.detail');
	Route::post('/operaciones/registro-pedidos-cor-existencias/obtener-chofer', 'PlantOrderCorController@getSelect2')->name('dashboard.operations.plant_order_cor_register.get_select2');
	Route::post('/operaciones/registro-pedidos-cor-existencias/listar-choferes', 'PlantOrderCorController@getEmployees')->name('dashboard.operations.plant_order_cor_register.get_employees');

	/** Operaciones > REGISTRO DE PEDIDOS */
	Route::get('/operaciones/registro-pedidos-existencias', 'PlantOrderController@index')->name('dashboard.operations.plant_order_register');
	Route::post('/operaciones/registro-pedidos-existencias/validar-formulario', 'PlantOrderController@store')->name('dashboard.operations.plant_order_register.store');
	Route::post('/operaciones/registro-pedidos/listar-pedidos', 'PlantOrderController@getPlanOrder')->name('dashboard.operations.plant_order_register.get_plan_order');
	Route::post('/operaciones/registro-pedidos/obtener-pedido', 'PlantOrderController@getPlan')->name('dashboard.operations.plant_order_register.get_plan');


	/** Operaciones > PARTICION DE PEDIDOS EN GUIAS */
	Route::get('/operaciones/registro-particion', 'GuidePartitionController@index')->name('dashboard.operations.partition_guide_register');
	Route::post('/operaciones/registro-particion/validar-formulario', 'GuidePartitionController@store')->name('dashboard.operations.partition_guide_register.store');
	Route::post('/operaciones/registro-particion/listar-pedidos', 'GuidePartitionController@getPlanOrder')->name('dashboard.operations.partition_guide_register.get_plan_order');
	Route::post('/operaciones/registro-particion/listar-plant', 'GuidePartitionController@getPlan')->name('dashboard.operations.partition_guide_register.get_plant');

	/** Operaciones > VIGILANCIA */
	Route::get('/operaciones/registro-movimientos-pedidos', 'VigilanciaController@index')->name('dashboard.operations.vigilancia_register');
	Route::post('/operaciones/registro-movimientos-pedidos/validar-formulario', 'VigilanciaController@store')->name('dashboard.operations.vigilancia_register.store');
	Route::post('/operaciones/registro-movimientos-pedidos/listar-pedidos', 'VigilanciaController@getPlanOrder')->name('dashboard.operations.vigilancia_register.get_plan_order');
	Route::post('/operaciones/registro-movimientos-pedidos/obtener-clase', 'VigilanciaController@getMovementClass')->name('dashboard.operations.vigilancia_register.get_movement_class');
	Route::post('/operaciones/registro-movimientos/listar-choferes', 'VigilanciaController@getEmployees')->name('dashboard.operations.vigilancia_register.get_employees');
	Route::post('/operaciones/registro-movimientos/listar-clientes', 'VigilanciaController@getClients')->name('dashboard.operations.vigilancia_register.get_clients');

	/** Operaciones > REGISTRO DE RECEPCION */
	Route::get('/operaciones/registro-recepcion', 'ReceptionController@index')->name('dashboard.operations.reception_register');
	Route::post('/operaciones/registro-recepcion/validar-formulario', 'ReceptionController@store')->name('dashboard.operations.reception_register.store');
	Route::post('/operaciones/registro-recepcion/listar-pedidos', 'ReceptionController@getPlanOrder')->name('dashboard.operations.reception_register.get_plan_order');
	Route::post('/operaciones/registro-recepcion/obtener-pedido', 'ReceptionController@getPlan')->name('dashboard.operations.reception_register.get_plant');

	/** Operaciones > CAMBIOS */
	Route::get('/operaciones/registro-cambios', 'GuideChangeController@index')->name('dashboard.operations.guide_change');
	Route::post('/operaciones/registro-cambios/validar-formulario', 'GuideChangeController@store')->name('dashboard.operations.guide_change.store');
	Route::post('/operaciones/registro-cambios/listar-pedidos', 'GuideChangeController@getReceptionPlant')->name('dashboard.operations.guide_change.get_reception_plants');
	Route::post('/operaciones/registro-cambios/listar-plant', 'GuideChangeController@getPlan')->name('dashboard.operations.guide_change.get_reception_plant');
	Route::post('/operaciones/registro-cambios/listar-ubigeos', 'GuideChangeController@getUbigeos')->name('dashboard.operations.guide_change.get_ubigeos');

	/** Facturación > Envío Guía de Transportista OSE PUNTO */
	Route::get('/facturacion/reporte-gr-transport-ose', 'GuideEfactTransportController@index')->name('dashboard.guide_transport.efact');
	Route::post('/facturacion/validar-formulario-gr-transport-ose', 'GuideEfactTransportController@validate_voucher_form')->name('dashboard.guide_transport.efact.validate_voucher_form');
	Route::post('/facturacion/listar-gr-transport-ose', 'GuideEfactTransportController@list')->name('dashboard.guide_transport.efact.list');
	Route::post('/facturacion/obtener-detalle-gr-transport-ose', 'GuideEfactTransportController@get_detail')->name('dashboard.guide_transport.efact.get_detail');
	Route::post('/facturacion/enviar-gr-transport-ose', 'GuideEfactTransportController@send_voucher')->name('dashboard.guide_transport.efact.send_voucher');


	/** Control GLP > Constancia de Verificación de Pesos y Medidas */
	Route::get('/operaciones/constancia-peso-medida', 'ConstancyGuideController@index')->name('dashboard.constancy_guide.report');
	Route::post('/operaciones/constancia-peso-medida-validar-form', 'ConstancyGuideController@validateForm')->name('dashboard.constancy_guide.validar_form');
	Route::post('/operaciones/constancia-peso-medida-listar', 'ConstancyGuideController@list')->name('dashboard.constancy_guide.list');
	Route::post('/operaciones/constancia-peso-medida-descargar-pdf', 'ConstancyGuideController@generarPdf')->name('dashboard.constancy_guide.descargar');
	Route::post('/operaciones/constancia-peso-medida-detalle', 'ConstancyGuideController@detail')->name('dashboard.constancy_guide.detalle');








	/** Inicio */
	Route::get('/pdd/welcome', 'PresentController@index')->name('dashboard.present.index');

	/** importación de facturas*/
	Route::get('/facturacion/importar', 'FacturationController@showImport')->name('facturations.import');
	Route::post('/facturacion/importar', 'FacturationController@postImport')->name('facturations.import');


	//VERIFICAR TOKEN
	Route::get('/token-pdd', function (EfactClient $billingClientPunto) {
		echo "PDD - Tiempo restante: " . $billingClientPunto->getTokenRemainingTime() . " segundos";
	});

	Route::get('/token-pdd-generar', function (EfactClient $billingClientPunto) {
		echo "PDD - Token: " . $billingClientPunto->getAccessToken() . "<br>";
	});

	Route::get('/token-cordia', function (EfactCordClient $billingClientCordia) {
		echo "CORDIA - Tiempo restante: " . $billingClientCordia->getTokenRemainingTime() . " segundos";
	});

	Route::get('/token-cordia-generar', function (EfactCordClient $billingClientCordia) {
		echo "CORDIA - Token: " . $billingClientCordia->getAccessToken() . "<br>";
	});


	/** BLUELINE - Flota  > Gestión de Choferes */
	Route::get('/blueline-choferes', 'BlueDriverController@index')->name('dashboard.blue_driver');
	Route::post('/blueline-choferes/crear', 'BlueDriverController@store')->name('dashboard.blue_driver.store');
	Route::post('/blueline-choferes/listar', 'BlueDriverController@list')->name('dashboard.blue_driver.list');
	Route::post('/blueline-choferes/eliminar', 'BlueDriverController@delete')->name('dashboard.blue_driver.delete');
	Route::post('/blueline-choferes/detalle', 'BlueDriverController@detail')->name('dashboard.blue_driver.detail');
	Route::post('/blueline-choferes/actualizar', 'BlueDriverController@update')->name('dashboard.blue_driver.update');
	Route::post('/blueline-choferes/exportar', 'BlueDriverController@exportExcel')->name('dashboard.blue_driver.export');



	/** BLUELINE - Flota  > Gestión de Flota */
	Route::get('/blueline-vehiculos-placa', 'BluePlateController@index')->name('dashboard.blue_plate');
	Route::post('/blueline-vehiculos-placa/crear', 'BluePlateController@store')->name('dashboard.blue_plate.store');
	Route::post('/blueline-vehiculos-placa/listar', 'BluePlateController@list')->name('dashboard.blue_plate.list');
	Route::post('/blueline-vehiculos-placa/eliminar', 'BluePlateController@delete')->name('dashboard.blue_plate.delete');
	Route::post('/blueline-vehiculos-placa/detalle', 'BluePlateController@detail')->name('dashboard.blue_plate.detail');
	Route::post('/blueline-vehiculos-placa/actualizar', 'BluePlateController@update')->name('dashboard.blue_plate.update');
	Route::post('/blueline-vehiculos-placa/exportar', 'BluePlateController@exportExcel')->name('dashboard.blue_plate.export');

	/** BLUELINE - Clientes > Gestión de Clientes */
	Route::get('/blueline-comercial/clientes', 'BlueClientController@index')->name('dashboard.voucher.blue_clients');
	Route::post('/blueline-comercial/clientes/validar-formulario', 'BlueClientController@validateForm')->name('dashboard.voucher.blue_clients.validate_form');
	Route::post('/blueline-comercial/clientes/listar', 'BlueClientController@list')->name('dashboard.voucher.blue_clients.list');
	Route::post('/blueline-comercial/clientes/guardar', 'BlueClientController@store')->name('dashboard.voucher.blue_clients.store');
	Route::post('/blueline-comercial/clientes/detalle', 'BlueClientController@detail')->name('dashboard.voucher.blue_clients.detail');
	Route::post('/blueline-comercial/clientes/obtener-ubigeos', 'BlueClientController@getUbigeos')->name('dashboard.voucher.blue_clients.get_ubigeos');
	Route::post('/blueline-comercial/clientes/obtener-clientes', 'BlueClientController@getClients')->name('dashboard.voucher.blue_clients.get_clients');
	Route::post('/blueline-comercial/clientes/obtener-select2', 'BlueClientController@getSelect2')->name('dashboard.voucher.blue_clients.get_select2');
	Route::post('/blueline-comercial/clientes/eliminar', 'BlueClientController@delete')->name('dashboard.voucher.blue_clients.delete');
	Route::post('/blueline-comercial/clientes/listar-direcciones', 'BlueClientController@addressList')->name('dashboard.voucher.blue_clients.address_list');
	Route::post('/blueline-comercial/clientes/guardar-direccion', 'BlueClientController@addressStore')->name('dashboard.voucher.blue_clients.address_store');
	Route::post('/blueline-comercial/clientes/detalle-direccion', 'BlueClientController@addressDetail')->name('dashboard.voucher.blue_clients.address_detail');
	Route::post('/blueline-comercial/clientes/eliminar-direccion', 'BlueClientController@addressDelete')->name('dashboard.voucher.blue_clients.address_delete');
	Route::post('/blueline-comercial/clientes/listar-precios', 'BlueClientController@priceList')->name('dashboard.voucher.blue_clients.price_list');
	Route::post('/blueline-comercial/clientes/listar-articulos', 'BlueClientController@priceArticles')->name('dashboard.voucher.blue_clients.price_articles');
	Route::post('/blueline-comercial/clientes/obtener-dia', 'BlueClientController@priceMinEffectiveDate')->name('dashboard.voucher.blue_clients.price_min_effective_date');
	Route::post('/blueline-comercial/clientes/guardar-precio', 'BlueClientController@priceStore')->name('dashboard.voucher.blue_clients.price_store');

	/** BLUELINE - Clientes > Reporte de Clientes */
	Route::get('/reporte-blue/relacion-de-clientes', 'BlueClientsReportController@index')->name('dashboard.report.blue_list_clients');
	Route::post('/reporte-blue/relacion-de-clientes/validar-formulario', 'BlueClientsReportController@validateForm')->name('dashboard.report.blue_list_clients.validate_form');
	Route::post('/reporte-blue/relacion-de-clientes/listar', 'BlueClientsReportController@list')->name('dashboard.report.blue_list_clients.list');

	/** BLUELINE - Operaciones > Registro de Guía de Remisión Electrónica */
	Route::get('/operaciones-blue/registro-movimiento-existencias', 'BlueGuidesRegisterController@index')->name('dashboard.operations.blue_guides_register');
	Route::post('/operaciones-blue/registro-movimiento-existencias/validar-formulario', 'BlueGuidesRegisterController@validateForm')->name('dashboard.operations.blue_guides_register.validate_form');
	Route::post('/operaciones-blue/registro-movimiento-existencias/listar', 'BlueGuidesRegisterController@list')->name('dashboard.operations.blue_guides_register.list');
	Route::post('/operaciones-blue/registro-movimiento-existencias/listar-cuentas', 'BlueGuidesRegisterController@getAccounts')->name('dashboard.operations.blue_guides_register.get_accounts');
	Route::post('/operaciones-blue/registro-movimiento-existencias/listar-articulos', 'BlueGuidesRegisterController@getArticles')->name('dashboard.operations.blue_guides_register.get_articles');
	Route::post('/operaciones-blue/registro-movimiento-existencias/obtener-percepcion', 'BlueGuidesRegisterController@getPerceptionPercentage')->name('dashboard.operations.blue_guides_register.get_perception_percentage');
	Route::post('/operaciones-blue/registro-movimiento-existencias/obtener-tasas', 'BlueGuidesRegisterController@getArticleRates')->name('dashboard.operations.blue_guides_register.get_article_rates');
	Route::post('/operaciones-blue/registro-movimiento-existencias/obtener-articulo', 'BlueGuidesRegisterController@getArticle')->name('dashboard.operations.blue_guides_register.get_article');
	Route::post('/operaciones-blue/registro-movimiento-existencias/guardar', 'BlueGuidesRegisterController@store')->name('dashboard.operations.blue_guides_register.store');
	Route::get('/operaciones-blue/registro-movimiento-existencias/obtener-siguiente-correlativo', 'BlueGuidesRegisterController@getNextcorrelative')->name('dashboard.operations.blue_guides_register.next_correlative');
	Route::post('/operaciones-blue/registro-movimiento-existencias/listar-ubigeos', 'BlueGuidesRegisterController@getUbigeos')->name('dashboard.operations.blue_guides_register.get_ubigeos');
	Route::post('/operaciones-blue/registro-movimiento-existencias/listar-empleados', 'BlueGuidesRegisterController@getEmployees')->name('dashboard.operations.blue_guides_register.get_employees');
	Route::post('/operaciones-blue/registro-movimiento-existencias/listar-series', 'BlueGuidesRegisterController@getElectSeries')->name('dashboard.operations.blue_guides_register.get_series');

	/** BLUELINE - ADMINISTRACION > Generación de Documentos */
	Route::get('/facturacion-blue/documentos-por-cobrar', 'BlueRegisterDocumentChargeController@index')->name('dashboard.voucher.blue_register_document_charge');
	Route::post('/facturacion-blue/documentos-por-cobrar/validar-primer-paso', 'BlueRegisterDocumentChargeController@validateFirstStep')->name('dashboard.voucher.blue_register_document_charge.validate_first_step');
	Route::post('/facturacion-blue/documentos-por-cobrar/obtener-comprobante', 'BlueRegisterDocumentChargeController@getVoucher')->name('dashboard.voucher.blue_register_document_charge.get_voucher');
	Route::post('/facturacion-blue/documentos-por-cobrar/validar-segundo-paso', 'BlueRegisterDocumentChargeController@validateSecondStep')->name('dashboard.voucher.blue_register_document_charge.validate_second_step');
	Route::post('/facturacion-blue/documentos-por-cobrar/obtener-clientes', 'BlueRegisterDocumentChargeController@getClients')->name('dashboard.voucher.blue_register_document_charge.get_clients');
	Route::post('/facturacion-blue/documentos-por-cobrar/validar-tercer-paso', 'BlueRegisterDocumentChargeController@validateThirdStep')->name('dashboard.voucher.blue_register_document_charge.validate_third_step');
	Route::post('/facturacion-blue/documentos-por-cobrar/guardar', 'BlueRegisterDocumentChargeController@store')->name('dashboard.voucher.blue_register_document_charge.store');

	/** BLUELINE - ADMINISTRACION > Relación de Documentos Emitidos */
	Route::get('/reporte-blue/relacion-de-documentos-por-cobrar', 'BlueUncollectedDocumentReportController@index')->name('dashboard.report.blue_uncollected_document_report');
	Route::post('/reporte-blue/relacion-de-documentos-por-cobrar/obtener-clientes', 'BlueUncollectedDocumentReportController@getClients')->name('dashboard.report.blue_uncollected_document_report.get_clients');
	Route::post('/reporte-blue/relacion-de-documentos-por-cobrar/validar-formulario', 'BlueUncollectedDocumentReportController@validateForm')->name('dashboard.report.blue_uncollected_document_report.validate_form');
	Route::post('/reporte-blue/relacion-de-documentos-por-cobrar/listar', 'BlueUncollectedDocumentReportController@list')->name('dashboard.report.blue_uncollected_document_report.list');

	/** BLUELINE - Flota  > Gestión de Flota */
	Route::get('/blueline-mantenimientos-placa', 'BlueMaintenanceController@index')->name('dashboard.gestion.blue_maintenance');
	Route::post('/blueline-mantenimientos-placa/crear', 'BlueMaintenanceController@store')->name('dashboard.gestion.blue_maintenance.store');
	Route::post('/blueline-mantenimientos-placa/listar', 'BlueMaintenanceController@list')->name('dashboard.gestion.blue_maintenance.list');
	Route::post('/blueline-mantenimientos-placa/eliminar', 'BlueMaintenanceController@delete')->name('dashboard.gestion.blue_maintenance.delete');
	Route::post('/blueline-mantenimientos-placa/detalle', 'BlueMaintenanceController@detail')->name('dashboard.gestion.blue_maintenance.detail');
	Route::post('/blueline-mantenimientos-placa/validar', 'BlueMaintenanceController@validate_form')->name('dashboard.gestion.blue_maintenance.validate_form');


	/** Casa del Gas > Registro de Pedidos*/
	Route::get('/casa/pedidos', 'OrderHouseController@index')->name('dashboard.order_house.index');
	Route::post('/casa/pedidos/listar', 'OrderHouseController@list')->name('dashboard.order_house.list');
	Route::post('/casa/pedidos/listar-clientes', 'OrderHouseController@getClients')->name('dashboard.order_house.list_clients');
	Route::post('/casa/pedidos/listar-empleados', 'OrderHouseController@getEmployees')->name('dashboard.order_house.list_employees');
	Route::post('/casa/pedidos/crear', 'OrderHouseController@store')->name('dashboard.order_house.store');
	Route::post('/casa/pedidos/actualizar', 'OrderHouseController@update')->name('dashboard.order_house.update');
	Route::post('/casa/pedidos/detalle', 'OrderHouseController@detail')->name('dashboard.order_house.detail');
	Route::post('/casa/pedidos/eliminar', 'OrderHouseController@delete')->name('dashboard.order_house.delete');
	Route::post('/casa/pedidos/obtener-ubigeos', 'OrderHouseController@getUbigeos')->name('dashboard.order_house.get_ubigeos');
	Route::post('/casa/pedidos/registrar-cliente', 'OrderHouseController@registerClient')->name('dashboard.order_house.register_client');
	Route::post('/casa/pedidos/obtener-voucher', 'OrderHouseController@getVoucher')->name('dashboard.order_house.get_voucher');

	/** Casa del Gas > Reporte de Pedidos*/
	Route::get('/reporte/casa/pedidos', 'OrderHouseReportController@index')->name('dashboard.report.order_house');
	Route::post('/reporte/casa/pedidos/validar-formulario', 'OrderHouseReportController@validateForm')->name('dashboard.report.order_house.validate_form');
	Route::post('/reporte/casa/pedidos/listar', 'OrderHouseReportController@list')->name('dashboard.report.order_house.list');


	/** Casa del Gas > Venta Canal*/
	Route::get('/reporte-casa-gas/venta-canal', 'LiquidationChannelGasReportController@index')->name('dashboard.report_casa_gas.liquidations_channel');
	Route::post('/reporte-casa-gas/venta-canal/validar-formulario', 'LiquidationChannelGasReportController@validateForm')->name('dashboard.report_casa_gas.liquidations_channel.validate_form');
	Route::post('/reporte-casa-gas/venta-canal/obtener-clientes', 'LiquidationChannelGasReportController@getClients')->name('dashboard.report_casa_gas.liquidations_channel.get_clients');
	Route::post('/reporte-casa-gas/venta-canal/listar', 'LiquidationChannelGasReportController@list')->name('dashboard.report_casa_gas.liquidations_channel.list');

	/** Casa del Gas > Venta Canal Resumido*/
	Route::get('/reporte-casa-gas/venta-canal-resumido', 'LiquidationChannelTotalCasaGasReportController@index')->name('dashboard.report_casa_gas.liquidations_channel_total');
	Route::post('/reporte-casa-gas/venta-canal-resumido/validar-formulario', 'LiquidationChannelTotalCasaGasReportController@validateForm')->name('dashboard.report_casa_gas.liquidations_channel_total.validate_form');
	Route::post('/reporte-casa-gas/venta-canal-resumido/obtener-clientes', 'LiquidationChannelTotalCasaGasReportController@getClients')->name('dashboard.report_casa_gas.liquidations_channel_total.get_clients');
	Route::post('/reporte-casa-gas/venta-canal-resumido/listar', 'LiquidationChannelTotalCasaGasReportController@list')->name('dashboard.report_casa_gas.liquidations_channel_total.list');

	/** Casa del Gas > Clientes */
	Route::get('/casa-gas/clientes', 'ClientCasaGasController@index')->name('dashboard.voucher.clients_casa_gas');
	Route::post('/casa-gas/clientes/validar-formulario', 'ClientCasaGasController@validateForm')->name('dashboard.voucher.clients_casa_gas.validate_form');
	Route::post('/casa-gas/clientes/listar', 'ClientCasaGasController@list')->name('dashboard.voucher.clients_casa_gas.list');
	Route::post('/casa-gas/clientes/guardar', 'ClientCasaGasController@store')->name('dashboard.voucher.clients_casa_gas.store');
	Route::post('/casa-gas/clientes/detalle', 'ClientCasaGasController@detail')->name('dashboard.voucher.clients_casa_gas.detail');
	Route::post('/casa-gas/clientes/obtener-ubigeos', 'ClientCasaGasController@getUbigeos')->name('dashboard.voucher.clients_casa_gas.get_ubigeos');
	Route::post('/casa-gas/clientes/obtener-clientes', 'ClientCasaGasController@getClients')->name('dashboard.voucher.clients_casa_gas.get_clients');
	Route::post('/casa-gas/clientes/obtener-select2', 'ClientCasaGasController@getSelect2')->name('dashboard.voucher.clients_casa_gas.get_select2');
	Route::post('/casa-gas/clientes/eliminar', 'ClientCasaGasController@delete')->name('dashboard.voucher.clients_casa_gas.delete');
	Route::post('/casa-gas/clientes/listar-direcciones', 'ClientCasaGasController@addressList')->name('dashboard.voucher.clients_casa_gas.address_list');
	Route::post('/casa-gas/clientes/guardar-direccion', 'ClientCasaGasController@addressStore')->name('dashboard.voucher.clients_casa_gas.address_store');
	Route::post('/casa-gas/clientes/detalle-direccion', 'ClientCasaGasController@addressDetail')->name('dashboard.voucher.clients_casa_gas.address_detail');
	Route::post('/casa-gas/clientes/eliminar-direccion', 'ClientCasaGasController@addressDelete')->name('dashboard.voucher.clients_casa_gas.address_delete');
	Route::post('/casa-gas/clientes/listar-precios', 'ClientCasaGasController@priceList')->name('dashboard.voucher.clients_casa_gas.price_list');
	Route::post('/casa-gas/clientes/listar-articulos', 'ClientCasaGasController@priceArticles')->name('dashboard.voucher.clients_casa_gas.price_articles');
	Route::post('/casa-gas/clientes/obtener-dia', 'ClientCasaGasController@priceMinEffectiveDate')->name('dashboard.voucher.clients_casa_gas.price_min_effective_date');
	Route::post('/casa-gas/clientes/guardar-precio', 'ClientCasaGasController@priceStore')->name('dashboard.voucher.clients_casa_gas.price_store');

	/** Casa del Gas > Clientes Perdidos Semestral Locales*/
	Route::get('/casa/reporte/vdc-semestral-perdidos', 'CasaLocalReport@index')->name('dashboard.report.casa_local');
	Route::post('/casa/reporte/vdc-semestral-perdidos/validar-formulario', 'CasaLocalReport@validateForm')->name('dashboard.report.casa_local.validate_form');
	Route::post('/casa/reporte/vdc-semestral-perdidos/obtener-clientes', 'CasaLocalReport@getClients')->name('dashboard.report.casa_local.get_clients');
	Route::post('/casa/reporte/vdc-semestral-perdidos/listar', 'CasaLocalReport@list')->name('dashboard.report.casa_local.list');





	/** Facturación > Envío OSE */
	Route::get('/facturacion/principal', 'VoucherOseController@sendOse')->name('dashboard.voucher.send_ose');
	Route::post('/facturacion/validar-formulario-documentos', 'VoucherOseController@validate_voucher_form')->name('dashboard.voucher.send_ose.validate_voucher_form');
	Route::post('/facturacion/obtener-documentos-tabla', 'VoucherOseController@get_vouchers_for_table')->name('dashboard.voucher.send_ose.get_vouchers_for_table');
	Route::post('/facturacion/listar', 'VoucherOseController@list')->name('dashboard.voucher.send_ose.list');
	Route::post('/facturacion/obtener-detalle-documento', 'VoucherOseController@get_voucher_detail')->name('dashboard.voucher.send_ose.get_voucher_detail');
	Route::post('/facturacion/enviar-documento', 'VoucherOseController@send_voucher')->name('dashboard.voucher.send_ose.send_voucher');

	/** Facturación > EFACT API OSE */
	Route::get('/Efact/api', 'EnvioEfactController@sendOse')->name('dashboard.api.send_efact');
	Route::post('/Efact/validar-formulario-documentos', 'EnvioEfactController@validate_voucher_form')->name('dashboard.api.send_efact.validate_voucher_form');
	Route::post('/Efact/obtener-documentos-tabla', 'EnvioEfactController@get_vouchers_for_table')->name('dashboard.api.send_efact.get_vouchers_for_table');
	Route::post('/Efact/listar', 'EnvioEfactController@list')->name('dashboard.send_efact.list');
	Route::post('/Efact/obtener-detalle-documento', 'EnvioEfactController@get_voucher_detail')->name('dashboard.api.send_efact.get_voucher_detail');
	Route::post('/Efact/enviar-documento', 'EnvioEfactController@send_voucher')->name('dashboard.api.send_efact.send_voucher');

	/** Facturación > Envío de Boletas como Resúmenes Punto*/
	Route::get('/facturacion/boletas', 'ResumenBoletaController@sendOse')->name('dashboard.boleta.send_ose');
	Route::post('/facturacion/boletas/validar-formulario-documentos', 'ResumenBoletaController@validate_voucher_form')->name('dashboard.boleta.validate_voucher_form');
	Route::post('/facturacion/boletas/obtener-documentos-tabla', 'ResumenBoletaController@get_vouchers_for_table')->name('dashboard.boleta.get_vouchers_for_table');
	Route::post('/facturacion/boletas/listar', 'ResumenBoletaController@list')->name('dashboard.boleta.list');
	Route::post('/facturacion/boletas/obtener-detalle-documento', 'ResumenBoletaController@get_voucher_detail')->name('dashboard.boleta.get_voucher_detail');
	Route::post('/facturacion/boletas/enviar-documento', 'ResumenBoletaController@send_voucher')->name('dashboard.boleta.send_voucher');

	/** Facturación > Envío Guía de Remisión OSE PUNTO */
	Route::get('/facturacion/reporte-gr-ose', 'GuideEfactController@index')->name('dashboard.guide.efact');
	Route::post('/facturacion/validar-formulario-gr-ose', 'GuideEfactController@validate_voucher_form')->name('dashboard.guide.efact.validate_voucher_form');
	Route::post('/facturacion/listar-gr-ose', 'GuideEfactController@list')->name('dashboard.guide.efact.list');
	Route::post('/facturacion/obtener-detalle-gr-ose', 'GuideEfactController@get_detail')->name('dashboard.guide.efact.get_detail');
	Route::post('/facturacion/enviar-gr-ose', 'GuideEfactController@send_voucher')->name('dashboard.guide.efact.send_voucher');

	/** Facturación > Consulta Envío OSE */
	Route::get('/facturacion/reporte-ose', 'VoucherReportOseController@index')->name('dashboard.voucher.reportOse');
	Route::post('/facturacion/reporte-ose/validar-formulario-documentos', 'VoucherReportOseController@validateForm')->name('dashboard.voucher.reportOse.validateForm');
	Route::post('/facturacion/reporte-ose/obtener-documentos-tabla', 'VoucherReportOseController@getVouchersTable')->name('dashboard.voucher.reportOse.getVouchersTable');
	Route::post('/facturacion/reporte-ose/obtener-detalle-documento', 'VoucherReportOseController@getVoucherDetail')->name('dashboard.voucher.reportOse.getVoucherDetail');
	Route::post('/facturacion/reporte-ose/enviar-documento', 'VoucherReportOseController@sendVoucher')->name('dashboard.voucher.reportOse.sendVoucher');

	/** Facturación > Registro de Documentos por Cobrar */
	Route::get('/facturacion/documentos-por-cobrar', 'RegisterDocumentChargeController@index')->name('dashboard.voucher.register_document_charge');
	Route::post('/facturacion/documentos-por-cobrar/validar-primer-paso', 'RegisterDocumentChargeController@validateFirstStep')->name('dashboard.voucher.register_document_charge.validate_first_step');
	Route::post('/facturacion/documentos-por-cobrar/obtener-comprobante', 'RegisterDocumentChargeController@getVoucher')->name('dashboard.voucher.register_document_charge.get_voucher');
	Route::post('/facturacion/documentos-por-cobrar/validar-segundo-paso', 'RegisterDocumentChargeController@validateSecondStep')->name('dashboard.voucher.register_document_charge.validate_second_step');
	Route::post('/facturacion/documentos-por-cobrar/obtener-clientes', 'RegisterDocumentChargeController@getClients')->name('dashboard.voucher.register_document_charge.get_clients');
	Route::post('/facturacion/documentos-por-cobrar/validar-tercer-paso', 'RegisterDocumentChargeController@validateThirdStep')->name('dashboard.voucher.register_document_charge.validate_third_step');
	Route::post('/facturacion/documentos-por-cobrar/guardar', 'RegisterDocumentChargeController@store')->name('dashboard.voucher.register_document_charge.store');


	/** Facturación > Registro de Egresos */
	Route::get('/facturacion/egresos', 'RegisterChargeController@index')->name('dashboard.voucher.register_charge');
	Route::post('/facturacion/egresos/validar-primer-paso', 'RegisterChargeController@validateFirstStep')->name('dashboard.voucher.register_charge.validate_first_step');
	Route::post('/facturacion/egresos/obtener-comprobante', 'RegisterChargeController@getVoucher')->name('dashboard.voucher.register_charge.get_voucher');
	Route::post('/facturacion/egresos/validar-segundo-paso', 'RegisterChargeController@validateSecondStep')->name('dashboard.voucher.register_charge.validate_second_step');
	Route::post('/facturacion/egresos/obtener-tipo', 'RegisterChargeController@getAccounts')->name('dashboard.voucher.register_charge.get_accounts');
	Route::post('/facturacion/egresos/validar-tercer-paso', 'RegisterChargeController@validateThirdStep')->name('dashboard.voucher.register_charge.validate_third_step');
	Route::post('/facturacion/egresos/guardar', 'RegisterChargeController@store')->name('dashboard.voucher.register_charge.store');


	/** Facturación > Registro de Vouchers */
	Route::get('/facturacion/vouchers/registrar-voucher', 'RegisterVoucherController@showRegisterVoucher')->name('dashboard.voucher.register_voucher');
	Route::post('/facturacion/vouchers/registrar-voucher/guardar', 'RegisterVoucherController@registerVoucher')->name('dashboard.voucher.register_voucher.store');
	Route::post('/facturacion/vouchers/obtener-cuentas-banco', 'RegisterVoucherController@getBankAccounts')->name('dashboard.voucher.register_voucher.get_bank_accounts');
	Route::post('/facturacion/vouchers/listar', 'RegisterVoucherController@list')->name('dashboard.voucher.register_voucher.list');
	Route::post('/facturacion/vouchers/eliminar', 'RegisterVoucherController@delete')->name('dashboard.voucher.register_voucher.delete');
	Route::post('/facturacion/vouchers/detalle', 'RegisterVoucherController@detail')->name('dashboard.voucher.register_voucher.detail');
	Route::post('/facturacion/vouchers/actualizar', 'RegisterVoucherController@update')->name('dashboard.voucher.register_voucher.update');


		/** Facturación > Registro de Efectivo */
        Route::get('/facturacion/efectivo/registrar-efectivo', 'RegisterEfectiveController@showRegisterEfective')->name('dashboard.efective.register_efective');
        Route::post('/facturacion/efectivo/registrar-efectivo', 'RegisterEfectiveController@registerEfective')->name('dashboard.efective.register_efective');
        Route::post('/facturacion/efectivo/obtener-movimientos-almacen', 'RegisterEfectiveController@getWarehouseMovements')->name('dashboard.efective.register_efective.get_warehouse_movements');
        Route::post('/facturacion/efectivo/listar-datos', 'RegisterEfectiveController@list')->name('dashboard.data_efective');
        Route::post('/facturacion/efectivo/eliminar', 'RegisterEfectiveController@delete')->name('dashboard.efective.register_efective.delete');
        Route::post('/facturacion/efectivo/detalle', 'RegisterEfectiveController@detail')->name('dashboard.efective.register_efective.detail');
        Route::post('/facturacion/efectivo/actualizar', 'RegisterEfectiveController@update')->name('dashboard.efective.register_efective.update');

	/** Facturación > Relación de Egresos */
	Route::get('/facturacion/relacion-de-egresos', 'UnDocumentReportController@index')->name('dashboard.voucher.un_document_report');
	Route::post('/facturacion/relacion-de-egresos/obtener-clientes', 'UnDocumentReportController@getClients')->name('dashboard.voucher.un_document_report.get_clients');
	Route::post('/facturacion/relacion-de-egresos/validar-formulario', 'UnDocumentReportController@validateForm')->name('dashboard.voucher.un_document_report.validate_form');
	Route::post('/facturacion/relacion-de-egresos/listar', 'UnDocumentReportController@list')->name('dashboard.voucher.un_document_report.list');

	/** Facturación > Registro de Facturacion a Global */
	Route::get('/facturacion/documentos-global', 'RegisterDocumentFactController@index')->name('dashboard.voucher.register_document_fact');
	Route::post('/facturacion/documentos-global/validar-primer-paso', 'RegisterDocumentFactController@validateFirstStep')->name('dashboard.voucher.register_document_fact.validate_first_step');
	Route::post('/facturacion/documentos-global/obtener-comprobante', 'RegisterDocumentFactController@getVoucher')->name('dashboard.voucher.register_document_fact.get_voucher');
	Route::post('/facturacion/documentos-global/validar-segundo-paso', 'RegisterDocumentFactController@validateSecondStep')->name('dashboard.voucher.register_document_fact.validate_second_step');
	Route::post('/facturacion/documentos-global/obtener-clientes', 'RegisterDocumentFactController@getClients')->name('dashboard.voucher.register_document_fact.get_clients');
	Route::post('/facturacion/documentos-global/validar-tercer-paso', 'RegisterDocumentFactController@validateThirdStep')->name('dashboard.voucher.register_document_fact.validate_third_step');
	Route::post('/facturacion/documentos-global/guardar', 'RegisterDocumentFactController@store')->name('dashboard.voucher.register_document_fact.store');

	/** Facturación > Registro de Cobranzas */
	Route::get('/facturacion/cobranzas', 'CollectionRegisterController@index')->name('dashboard.voucher.collection_register');
	Route::post('/facturacion/cobranzas/obtener-clientes', 'CollectionRegisterController@getClients')->name('dashboard.voucher.collection_register.get_clients');
	Route::post('/facturacion/cobranzas/validar-primer-paso', 'CollectionRegisterController@validateFirstStep')->name('dashboard.voucher.collection_register.validate_first_step');
	Route::post('/facturacion/cobranzas/validar-segundo-paso', 'CollectionRegisterController@validateSecondStep')->name('dashboard.voucher.collection_register.validate_second_step');
	Route::post('/facturacion/cobranzas/obtener-ventas', 'CollectionRegisterController@getSales')->name('dashboard.voucher.collection_register.get_sales');
	Route::post('/facturacion/cobranzas/guardar', 'CollectionRegisterController@store')->name('dashboard.voucher.collection_register.store');

	/** Facturación > Registro de Descuentos a Personal */
	Route::get('/facturacion/descuentos-cancelacion', 'DescuentoRegisterController@index')->name('dashboard.voucher.descuento_register');
	Route::post('/facturacion/descuentos-cancelacion/validar-primer-paso', 'DescuentoRegisterController@validateFirstStep')->name('dashboard.voucher.descuento_register.validate_first_step');
	Route::post('/facturacion/descuentos-cancelacion/obtener-comprobante', 'DescuentoRegisterController@getSales')->name('dashboard.voucher.descuento_register.get_sales');
	Route::post('/facturacion/descuentos-cancelacion/validar-segundo-paso', 'DescuentoRegisterController@validateSecondStep')->name('dashboard.voucher.descuento_register.validate_second_step');
	Route::post('/facturacion/descuentos-cancelacion/obtener-clientes', 'DescuentoRegisterController@getClients')->name('dashboard.voucher.descuento_register.get_clients');
	Route::post('/facturacion/descuentos-cancelacion/guardar', 'DescuentoRegisterController@store')->name('dashboard.voucher.descuento_register.store');

	/** Facturación > Liquidaciones */
	Route::get('/facturacion/liquidaciones', 'LiquidationController@index')->name('dashboard.voucher.liquidations');
	Route::post('/facturacion/liquidaciones/validar-formulario', 'LiquidationController@validateForm')->name('dashboard.voucher.liquidations.validate_form');
	Route::post('/facturacion/liquidaciones/obtener-movimientos-almacen', 'LiquidationController@getWarehouseMovements')->name('dashboard.voucher.liquidations.get_warehouse_movements');
	Route::post('/facturacion/liquidaciones/listar', 'LiquidationController@list')->name('dashboard.voucher.liquidations.list');
	Route::post('/facturacion/liquidaciones/obtener-clientes', 'LiquidationController@getClients')->name('dashboard.voucher.liquidations.get_clients');
	Route::post('/facturacion/liquidaciones/obtener-precio-articulo', 'LiquidationController@getArticlePrice')->name('dashboard.voucher.liquidations.get_article_price');
	Route::post('/facturacion/liquidaciones/obtener-cuentas-banco', 'LiquidationController@getBankAccounts')->name('dashboard.voucher.liquidations.get_bank_accounts');
	Route::post('/facturacion/liquidaciones/verificar-documento', 'LiquidationController@verifyDocumentType')->name('dashboard.voucher.liquidations.verify_document_type');
	Route::post('/facturacion/liquidaciones/guardar', 'LiquidationController@store')->name('dashboard.voucher.liquidations.store');

	/** Facturación > Otras Liquidaciones */
	Route::get('/facturacion/liquidaciones-otras', 'LiquidationOtherController@index')->name('dashboard.voucher.liquidations_other');
	Route::post('/facturacion/liquidaciones-otras/validar-formulario', 'LiquidationOtherController@validateForm')->name('dashboard.voucher.liquidations_other.validate_form');
	Route::post('/facturacion/liquidaciones-otras/obtener-movimientos-almacen', 'LiquidationOtherController@getWarehouseMovements')->name('dashboard.voucher.liquidations_other.get_warehouse_movements');
	Route::post('/facturacion/liquidaciones-otras/listar', 'LiquidationOtherController@list')->name('dashboard.voucher.liquidations_other.list');
	Route::post('/facturacion/liquidaciones-otras/obtener-clientes', 'LiquidationOtherController@getClients')->name('dashboard.voucher.liquidations_other.get_clients');
	Route::post('/facturacion/liquidaciones-otras/obtener-precio-articulo', 'LiquidationOtherController@getArticlePrice')->name('dashboard.voucher.liquidations_other.get_article_price');
	Route::post('/facturacion/liquidaciones-otras/obtener-cuentas-banco', 'LiquidationOtherController@getBankAccounts')->name('dashboard.voucher.liquidations_other.get_bank_accounts');
	Route::post('/facturacion/liquidaciones-otras/verificar-documento', 'LiquidationOtherController@verifyDocumentType')->name('dashboard.voucher.liquidations_other.verify_document_type');
	Route::post('/facturacion/liquidaciones-otras/guardar', 'LiquidationOtherController@store')->name('dashboard.voucher.liquidations_other.store');

	/** Facturación > Liquidaciones Final */
	Route::get('/facturacion/liquidaciones-envasado', 'LiquidationEnvasadoController@index')->name('dashboard.voucher.liquidations_envasado');
	Route::post('/facturacion/liquidaciones-envasado/validar-formulario', 'LiquidationEnvasadoController@validateForm')->name('dashboard.voucher.liquidations_envasado.validate_form');
	Route::post('/facturacion/liquidaciones-envasado/obtener-movimientos-almacen', 'LiquidationEnvasadoController@getWarehouseMovements')->name('dashboard.voucher.liquidations_envasado.get_warehouse_movements');
	Route::post('/facturacion/liquidaciones-envasado/listar', 'LiquidationEnvasadoController@list')->name('dashboard.voucher.liquidations_envasado.list');
	Route::post('/facturacion/liquidaciones-envasado/obtener-clientes', 'LiquidationEnvasadoController@getClients')->name('dashboard.voucher.liquidations_envasado.get_clients');
	Route::post('/facturacion/liquidaciones-envasado/obtener-precio-articulo', 'LiquidationEnvasadoController@getArticlePrice')->name('dashboard.voucher.liquidations_envasado.get_article_price');
	Route::post('/facturacion/liquidaciones-envasado/obtener-cuentas-banco', 'LiquidationEnvasadoController@getBankAccounts')->name('dashboard.voucher.liquidations_envasado.get_bank_accounts');
	Route::post('/facturacion/liquidaciones-envasado/verificar-documento', 'LiquidationEnvasadoController@verifyDocumentType')->name('dashboard.voucher.liquidations_envasado.verify_document_type');
	Route::post('/facturacion/liquidaciones-envasado/guardar', 'LiquidationEnvasadoController@store')->name('dashboard.voucher.liquidations_envasado.store');
	Route::post('/facturacion/liquidaciones-envasado/listar-empleados', 'LiquidationEnvasadoController@getEmployees')->name('dashboard.voucher.liquidations_envasado.get_employees');
	Route::post('/facturacion/liquidaciones-envasado/validar-cliente', 'LiquidationEnvasadoController@validateClient')->name('dashboard.voucher.liquidations_envasado.validate_client');
	//	Route::get('/facturacion/liquidaciones-envasado/get-op-number', 'LiquidationEnvasadoController@getOperationNumber')->name('dashboard.voucher.liquidations_envasado.get_op_number');


	/** Facturación > Lista de Precios */
	Route::get('/facturacion/lista-de-precios', 'PriceListController@index')->name('dashboard.voucher.price_list');
	Route::post('/facturacion/lista-de-precios/validar-formulario', 'PriceListController@validateForm')->name('dashboard.voucher.price_list.validate_form');
	Route::post('/facturacion/lista-de-precios/listar', 'PriceListController@list')->name('dashboard.voucher.price_list.list');
	Route::post('/facturacion/lista-de-precios/obtener-dia', 'PriceListController@getMinEffectiveDate')->name('dashboard.voucher.price_list.get_min_effective_date');
	Route::post('/facturacion/lista-de-precios/guardar', 'PriceListController@store')->name('dashboard.voucher.price_list.store');

	/** Facturación > EFACT PUNTO MOTOR OSE */
	//Route::get('/motor/api', 'OsemotorController@sendOse')->name('dashboard.api.motor_efact');
	//Route::post('/motor/validar-formulario-documentos', 'OsemotorController@validate_voucher_form')->name('dashboard.api.motor_efact.validate_voucher_form');
	//Route::post('/motor/obtener-documentos-tabla', 'OsemotorController@get_vouchers_for_table')->name('dashboard.api.motor_efact.get_vouchers_for_table');
	//Route::post('/motor/listar', 'OsemotorController@list')->name('dashboard.motor_efact.list');
	//Route::post('/motor/obtener-detalle-documento', 'OsemotorController@get_voucher_detail')->name('dashboard.api.motor_efact.get_voucher_detail');
	//Route::post('/motor/enviar-documento', 'OsemotorController@send_voucher')->name('dashboard.api.motor_efact.send_voucher');


	/** Facturación > Otros Ingresos de Caja - Estiba y cilindros */
	Route::get('/facturacion/ingresos', 'RegisterOtherCashController@index')->name('dashboard.voucher.register_other_cash');
	Route::post('/facturacion/ingresos/validar-primer-paso', 'RegisterOtherCashController@validateFirstStep')->name('dashboard.voucher.register_other_cash.validate_first_step');
	Route::post('/facturacion/ingresos/obtener-tipo', 'RegisterOtherCashController@getAccounts')->name('dashboard.voucher.register_other_cash.get_accounts');
	Route::post('/facturacion/ingresos/validar-tercer-paso', 'RegisterOtherCashController@validateThirdStep')->name('dashboard.voucher.register_other_cash.validate_third_step');
	Route::post('/facturacion/ingresos/guardar', 'RegisterOtherCashController@store')->name('dashboard.voucher.register_other_cash.store');
	Route::post('/facturacion/ingresos/metodos-pago', 'RegisterOtherCashController@getPaymentMethod')->name('dashboard.voucher.list.register_other_cash.method');
	Route::post('/facturacion/ingresos/listar-bancos', 'RegisterOtherCashController@getBankAccount')->name('dashboard.voucher.list.register_other_cash.bank_account');

	/** Facturación > Edición de Facturas */
	Route::get('/facturacion-edit', 'VoucherEditController@index')->name('dashboard.fact_edit');
	Route::post('/facturacion-edit/listar-comprobantes', 'VoucherEditController@list')->name('dashboard.fact_edit.list');
	Route::post('/facturacion-edit/detalle', 'VoucherEditController@detail')->name('dashboard.fact_edit.detail');
	Route::post('/facturacion-edit/actualizar', 'VoucherEditController@update')->name('dashboard.fact_edit.update');


	/** Reportes > Cuentas Corrientes Clientes */
	Route::get('/reporte/cuentas-corrientes-clientes', 'CheckingAccountReportController@index')->name('dashboard.report.checking_account_report');
	Route::post('/reporte/cuentas-corrientes-clientes/obtener-clientes', 'CheckingAccountReportController@getClients')->name('dashboard.report.checking_account_report.get_clients');
	Route::post('/reporte/cuentas-corrientes-clientes/validar-formulario', 'CheckingAccountReportController@validateForm')->name('dashboard.report.checking_account_report.validate_form');
	Route::post('/reporte/cuentas-corrientes-clientes/listar', 'CheckingAccountReportController@list')->name('dashboard.report.checking_account_report.list');

	/** Reportes > Kardex */
	Route::get('/reporte/kardex', 'KardexController@index')->name('dashboard.report.kardex');
	Route::post('/reporte/kardex/obtener-articulos', 'KardexController@getArticles')->name('dashboard.report.kardex.get_articles');
	Route::post('/reporte/kardex/obtener-cuentas', 'KardexController@getAccounts')->name('dashboard.report.kardex.get_accounts');
	Route::post('/reporte/kardex/validar-formulario', 'KardexController@validateForm')->name('dashboard.report.kardex.validate_form');
	Route::post('reporte/kardex/listar', 'KardexController@list')->name('dashboard.report.kardex.list');

	/** Reportes > Liquidaciones */
	Route::get('/reporte/liquidaciones', 'LiquidationReportController@index')->name('dashboard.report.liquidations');
	Route::post('/reporte/liquidaciones/validar-formulario', 'LiquidationReportController@validateForm')->name('dashboard.report.liquidations.validate_form');
	Route::post('/reporte/liquidaciones/obtener-clientes', 'LiquidationReportController@getClients')->name('dashboard.report.liquidations.get_clients');
	Route::post('/reporte/liquidaciones/listar', 'LiquidationReportController@list')->name('dashboard.report.liquidations.list');

	/** Reportes > Efectivo */
	Route::get('/facturacion/efectivo', 'EfectivesReportController@index')->name('dashboard.voucher.efectives');
	Route::post('/facturacion/efectivo/validar-formulario', 'EfectivesReportController@validateForm')->name('dashboard.voucher.efectives.validate_form');
	Route::post('/facturacion/efectivo/obtener-clientes', 'EfectivesReportController@getClients')->name('dashboard.voucher.efectives.get_clients');
	Route::post('/facturacion/efectivo/listar', 'EfectivesReportController@list')->name('dashboard.voucher.efectives.list');

	/** Reportes > Depositos */
	Route::get('/facturacion/deposito', 'VoucherReportController@index')->name('dashboard.voucher.deposits');
	Route::post('/facturacion/deposito/validar-formulario', 'VoucherReportController@validateForm')->name('dashboard.voucher.deposits.validate_form');
	Route::post('/facturacion/deposito/obtener-clientes', 'VoucherReportController@getClients')->name('dashboard.voucher.deposits.get_clients');
	Route::post('/facturacion/deposito/listar', 'VoucherReportController@list')->name('dashboard.voucher.deposits.list');

	/** Reportes > Liquidaciones Resumido*/
	Route::get('/reporte/liquidaciones-resumen', 'LiquidationTotalReportController@index')->name('dashboard.report.liquidations_total');
	Route::post('/reporte/liquidaciones-resumen/validar-formulario', 'LiquidationTotalReportController@validateForm')->name('dashboard.report.liquidations_total.validate_form');
	Route::post('/reporte/liquidaciones-resumen/obtener-clientes', 'LiquidationTotalReportController@getClients')->name('dashboard.report.liquidations_total.get_clients');
	Route::post('/reporte/liquidaciones-resumen/listar', 'LiquidationTotalReportController@list')->name('dashboard.report.liquidations_total.list');

	/** Reportes > Prueba Guias */
	Route::get('/reporte/guias-registro', 'StockSeekRegisterReportController@index')->name('dashboard.report.stock_seek_register');
	Route::post('/reporte/guias-registro/validar-formulario', 'StockSeekRegisterReportController@validateForm')->name('dashboard.report.stock_seek_register.validate_form');
	Route::post('/reporte/guias-registro/obtener-guias', 'StockSeekRegisterReportController@getWarehouseMovements')->name('dashboard.report.stock_seek_register.get_warehouse_movements');
	Route::post('/reporte/guias-registro/listar', 'StockSeekRegisterReportController@list')->name('dashboard.report.stock_seek_register.list');
	Route::post('/reporte/guias-registro/detalle', 'StockSeekRegisterReportController@detail')->name('dashboard.report.stock_seek_register.detail');
	Route::post('/reporte/guias-registro/actualizar', 'StockSeekRegisterReportController@update')->name('dashboard.report.stock_seek_register.update');


	/** Reportes > Guias */
	Route::get('/reporte/guias', 'MovementsFinalReportController@index')->name('dashboard.report.movements_final');
	Route::post('/reporte/guias-final/validar-formulario', 'MovementsFinalReportController@validateForm')->name('dashboard.report.movements_final.validate_form');
	Route::post('/reporte/guias-final/obtener-guias', 'MovementsFinalReportController@getWarehouseMovements')->name('dashboard.report.movements_final.get_warehouse_movements');
	Route::post('/reporte/guias-final/listar-cuentas', 'MovementsFinalReportController@getAccounts')->name('dashboard.report.movements_final.get_accounts');
	Route::post('/reporte/guias-final/listar', 'MovementsFinalReportController@list')->name('dashboard.report.movements_final.list');
	Route::post('/reporte/guias-final/detalle', 'MovementsFinalReportController@detail')->name('dashboard.report.movements_final.detail');
	Route::post('/reporte/guias-final/actualizar', 'MovementsFinalReportController@update')->name('dashboard.report.movements_final.update');

	/** Reportes > Buscador de Guías */
	Route::get('/reporte/buscador-guias', 'GuidesSeekReportController@index')->name('dashboard.report.guides_seek');
	Route::post('/reporte/buscador-guias/validar-formulario', 'GuidesSeekReportController@validateForm')->name('dashboard.report.guides_seek.validate_form');
	Route::post('/reporte/buscador-guias/listar', 'GuidesSeekReportController@list')->name('dashboard.report.guides_seek.list');

	/** Reportes > Estado de Guías */
	Route::get('/reporte/estado-guias', 'GuidesScopReportController@index')->name('dashboard.report.guides_scop');
	Route::post('/reporte/estado-guias/validar-formulario', 'GuidesScopReportController@validateForm')->name('dashboard.report.guides_scop.validate_form');
	Route::post('/reporte/estado-guias/obtener-clientes', 'GuidesScopReportController@getClients')->name('dashboard.report.guides_scop.get_warehouse_movements');
	Route::post('/reporte/estado-guias/listar', 'GuidesScopReportController@list')->name('dashboard.report.guides_scop.list');
	Route::post('/reporte/estado-guias/detalle', 'GuidesScopReportController@detail')->name('dashboard.report.guides_scop.detail');
	Route::post('/reporte/estado-guias/actualizar', 'GuidesScopReportController@update')->name('dashboard.report.guides_scop.update');


	/** Reportes > Facturas emitidas */
	Route::get('/reporte/facturas-emitidas', 'FacturationSalesReportController@index')->name('dashboard.report.facturations_sales');
	Route::post('/reporte/facturas-emitidas/validar-formulario', 'FacturationSalesReportController@validateForm')->name('dashboard.report.facturations_sales.validate_form');
	Route::post('/reporte/facturas-emitidas/obtener-clientes', 'FacturationSalesReportController@getClients')->name('dashboard.report.facturations_sales.get_clients');
	Route::post('/reporte/facturas-emitidas/listar', 'FacturationSalesReportController@list')->name('dashboard.report.facturations_sales.list');
	Route::post('/reporte/facturas-emitidas/obtener-voucher', 'FacturationSalesReportController@getVoucher')->name('dashboard.report.facturations_sales.get_voucher');
	Route::post('/reporte/facturas-emitidas/actualizar-voucher', 'FacturationSalesReportController@updateVoucher')->name('dashboard.report.facturations_sales.update_voucher');
	Route::post('/reporte/facturas-emitidas/eliminar-voucher', 'FacturationSalesReportController@deleteVoucher')->name('dashboard.report.facturations_sales.delete_voucher');

	/** Reportes > Boletas emitidas */
	Route::get('/reporte/boletas-emitidas', 'ContabilidadBoletasReportController@index')->name('dashboard.report.contabilidad_boletas');
	Route::post('/reporte/boletas-emitidas/validar-formulario', 'ContabilidadBoletasReportController@validateForm')->name('dashboard.report.contabilidad_boletas.validate_form');
	Route::post('/reporte/boletas-emitidas/obtener-clientes', 'ContabilidadBoletasReportController@getClients')->name('dashboard.report.contabilidad_boletas.get_clients');
	Route::post('/reporte/boletas-emitidas/listar', 'ContabilidadBoletasReportController@list')->name('dashboard.report.contabilidad_boletas.list');

	/** Reportes > Venta Comercial */

	Route::get('/reporte/venta-comercial', 'LiquidationSalesReportController@index')->name('dashboard.report.liquidations_sales');
	Route::post('/reporte/venta-comercial/validar-formulario', 'LiquidationSalesReportController@validateForm')->name('dashboard.report.liquidations_sales.validate_form');
	Route::post('/reporte/venta-comercial/obtener-clientes', 'LiquidationSalesReportController@getClients')->name('dashboard.report.liquidations_sales.get_clients');
	Route::post('/reporte/venta-comercial/listar', 'LiquidationSalesReportController@list')->name('dashboard.report.liquidations_sales.list');

	/** Reportes > Venta Canal*/
	Route::get('/reporte/venta-canal', 'LiquidationChannelReportController@index')->name('dashboard.report.liquidations_channel');
	Route::post('/reporte/venta-canal/validar-formulario', 'LiquidationChannelReportController@validateForm')->name('dashboard.report.liquidations_channel.validate_form');
	Route::post('/reporte/venta-canal/obtener-clientes', 'LiquidationChannelReportController@getClients')->name('dashboard.report.liquidations_channel.get_clients');
	Route::post('/reporte/venta-canal/listar', 'LiquidationChannelReportController@list')->name('dashboard.report.liquidations_channel.list');

	/** Reportes > Reporte de Exedente*/
	Route::get('/reporte/exedente', 'ExedentReportController@index')->name('dashboard.report.exedent');
	Route::post('/reporte/exedente/validar-formulario', 'ExedentReportController@validateForm')->name('dashboard.report.exedent.validate_form');
	Route::post('/reporte/exedente/obtener-clientes', 'ExedentReportController@getClients')->name('dashboard.report.exedent.get_clients');
	Route::post('/reporte/exedente/listar', 'ExedentReportController@list')->name('dashboard.report.exedent.list');

	/** Reportes > Clientes Perdidos*/
	Route::get('/reporte/venta-perdidos', 'LiquidationComercialReportController@index')->name('dashboard.report.liquidations_comercial');
	Route::post('/reporte/venta-perdidos/validar-formulario', 'LiquidationComercialReportController@validateForm')->name('dashboard.report.liquidations_comercial.validate_form');
	Route::post('/reporte/venta-perdidos/obtener-clientes', 'LiquidationComercialReportController@getClients')->name('dashboard.report.liquidations_comercial.get_clients');
	Route::post('/reporte/venta-perdidos/listar', 'LiquidationComercialReportController@list')->name('dashboard.report.liquidations_comercial.list');

	/** Reportes > Clientes Perdidos Semestral*/
	Route::get('/reporte/semestral-perdidos', 'LiquidationSixComercialReportController@index')->name('dashboard.report.liquidations_six_comercial');
	Route::post('/reporte/semestral-perdidos/validar-formulario', 'LiquidationSixComercialReportController@validateForm')->name('dashboard.report.liquidations_six_comercial.validate_form');
	Route::post('/reporte/semestral-perdidos/obtener-clientes', 'LiquidationSixComercialReportController@getClients')->name('dashboard.report.liquidations_six_comercial.get_clients');
	Route::post('/reporte/semestral-perdidos/listar', 'LiquidationSixComercialReportController@list')->name('dashboard.report.liquidations_six_comercial.list');

	/** Reportes > Clientes Nuevos Semestral*/
	Route::get('/reporte/semestral-nuevos', 'ClientsSixComercialReportController@index')->name('dashboard.report.clients_six_comercial');
	Route::post('/reporte/semestral-nuevos/validar-formulario', 'ClientsSixComercialReportController@validateForm')->name('dashboard.report.clients_six_comercial.validate_form');
	Route::post('/reporte/semestral-nuevos/obtener-clientes', 'ClientsSixComercialReportController@getClients')->name('dashboard.report.clients_six_comercial.get_clients');
	Route::post('/reporte/semestral-nuevos/listar', 'ClientsSixComercialReportController@list')->name('dashboard.report.clients_six_comercial.list');


        /** comercial > Presupuesto Venta Mensual */
        Route::get('/comercial/presupuesto', 'SalePresupuestoController@index')->name('dashboard.voucher.sale_presupuesto');
        Route::post('/comercial/presupuesto/validar-formulario', 'SalePresupuestoController@validateForm')->name('dashboard.voucher.sale_presupuesto.validate_form');
        Route::post('/comercial/presupuesto/listar', 'SalePresupuestoController@list')->name('dashboard.voucher.sale_presupuesto.list');
        Route::post('/comercial/presupuesto/guardar', 'SalePresupuestoController@store')->name('dashboard.voucher.sale_presupuesto.store');
        Route::post('/comercial/presupuesto/detalle', 'SalePresupuestoController@detail')->name('dashboard.voucher.sale_presupuesto.detail');
        Route::post('/comercial/presupuesto/eliminar', 'SalePresupuestoController@delete')->name('dashboard.voucher.sale_presupuesto.delete');
        Route::post('/comercial/presupuesto/export', 'SalePresupuestoController@export')->name('dashboard.voucher.sale_presupuesto.export');

    /** Comercial > REPORTE VENTA DIARIA*/
		Route::get('/reportes/venta-diaria', 'SaleDayReportController@index')->name('dashboard.sale_day_report.index');
		Route::post('/reportes/venta-diaria/exportar', 'SaleDayReportController@export')->name('dashboard.sale_day_report.export');
		Route::post('/reportes/venta-diaria/validar-formulario', 'SaleDayReportController@validateForm')->name('dashboard.sale_day_report.validate_form');

         /** Comercial > REPORTE LISTA DE PRECIO*/
		Route::get('/reportes/lista-precio', 'ListPriceReportController@index')->name('dashboard.list_price_report.index');
		Route::post('/reportes/lista-precio/exportar', 'ListPriceReportController@export')->name('dashboard.list_price_report.export');
		Route::post('/reportes/lista-precio/validar-formulario', 'ListPriceReportController@validateForm')->name('dashboard.list_price_report.validate_form');


        	    /** Comercial > CARTERA CLIENTES LOCALES*/
		Route::get('/reportes/cartera-locales', 'ReportPortfolioLocalController@index')->name('dashboard.portfolio_local.index');
		Route::post('/reportes/cartera-locales/exportar', 'ReportPortfolioLocalController@export')->name('dashboard.portfolio_local.export');
		Route::post('/reportes/cartera-locales/validar-formulario', 'ReportPortfolioLocalController@validateForm')->name('dashboard.portfolio_local.validate_form');


	/** Reportes > Clientes Nuevos TM  Semestral*/
	Route::get('/reporte/semestral-tm', 'ClientsTmSixComercialReportController@index')->name('dashboard.report.clients_tm_six_comercial');
	Route::post('/reporte/semestral-tm/validar-formulario', 'ClientsTmSixComercialReportController@validateForm')->name('dashboard.report.clients_tm_six_comercial.validate_form');
	Route::post('/reporte/semestral-tm/obtener-clientes', 'ClientsTmSixComercialReportController@getClients')->name('dashboard.report.clients_tm_six_comercial.get_clients');
	Route::post('/reporte/semestral-tm/listar', 'ClientsTmSixComercialReportController@list')->name('dashboard.report.clients_tm_six_comercial.list');

	/** Reportes > Clientes Perdidos VDC*/
	Route::get('/reporte/vdc-venta-perdidos', 'VdcComercialReportController@index')->name('dashboard.report.vdc_comercial');
	Route::post('/reporte/vdc-venta-perdidos/validar-formulario', 'VdcComercialReportController@validateForm')->name('dashboard.report.vdc_comercial.validate_form');
	Route::post('/reporte/vdc-venta-perdidos/obtener-clientes', 'VdcComercialReportController@getClients')->name('dashboard.report.vdc_comercial.get_clients');
	Route::post('/reporte/vdc-venta-perdidos/listar', 'VdcComercialReportController@list')->name('dashboard.report.vdc_comercial.list');

	/** Reportes > Clientes Perdidos Semestral VDC*/
	Route::get('/reporte/vdc-semestral-perdidos', 'VdcSixComercialReportController@index')->name('dashboard.report.vdc_six_comercial');
	Route::post('/reporte/vdc-semestral-perdidos/validar-formulario', 'VdcSixComercialReportController@validateForm')->name('dashboard.report.vdc_six_comercial.validate_form');
	Route::post('/reporte/vdc-semestral-perdidos/obtener-clientes', 'VdcSixComercialReportController@getClients')->name('dashboard.report.vdc_six_comercial.get_clients');
	Route::post('/reporte/vdc-semestral-perdidos/listar', 'VdcSixComercialReportController@list')->name('dashboard.report.vdc_six_comercial.list');

	/** Reportes > Clientes Perdidos granel*/
	Route::get('/reporte/granel-venta-perdidos', 'GranelComercialReportController@index')->name('dashboard.report.granel_comercial');
	Route::post('/reporte/granel-venta-perdidos/validar-formulario', 'GranelComercialReportController@validateForm')->name('dashboard.report.granel_comercial.validate_form');
	Route::post('/reporte/granel-venta-perdidos/obtener-clientes', 'GranelComercialReportController@getClients')->name('dashboard.report.granel_comercial.get_clients');
	Route::post('/reporte/granel-venta-perdidos/listar', 'GranelComercialReportController@list')->name('dashboard.report.granel_comercial.list');

	/** Reportes > Clientes Perdidos Semestral granel*/
	Route::get('/reporte/granel-semestral-perdidos', 'GranelSixComercialReportController@index')->name('dashboard.report.granel_six_comercial');
	Route::post('/reporte/granel-semestral-perdidos/validar-formulario', 'GranelSixComercialReportController@validateForm')->name('dashboard.report.granel_six_comercial.validate_form');
	Route::post('/reporte/granel-semestral-perdidos/obtener-clientes', 'GranelSixComercialReportController@getClients')->name('dashboard.report.granel_six_comercial.get_clients');
	Route::post('/reporte/granel-semestral-perdidos/listar', 'GranelSixComercialReportController@list')->name('dashboard.report.granel_six_comercial.list');

	/** Reportes > Venta Canal Resumido*/
	Route::get('/reporte/venta-canal-resumido', 'LiquidationChannelTotalReportController@index')->name('dashboard.report.liquidations_channel_total');
	Route::post('/reporte/venta-canal-resumido/validar-formulario', 'LiquidationChannelTotalReportController@validateForm')->name('dashboard.report.liquidations_channel_total.validate_form');
	Route::post('/reporte/venta-canal-resumido/obtener-clientes', 'LiquidationChannelTotalReportController@getClients')->name('dashboard.report.liquidations_channel_total.get_clients');
	Route::post('/reporte/venta-canal-resumido/listar', 'LiquidationChannelTotalReportController@list')->name('dashboard.report.liquidations_channel_total.list');


	/** Reportes > Liquidación Detallado */
	Route::get('/reporte/liquidacion-detallado', 'CollectionSalesReportController@index')->name('dashboard.report.collection_sales_report');
	Route::post('/reporte/liquidacion-detallado/obtener-clientes', 'CollectionSalesReportController@getClients')->name('dashboard.report.collection_sales_report.get_clients');
	Route::post('/reporte/liquidacion-detallado/validar-formulario', 'CollectionSalesReportController@validateForm')->name('dashboard.report.collection_sales_report.validate_form');
	Route::post('/reporte/liquidacion-detallado/listar', 'CollectionSalesReportController@list')->name('dashboard.report.collection_sales_report.list');

	/** Reportes > Lista de Precios */
	Route::get('/reporte/lista-de-precios', 'PriceListReportController@index')->name('dashboard.report.price_list');
	Route::post('/reporte/lista-de-precios/validar-formulario', 'PriceListReportController@validateForm')->name('dashboard.report.price_list.validate_form');
	Route::post('/reporte/lista-de-precios/obtener-clientes', 'PriceListReportController@getClients')->name('dashboard.report.price_list.get_clients');
	Route::post('/reporte/lista-de-precios/obtener-articulos', 'PriceListReportController@getArticles')->name('dashboard.report.price_list.get_articles');
	Route::post('/reporte/lista-de-precios/listar', 'PriceListReportController@list')->name('dashboard.report.price_list.list');
	Route::post('/reporte/lista-de-precios/exportar-pdf', 'PriceListReportController@exportPdf')->name('dashboard.report.price_list.export_pdf');

	/** Reportes > Movimiento de Existencias */
	Route::get('/reporte/movimiento-existencias', 'MovementsReportController@index')->name('dashboard.report.movements');
	Route::get('/reporte/movimiento-existencias-valorizado', 'MovementsReportController@index')->name('dashboard.report.movements_valued');
	Route::post('/reporte/movimiento-existencias/validar-formulario', 'MovementsReportController@validateForm')->name('dashboard.report.movements.validate_form');
	Route::post('/reporte/movimiento-existencias/listar', 'MovementsReportController@list')->name('dashboard.report.movements.list');
	Route::post('/reporte/movimiento-existencias/detalle', 'MovementsReportController@detail')->name('dashboard.report.movements.detail');
	Route::post('/reporte/movimiento-existencias/actualizar', 'MovementsReportController@update')->name('dashboard.report.movements.update');
	Route::post('/reporte/movimiento-existencias/getAccount', 'MovementsReportController@getAccounts')->name('dashboard.report.movements.get_account');


	/** Reportes > Parte de Almacén */
	Route::get('/reporte/parte-almacen', 'WarehousePartController@index')->name('dashboard.report.warehouse_part');
	Route::post('/reporte/parte-almacen/validar-formulario', 'WarehousePartController@validateForm')->name('dashboard.report.warehouse_part.validate_form');
	Route::post('/reporte/parte-almacen/obtener-movimientos-almacen', 'WarehousePartController@getWarehouseMovements')->name('dashboard.report.warehouse_part.get_warehouse_movements');
	Route::post('/reporte/parte-almacen/listar', 'WarehousePartController@list')->name('dashboard.report.warehouse_part.list');
	Route::post('/reporte/parte-almacen/exportar', 'WarehousePartController@export')->name('dashboard.report.warehouse_part.export');
	Route::get('/reporte/parte-almacen/exportar-guia-remision', 'WarehousePartController@exportReferralGuide')->name('dashboard.report.warehouse_part.export_referral_guide');

	/** Reportes > Reporte de Percepciones */
	Route::get('/reporte/registro-ventas', 'SalesRegisterReportController@index')->name('dashboard.report.sales-register');
	Route::post('/reporte/registro-ventas/validar-formulario', 'SalesRegisterReportController@validateForm')->name('dashboard.report.sales-register.validate_form');
	Route::post('/reporte/registro-ventas/listar', 'SalesRegisterReportController@list')->name('dashboard.report.sales-register.list');

	/** Reportes > Relación de Clientes */
	Route::get('/reporte/relacion-de-clientes', 'ListClientsReportController@index')->name('dashboard.report.list_clients');
	Route::post('/reporte/relacion-de-clientes/validar-formulario', 'ListClientsReportController@validateForm')->name('dashboard.report.list_clients.validate_form');
	Route::post('/reporte/lista-de-precios/obtener-clientes', 'PriceListReportController@getClients')->name('dashboard.report.price_list.get_clients');
	Route::post('/reporte/relacion-de-clientes/listar', 'ListClientsReportController@list')->name('dashboard.report.list_clients.list');

	/** Reportes > Relación de Cobranzas */
	Route::get('/reporte/relacion-de-cobranzas', 'CollectionNewReportController@index')->name('dashboard.report.collection_report');
	Route::post('/reporte/relacion-de-cobranzas/obtener-clientes', 'CollectionNewReportController@getClients')->name('dashboard.report.collection_report.get_clients');
	Route::post('/reporte/relacion-de-cobranzas/validar-formulario', 'CollectionNewReportController@validateForm')->name('dashboard.report.collection_report.validate_form');
	Route::post('/reporte/relacion-de-cobranzas/listar', 'CollectionNewReportController@list')->name('dashboard.report.collection_report.list');

	/** Reportes > Reporte de Ventas */
	Route::get('/reporte/reporte-de-ventas', 'SalesReportController@index')->name('dashboard.report.sales-report');
	Route::post('/reporte/reporte-de-ventas/validar-formulario', 'SalesReportController@validateForm')->name('dashboard.report.sales-report.validate_form');
	Route::post('/reporte/reporte-de-ventas/obtener-precio', 'SalesReportController@getCurrentPrice')->name('dashboard.report.sales-report.get_current_price');
	Route::post('/reporte/reporte-de-ventas/listar', 'SalesReportController@list')->name('dashboard.report.sales-report.list');
	Route::post('/reporte/reporte-de-ventas/detalle', 'SalesReportController@detail')->name('dashboard.report.sales-report.detail');
	Route::post('/reporte/reporte-de-ventas/exportar', 'SalesReportController@export')->name('dashboard.report.sales-report.export');
	// Route::get('/reporte/reporte-de-ventas/actualizar-id-cliente', 'SalesReportController@updateClientId');

	/** Reportes > Reporte de Ventas > Administrador de Presupuestos */
	Route::get('/reporte/reporte-de-ventas/presupuestos', 'BudgetController@index')->name('dashboard.report.sales-report.budgets');
	Route::post('/reporte/reporte-de-ventas/presupuestos/validar-formulario', 'BudgetController@validateForm')->name('dashboard.report.sales-report.budgets.validate_form');
	Route::post('/reporte/reporte-de-ventas/presupuestos/listar', 'BudgetController@list')->name('dashboard.report.sales-report.budgets.list');
	Route::get('/reporte/reporte-de-ventas/presupuestos/listar', 'BudgetController@list')->name('dashboard.report.sales-report.budgets.list');
	Route::post('/reporte/reporte-de-ventas/presupuestos/guardar', 'BudgetController@store')->name('dashboard.report.sales-report.budgets.store');
	Route::get('/reporte/reporte-de-ventas/presupuestos/guardar', 'BudgetController@store')->name('dashboard.report.sales-report.budgets.store');

	/** Reportes > Relación de Documentos Pendientes */
	Route::get('/reporte/relacion-de-documentos-pendientes', 'PendingDocumentReportController@index')->name('dashboard.report.pending_document_report');
	Route::post('/reporte/relacion-de-documentos-pendientes/obtener-clientes', 'PendingDocumentReportController@getClients')->name('dashboard.report.pending_document_report.get_clients');
	Route::post('/reporte/relacion-de-documentos-pendientes/validar-formulario', 'PendingDocumentReportController@validateForm')->name('dashboard.report.pending_document_report.validate_form');
	Route::post('/reporte/relacion-de-documentos-pendientes/listar', 'PendingDocumentReportController@list')->name('dashboard.report.pending_document_report.list');

	/** Reportes > Relación de Documentos Emitidos */
	Route::get('/reporte/relacion-de-documentos-por-cobrar', 'UncollectedDocumentReportController@index')->name('dashboard.report.uncollected_document_report');
	Route::post('/reporte/relacion-de-documentos-por-cobrar/obtener-clientes', 'UncollectedDocumentReportController@getClients')->name('dashboard.report.uncollected_document_report.get_clients');
	Route::post('/reporte/relacion-de-documentos-por-cobrar/validar-formulario', 'UncollectedDocumentReportController@validateForm')->name('dashboard.report.uncollected_document_report.validate_form');
	Route::post('/reporte/relacion-de-documentos-por-cobrar/listar', 'UncollectedDocumentReportController@list')->name('dashboard.report.uncollected_document_report.list');

	/** Reportes > Crédito histórico */
	Route::get('/reporte/credito-historico', 'CreditHistoryReportController@index')->name('dashboard.report.credit_history');
	Route::post('/reporte/credito-historico/validar-formulario', 'CreditHistoryReportController@validateForm')->name('dashboard.report.credit_history.validate_form');
	Route::post('/reporte/credito-historico/listar', 'CreditHistoryReportController@list')->name('dashboard.report.credit_history.list');

	/** Control de Planta > Apertura de Caja */
	Route::get('/facturacion/apertura-caja/registrar', 'RegisterOpeningCajaController@index')->name('dashboard.opening.caja');
	Route::post('/facturacion/apertura-caja/registrar-voucher', 'RegisterOpeningCajaController@store')->name('dashboard.opening.caja_register');
	Route::post('/facturacion/apertura-caja/listar', 'RegisterOpeningCajaController@list')->name('dashboard.opening.caja_list');

	/** Control de Planta > Apertura de Planta */
	Route::get('/facturacion/apertura-planta/registrar', 'RegisterOpeningPlantaController@index')->name('dashboard.opening.planta');
	Route::post('/facturacion/apertura-planta/registrar-voucher', 'RegisterOpeningPlantaController@store')->name('dashboard.opening.planta_register');
	Route::post('/facturacion/apertura-planta/listar', 'RegisterOpeningPlantaController@list')->name('dashboard.opening.planta_list');




	/** Solicitud de Requerimientos */

	/** ADMINISTRACION Y FINANZAS > Registro de Solicitud de Requerimiento */
	Route::get('/operaciones/registro-solicitud-requerimiento', 'SolReqRegisterController@index')->name('dashboard.operations.sol_req_register');
	Route::post('/operaciones/registro-solicitud-requerimiento/listar-areas', 'SolReqRegisterController@getAreas')->name('dashboard.operations.sol_req_register.get_areas');
	Route::post('/operaciones/registro-solicitud-requerimiento/guardar', 'SolReqRegisterController@store')->name('dashboard.operations.sol_req_register.store');
	Route::post('/operaciones/registro-solicitud-requerimiento/listar-tipos', 'SolReqRegisterController@getTypes')->name('dashboard.operations.sol_req_register.get_types');
	Route::post('/operaciones/registro-solicitud-requerimiento/listar-subtipos', 'SolReqRegisterController@getSubTypes')->name('dashboard.operations.sol_req_register.get_sub_types');
	Route::post('/operaciones/registro-solicitud-requerimiento/listar-presupuesto', 'SolReqRegisterController@getPresupuesto')->name('dashboard.operations.sol_req_register.get_presupuesto');
	Route::post('/operaciones/registro-solicitud-requerimiento/listar-costos', 'SolReqRegisterController@getCostos')->name('dashboard.operations.sol_req_register.get_costos');
	Route::post('/operaciones/registro-solicitud-requerimiento/listar-costo-secs', 'SolReqRegisterController@getCostoSecs')->name('dashboard.operations.sol_req_register.get_costo_secs');
	Route::post('/operaciones/registro-solicitud-requerimiento/obtener-num-mov', 'SolReqRegisterController@getNumMov')->name('dashboard.operations.sol_req_register.get_num_mov');

	/** ADMINISTRACION Y FINANZAS > Estado de Solicitudes */
	Route::get('/operaciones/estado-solicitud', 'StateSolController@index')->name('dashboard.voucher.state_sol');
	Route::post('/operaciones/estado-solicitud/validar-formulario', 'StateSolController@validateForm')->name('dashboard.voucher.state_sol.validate_form');
	Route::post('/operaciones/estado-solicitud/obtener-areas', 'StateSolController@getAreaAlls')->name('dashboard.voucher.state_sol.get_area_alls');
	Route::post('/operaciones/estado-solicitud/listar', 'StateSolController@list')->name('dashboard.voucher.state_sol.list');
	Route::post('/operaciones/estado-solicitud/detalle', 'StateSolController@detail')->name('dashboard.voucher.state_sol.detail');
	Route::post('/operaciones/estado-solicitud/obtener-cont', 'StateSolController@getImportCont')->name('dashboard.voucher.state_sol.get_import_cont');
	Route::post('/operaciones/estado-solicitud/obtener-fi', 'StateSolController@getImportFi')->name('dashboard.voucher.state_sol.get_import_fi');
	Route::post('/operaciones/estado-solicitud/actualizar-cont', 'StateSolController@updateCont')->name('dashboard.voucher.state_sol.update_cont');
	Route::post('/operaciones/estado-solicitud/actualizar-fi', 'StateSolController@updateFi')->name('dashboard.voucher.state_sol.update_fi');
	Route::post('/operaciones/estado-solicitud/actualizar-tes', 'StateSolController@updateTes')->name('dashboard.voucher.state_sol.update_tes');
	Route::post('/operaciones/estado-solicitud/verificar-registro-cont', 'StateSolController@verifyRegisterCont')->name('dashboard.voucher.state_sol.verify_register_cont');
	Route::post('/operaciones/estado-solicitud/aprobar-cont', 'StateSolController@aprobarCont')->name('dashboard.voucher.state_sol.aprobar_cont');
	Route::post('/operaciones/estado-solicitud/desaprobar-cont', 'StateSolController@desaprobarCont')->name('dashboard.voucher.state_sol.desaprobar_cont');

	/** ADMINISTRACION Y FINANZAS > SUBIDA DE DOCUMENTOS A RENDIR */

	Route::get('/operaciones/subida-rendir', 'LiqRendirRegisterController@index')->name('dashboard.operations.liq_rendir');
	Route::post('/operaciones/subida-rendir/listar-areas', 'LiqRendirRegisterController@getAreas')->name('dashboard.operations.liq_rendir.get_areas');
	Route::post('/operaciones/subida-rendir/guardar', 'LiqRendirRegisterController@store')->name('dashboard.operations.liq_rendir.store');
	Route::post('/operaciones/subida-rendir/listar-presupuesto', 'LiqRendirRegisterController@getPresupuesto')->name('dashboard.operations.liq_rendir.get_presupuesto');
	Route::post('/operaciones/subida-rendir/listar-costos', 'LiqRendirRegisterController@getCostos')->name('dashboard.operations.liq_rendir.get_costos');
	Route::post('/operaciones/subida-rendir/listar-costo-secs', 'LiqRendirRegisterController@getCostoSecs')->name('dashboard.operations.liq_rendir.get_costo_secs');
	Route::post('/operaciones/subida-rendir/obtener-aplicacion', 'LiqRendirRegisterController@getAplication')->name('dashboard.operations.liq_rendir.get_aplication');



	/** Logística > Pedido de orden de compra */
	Route::get('/logistica/registro-orden-pedido', 'ArticleOrderController@index')->name('dashboard.logistics.index');
	Route::post('/logistica/registro-orden-areas', 'ArticleOrderController@getArea')->name('dashboard.logistics.get_area');
	Route::post('/logistica/registro-orden-listar', 'ArticleOrderController@list')->name('dashboard.logistics.list');
	Route::post('/logistica/registro-orden-crear', 'ArticleOrderController@store')->name('dashboard.logistics.store');
	Route::post('/logistica/registro-orden-articulo', 'ArticleOrderController@getArticulo')->name('dashboard.logistics.get_article');
	Route::post('/logistica/registro-orden-validar-articulo', 'ArticleOrderController@validateModal')->name('dashboard.logistics.validate_modal');

	/** Logística > Reporte de orden de compra */
	Route::get('/logistica/reporte-orden', 'ArticleOrderReportController@index')->name('dashboard.logistics.report.order.index');
	Route::post('/logistica/reporte-orden/validar-formulario', 'ArticleOrderReportController@validateForm')->name('dashboard.logistics.report.order.validate_form');
	Route::post('/logistica/reporte-orden/listar', 'ArticleOrderReportController@list')->name('dashboard.logistics.report.order.list');
	Route::post('/logistica/reporte-orden/detalle', 'ArticleOrderReportController@detail')->name('dashboard.logistics.report.order.detail');
	Route::post('/logistica/reporte-orden/exportar', 'ArticleOrderReportController@export')->name('dashboard.logistics.report.order.export');

	/** Logística > Servicios */
	Route::get('/logistica/servicios', 'ServiceController@index')->name('dashboard.services');
	Route::post('/logistica/servicios/validar-formulario', 'ServiceController@validateForm')->name('dashboard.services.validate_form');
	Route::post('/logistica/servicios/listar', 'ServiceController@listServices')->name('dashboard.services.list');
	Route::post('/logistica/servicios/detalle', 'ServiceController@detail')->name('dashboard.services.detail');
	Route::post('/logistica/servicios/eliminar', 'ServiceController@delete')->name('dashboard.services.delete');
	Route::post('/logistica/servicios/guardar', 'ServiceController@store')->name('dashboard.services.store');
	Route::post('/logistica/servicios/listar/areas', 'ServiceController@getAreas')->name('dashboard.services.list.areas');
	Route::post('/logistica/servicios/exportar', 'ServiceController@exportRecord')->name('dashboard.services.export_record');

	/** Logística > Registro Movimiento de Existencias */
	Route::get('/logistica/registro-movimiento-existencias', 'StockLogRegisterController@index')->name('dashboard.logistics.stock_log_register');
	Route::post('/logistica/registro-movimiento-existencias/validar-formulario', 'StockLogRegisterController@validateForm')->name('dashboard.logistics.stock_log_register.validate_form');
	Route::post('/logistica/registro-movimiento-existencias/listar', 'StockLogRegisterController@list')->name('dashboard.logistics.stock_log_register.list');
	Route::post('/logistica/registro-movimiento-existencias/listar-cuentas', 'StockLogRegisterController@getAccounts')->name('dashboard.logistics.stock_log_register.get_accounts');
	Route::post('/logistica/registro-movimiento-existencias/listar-articulos', 'StockLogRegisterController@getArticles')->name('dashboard.logistics.stock_log_register.get_articles');
	Route::post('/logistica/registro-movimiento-existencias/obtener-percepcion', 'StockLogRegisterController@getPerceptionPercentage')->name('dashboard.logistics.stock_log_register.get_perception_percentage');
	Route::post('/logistica/registro-movimiento-existencias/obtener-tasas', 'StockLogRegisterController@getArticleRates')->name('dashboard.logistics.stock_log_register.get_article_rates');
	Route::post('/logistica/registro-movimiento-existencias/obtener-articulo', 'StockLogRegisterController@getArticle')->name('dashboard.logistics.stock_log_register.get_article');
	Route::post('/logistica/registro-movimiento-existencias/guardar', 'StockLogRegisterController@store')->name('dashboard.logistics.stock_log_register.store');

	/** Logística > Registro Movimiento de Existencias 2 */
	Route::get('/logistica/registro-movimiento-existencias-prueba', 'StockRegisterBetaController@index')->name('dashboard.logistics.stock_register_beta');
	Route::post('/logistica/registro-movimiento-existencias/validar-formulario-prueba', 'StockRegisterBetaController@validateForm')->name('dashboard.logistics.stock_register_beta.validate_form');
	Route::post('/logistica/registro-movimiento-existencias/listar-prueba', 'StockRegisterBetaController@list')->name('dashboard.logistics.stock_register_beta.list');
	Route::post('/logistica/registro-movimiento-existencias/listar-cuentas-prueba', 'StockRegisterBetaController@getAccounts')->name('dashboard.logistics.stock_register_beta.get_accounts');
	Route::post('/logistica/registro-movimiento-existencias/listar-articulos-prueba', 'StockRegisterBetaController@getArticles')->name('dashboard.logistics.stock_register_beta.get_articles');
	Route::post('/logistica/registro-movimiento-existencias/obtener-percepcion-prueba', 'StockRegisterBetaController@getPerceptionPercentage')->name('dashboard.logistics.stock_register_beta.get_perception_percentage');
	Route::post('/logistica/registro-movimiento-existencias/obtener-tasas-prueba', 'StockRegisterBetaController@getArticleRates')->name('dashboard.logistics.stock_register_beta.get_article_rates');
	Route::post('/logistica/registro-movimiento-existencias/obtener-articulo-prueba', 'StockRegisterBetaController@getArticle')->name('dashboard.logistics.stock_register_beta.get_article');
	Route::post('/logistica/registro-movimiento-existencias/guardar-prueba', 'StockRegisterBetaController@store')->name('dashboard.logistics.stock_register_beta.store');
	Route::get('/logistica/registro-movimiento-existencias/obtener-siguiente-correlativo-prueba', 'StockRegisterBetaController@getNextcorrelative')->name('dashboard.logistics.stock_register_beta.next_correlative');

	/** Logística > Inventario */
	Route::get('/logistica/inventario', 'InventoryController@index')->name('dashboard.logistics.inventories');
	Route::post('/logistica/inventario/validar-formulario', 'InventoryController@validateForm')->name('dashboard.logistics.inventories.validate_form');
	Route::post('/logistica/inventario/listar', 'InventoryController@list')->name('dashboard.logistics.inventories.list');
	Route::post('/logistica/inventario/obtener-articulos', 'InventoryController@getArticles')->name('dashboard.logistics.inventories.get_articles');
	Route::post('/logistica/inventario/crear-registro', 'InventoryController@createRecord')->name('dashboard.logistics.inventories.create_record');
	Route::post('/logistica/inventario/cerrar-registro', 'InventoryController@closeRecord')->name('dashboard.logistics.inventories.close_record');
	Route::post('/logistica/inventario/detalle', 'InventoryController@detail')->name('dashboard.logistics.inventories.detail');
	Route::post('/logistica/inventario/obtener-select2', 'InventoryController@getSelect2')->name('dashboard.logistics.inventories.get_select2');
	Route::post('/logistica/inventario/guardar', 'InventoryController@store')->name('dashboard.logistics.inventories.store');
	Route::post('/logistica/inventario/eliminar', 'InventoryController@delete')->name('dashboard.logistics.inventories.delete');
	Route::post('/logistica/inventario/formulario-registro', 'InventoryController@formRecord')->name('dashboard.logistics.inventories.form_record');
	Route::post('/logistica/inventario/exportar-registro', 'InventoryController@exportRecord')->name('dashboard.logistics.inventories.export_record');

	/** Logística > Inventario General */
	Route::get('/planta/inventario', 'AdjustInventoryController@index')->name('dashboard.operations.inventories');
	Route::post('/planta/inventario/validar-formulario', 'AdjustInventoryController@validateForm')->name('dashboard.operations.inventories.validate_form');
	Route::post('/planta/inventario/listar', 'AdjustInventoryController@list')->name('dashboard.operations.inventories.list');
	Route::post('/planta/inventario/obtener-articulos', 'AdjustInventoryController@getArticles')->name('dashboard.operations.inventories.get_articles');
	Route::post('/planta/inventario/crear-registro', 'AdjustInventoryController@createRecord')->name('dashboard.operations.inventories.create_record');
	Route::post('/planta/inventario/cerrar-registro', 'AdjustInventoryController@closeRecord')->name('dashboard.operations.inventories.close_record');
	Route::post('/planta/inventario/detalle', 'AdjustInventoryController@detail')->name('dashboard.operations.inventories.detail');
	Route::post('/planta/inventario/obtener-select2', 'AdjustInventoryController@getSelect2')->name('dashboard.operations.inventories.get_select2');
	Route::post('/planta/inventario/guardar', 'AdjustInventoryController@store')->name('dashboard.operations.inventories.store');
	Route::post('/planta/inventario/eliminar', 'AdjustInventoryController@delete')->name('dashboard.operations.inventories.delete');
	Route::post('/planta/inventario/formulario-registro', 'AdjustInventoryController@formRecord')->name('dashboard.operations.inventories.form_record');
	Route::post('/planta/inventario/exportar-registro', 'AdjustInventoryController@exportRecord')->name('dashboard.operations.inventories.export_record');
	Route::post('/planta/inventario/listar-almacen', 'AdjustInventoryController@getWarehouseTypes')->name('dashboard.operations.inventories.get_warehouse_types');
	Route::post('/planta/inventario/ver-articulos', 'AdjustInventoryController@getArticlesWarehouseType')->name('dashboard.operations.inventories.view_articles');

	/** Logística > Artículos */
	Route::get('/articulos', 'BienesRegisterController@index')->name('dashboard.articles_register');
	Route::post('/articulos/validar-formulario', 'BienesRegisterController@validateForm')->name('dashboard.articles_register.validate_form');
	Route::post('/articulos/listar', 'BienesRegisterController@list')->name('dashboard.articles_register.list');
	Route::post('/articulos/guardar', 'BienesRegisterController@store')->name('dashboard.articles_register.store');
	Route::post('/articulos/detalle', 'BienesRegisterController@detail')->name('dashboard.articles_register.detail');
	Route::post('/articulos/eliminar', 'BienesRegisterController@delete')->name('dashboard.articles_register.delete');
	Route::post('/articulos/exportar', 'BienesRegisterController@exportRecord')->name('dashboard.articles_register.export_record');
	Route::post('/articulos/listar-sedes', 'BienesRegisterController@getSede')->name('dashboard.articles_register.list_sede');
	Route::post('/articulos/listar-almacenes', 'BienesRegisterController@getWarehouseType')->name('dashboard.articles_register.list_almacen');

	/** Logística > Clasificaciones */
	Route::get('/clasificaciones', 'ClassificationController@index')->name('dashboard.classifications');
	Route::post('/clasificaciones/validar-formulario', 'ClassificationController@validateForm')->name('dashboard.classifications.validate_form');
	Route::post('/clasificaciones/listar', 'ClassificationController@list')->name('dashboard.classifications.list');
	Route::post('/clasificaciones/guardar', 'ClassificationController@store')->name('dashboard.classifications.store');
	Route::post('/clasificaciones/detalle', 'ClassificationController@detail')->name('dashboard.classifications.detail');
	Route::post('/clasificaciones/eliminar', 'ClassificationController@delete')->name('dashboard.classifications.delete');

	/** Logística > Proveedores */
	Route::get('/proveedores', 'ProviderController@index')->name('dashboard.providers');
	Route::post('/proveedores/validar-formulario', 'ProviderController@validateForm')->name('dashboard.providers.validate_form');
	Route::post('/proveedores/listar', 'ProviderController@list')->name('dashboard.providers.list');
	Route::post('/proveedores/guardar', 'ProviderController@store')->name('dashboard.providers.store');
	Route::post('/proveedores/detalle', 'ProviderController@detail')->name('dashboard.providers.detail');
	Route::post('/proveedores/obtener-ubigeos', 'ProviderController@getUbigeos')->name('dashboard.providers.get_ubigeos');
	Route::post('/proveedores/obtener-ubigeo', 'ProviderController@getUbigeo')->name('dashboard.providers.get_ubigeo');
	Route::post('/proveedores/eliminar', 'ProviderController@delete')->name('dashboard.providers.delete');
	Route::get('/logistica/inventario/test', 'InventoryController@test')->name('dashboard.logistics.inventories.test');
	Route::get('/clientes/store/{id}', 'ClientController@store_tmp')->name('dashboard.client.store_tmp');
	Route::get('/clientes/update/{id}', 'ClientController@update')->name('dashboard.client.update');
	Route::get('/clientes/update-address/{id}', 'ClientController@update_address')->name('dashboard.client.update_address');
	Route::get('/boletas-backup', 'BackupController@boletaBackup');


	/** Logística > Orden de Trabajo */
	Route::get('/logistica/registro-orden-trabajo', 'OrderWorkRegisterController@index')->name('dashboard.logistics.ot_register');
	Route::post('/logistica/registro-orden-trabajo/listar-vehiculos', 'OrderWorkRegisterController@getVehicles')->name('dashboard.logistics.ot_list_vehicles');
	Route::post('/logistica/registro-orden-trabajo/listar-detalle', 'OrderWorkRegisterController@list')->name('dashboard.logistics.ot_list_detalle');
	Route::post('/logistica/registro-orden-trabajo/listar-articulos-modal', 'OrderWorkRegisterController@listArticles')->name('dashboard.logistics.ot_articles');
	Route::post('/logistica/registro-orden-trabajo/listar-articulos', 'OrderWorkRegisterController@getArticle')->name('dashboard.logistics.ot_list_articles');
	Route::post('/logistica/registro-orden-trabajo/validar-modal', 'OrderWorkRegisterController@validateModalForm')->name('dashboard.logistics.ot_validate_modal');
	Route::post('/logistica/registro-orden-trabajo/registrar-cotizacion', 'OrderWorkRegisterController@store')->name('dashboard.logistics.ot_register_final');
	Route::get('/logistica/registro-orden-trabajo/mostrar-correlativo', 'OrderWorkRegisterController@getNextcorrelative')->name('dashboard.logistics.ot_correlative');
	Route::get('/logistica/registro-orden-trabajo/mostrar-cliente', 'OrderWorkRegisterController@getClient')->name('dashboard.logistics.ot_client');
	Route::post('/logistica/registro-orden-trabajo/listar-series', 'OrderWorkRegisterController@getSeries')->name('dashboard.logistics.ot_series');

	/** Logística > Reporte de Orden de Trabajo */
	Route::get('/logistica-reporte-orden-trabajo', 'OrderWorkReportController@index')->name('dashboard.order_work');
	Route::get('/logistica-reporte-orden-trabajo/imprimir-pdf', 'OrderWorkReportController@formato')->name('dashboard.order_work_pdf');
	Route::post('/logistica-reporte-orden-trabajo/listar-vehiculos', 'OrderWorkReportController@list')->name('dashboard.order_work.list');
	Route::post('/logistica-reporte-orden-trabajo/desaprobar-ot', 'OrderWorkReportController@delete')->name('dashboard.order_work.delete_asign');
	Route::post('/logistica-reporte-orden-trabajo/aprobar-ot', 'OrderWorkReportController@aprobar')->name('dashboard.order_work.aprobar_asign');
	Route::post('/logistica-reporte-orden-trabajo/listar-clientes-pm', 'OrderWorkReportController@getClients')->name('dashboard.order_work.list_clients');
	Route::post('/logistica-reporte-orden-trabajo/asignar-cliente-pm', 'OrderWorkReportController@assignClient')->name('dashboard.order_work.asignar');
	Route::post('/logistica-reporte-orden-trabajo/ver-asignacion', 'OrderWorkReportController@view_asign')->name('dashboard.order_work.view_asign');

	/** Logística > Cotización */
	Route::get('/logistica/registro-cotizacion', 'CotizacionRegisterController@index')->name('dashboard.logistics.cotizacion_register');
	Route::post('/logistica/registro-cotizacion/listar-vehiculos', 'CotizacionRegisterController@getVehicles')->name('dashboard.logistics.cotizacion_list_vehicles');
	Route::post('/logistica/registro-cotizacion/listar-clientes', 'CotizacionRegisterController@getClients')->name('dashboard.logistics.cotizacion_list_clients');
	Route::post('/logistica/registro-cotizacion/listar-detalle', 'CotizacionRegisterController@list')->name('dashboard.logistics.cotizacion_list_detalle');
	Route::post('/logistica/registro-cotizacion/listar-articulos-modal', 'CotizacionRegisterController@listArticles')->name('dashboard.logistics.cotizacion_articles');
	Route::post('/logistica/registro-cotizacion/listar-articulos', 'CotizacionRegisterController@getArticle')->name('dashboard.logistics.cotizacion_list_articles');
	Route::post('/logistica/registro-cotizacion/validar-modal', 'CotizacionRegisterController@validateModalForm')->name('dashboard.logistics.cotizacion_validate_modal');
	Route::post('/logistica/registro-cotizacion/registrar-cotizacion', 'CotizacionRegisterController@store')->name('dashboard.logistics.cotizacion_register_final');
	Route::get('/logistica/registro-cotizacion/mostrar-cliente', 'CotizacionRegisterController@getClient')->name('dashboard.logistics.cotizacion_client');

	/** Logística > Cotización X facturar */
	Route::get('/logistica/facturar-cotizacion', 'CotizacionReportController@index')->name('dashboard.logistics.cotizacion_report');
	Route::post('/logistica/facturar-validar-form', 'CotizacionReportController@validateForm')->name('dashboard.logistics.cotizacion_validar_form');
	Route::post('/logistica/facturar-cotizacion/listar', 'CotizacionReportController@list')->name('dashboard.logistics.cotizacion_report_list');
	Route::post('/logistica/facturar-cotizacion/aprobar', 'CotizacionReportController@aprobarCotizacion')->name('dashboard.logistics.cotizacion_report_aprob');
	Route::post('/logistica/facturar-cotizacion/crear-factura', 'CotizacionReportController@generarFactura')->name('dashboard.logistics.cotizacion_report_fact_create');
	Route::post('/logistica/facturar-cotizacion/exportar-factura', 'CotizacionReportController@exportFactura')->name('dashboard.logistics.cotizacion_report_fact');
	Route::post('/logistica/facturar-cotizacion/exportar-cotizacion', 'CotizacionReportController@exportCotizacion')->name('dashboard.logistics.cotizacion_report_cot');
	Route::post('/logistica/facturar-cotizacion/detalle', 'CotizacionReportController@detail')->name('dashboard.logistics.cotizacion_report_detail');
	Route::post('/logistica/facturar-cotizacion/detalle-articulo', 'CotizacionReportController@getDetailArticle')->name('dashboard.logistics.cotizacion_report_detail_article');
	Route::post('/logistica/facturar-cotizacion/actualizar-articulo', 'CotizacionReportController@storeArticle')->name('dashboard.logistics.cotizacion_report_update_article');
	Route::post('/logistica/facturar-cotizacion/detalle-eliminar', 'CotizacionReportController@deleteDetailArticle')->name('dashboard.logistics.cotizacion_report_delete_article');

	/** Operaciones > Guias */
	Route::get('/operaciones/registro-movimiento-existencias', 'GuidesRegisterController@index')->name('dashboard.operations.guides_register');
	Route::post('/operaciones/registro-movimiento-existencias/validar-formulario', 'GuidesRegisterController@validateForm')->name('dashboard.operations.guides_register.validate_form');
	Route::post('/operaciones/registro-movimiento-existencias/listar', 'GuidesRegisterController@list')->name('dashboard.operations.guides_register.list');
	Route::post('/operaciones/registro-movimiento-existencias/listar-cuentas', 'GuidesRegisterController@getAccounts')->name('dashboard.operations.guides_register.get_accounts');
	Route::post('/operaciones/registro-movimiento-existencias/listar-articulos', 'GuidesRegisterController@getArticles')->name('dashboard.operations.guides_register.get_articles');
	Route::post('/operaciones/registro-movimiento-existencias/obtener-percepcion', 'GuidesRegisterController@getPerceptionPercentage')->name('dashboard.operations.guides_register.get_perception_percentage');
	Route::post('/operaciones/registro-movimiento-existencias/obtener-tasas', 'GuidesRegisterController@getArticleRates')->name('dashboard.operations.guides_register.get_article_rates');
	Route::post('/operaciones/registro-movimiento-existencias/obtener-articulo', 'GuidesRegisterController@getArticle')->name('dashboard.operations.guides_register.get_article');
	Route::post('/operaciones/registro-movimiento-existencias/guardar', 'GuidesRegisterController@store')->name('dashboard.operations.guides_register.store');
	Route::get('/operaciones/registro-movimiento-existencias/obtener-siguiente-correlativo', 'GuidesRegisterController@getNextcorrelative')->name('dashboard.operations.guides_register.next_correlative');
	Route::post('/operaciones/registro-movimiento-existencias/listar-ubigeos', 'GuidesRegisterController@getUbigeos')->name('dashboard.operations.guides_register.get_ubigeos');
	Route::post('/operaciones/registro-movimiento-existencias/listar-empleados', 'GuidesRegisterController@getEmployees')->name('dashboard.operations.guides_register.get_employees');
	Route::post('/operaciones/registro-movimiento-existencias/listar-series', 'GuidesRegisterController@getElectSeries')->name('dashboard.operations.guides_register.get_series');
    Route::post('/operaciones/registro-movimiento-existencias/obtener-ruta', 'GuidesRegisterController@getRoute')->name('dashboard.operations.guides_register.get_ubigeo_ruta');

	/** Control GLP > Guias electronicas */
	Route::get('/operaciones/guias-electronicas', 'GuiasElectronicReportController@index')->name('dashboard.guias_electronic.report');
	Route::post('/operaciones/guias-electronicas-validar-form', 'GuiasElectronicReportController@validateForm')->name('dashboard.guias_electronic.validar_form');
	Route::post('/operaciones/guias-electronicas-listar', 'GuiasElectronicReportController@list')->name('dashboard.guias_electronic.list');
	Route::post('/operaciones/guias-electronicas-descargar-pdf', 'GuiasElectronicReportController@generarPdf')->name('dashboard.guias_electronic.descargar');
	Route::post('/operaciones/guias-electronicas-detalle', 'GuiasElectronicReportController@detail')->name('dashboard.guias_electronic.detalle');

	/** Control GLP > Impresión de Guías Chofer Preventa */
	Route::get('/operaciones/guias-preventa', 'PreventaReportController@index')->name('dashboard.preventa.report');
	Route::post('/operaciones/guias-preventa-listar', 'PreventaReportController@list')->name('dashboard.preventa.list');
	Route::post('/operaciones/guias-preventa-descargar-pdf', 'PreventaReportController@generarPdf')->name('dashboard.preventa.descargar');

	/** Control GLP > Almacenes */
	Route::get('/almacen', 'StoreController@index')->name('dashboard.stores');
	Route::post('/almacen/validar-formulario', 'StoreController@validateForm')->name('dashboard.stores.validate_form');
	Route::post('/almacen/listar', 'StoreController@list')->name('dashboard.stores.list');
	Route::post('/almacen/guardar', 'StoreController@store')->name('dashboard.stores.store');
	Route::post('/almacen/detalle', 'StoreController@detail')->name('dashboard.stores.detail');
	Route::post('/almacen/eliminar', 'StoreController@delete')->name('dashboard.stores.delete');


	/** Control GLP > Edición de Guias */
	Route::get('/guias-edit', 'GuideEditController@index')->name('dashboard.guide_edit');
	Route::post('/guias-edit/listar-guias', 'GuideEditController@list')->name('dashboard.guide_edit.list');
	Route::post('/guias-edit/detalle', 'GuideEditController@detail')->name('dashboard.guide_edit.detail');
	Route::post('/guias-edit/actualizar', 'GuideEditController@update')->name('dashboard.guide_edit.update');

	/** Operaciones > Choferes */
	Route::get('/choferes', 'EmployeeController@index')->name('dashboard.employee');
	Route::post('/choferes/crear', 'EmployeeController@store')->name('dashboard.employee.store');
	Route::post('/choferes/listar', 'EmployeeController@list')->name('dashboard.employee.list');
	Route::post('/choferes/eliminar', 'EmployeeController@delete')->name('dashboard.employee.delete');
	Route::post('/choferes/detalle', 'EmployeeController@detail')->name('dashboard.employee.detail');
	Route::post('/choferes/actualizar', 'EmployeeController@update')->name('dashboard.employee.update');
	Route::post('/choferes/exportar', 'EmployeeController@exportExcel')->name('dashboard.employee.export');

	// Rutas de Reportes de Envases/Cilindros
	Route::get('/reports/envases', 'ReportsEnvasesGeneralController@index')->name('dashboard.envases.index');

	Route::get('/reports/envases/stock', 'ReportsEnvasesGeneralController@getStockArticles')->name('dashboard.envases.stock');

	Route::get('/reports/envases/export', 'ReportsEnvasesGeneralController@exportResumenExcel')->name('dashboard.envases.export');


    /** Operaciones > Reporte Despacho Retorno - Danfeer */
		Route::get('/reporte/despacho-retorno', 'ReportCylinderController@index')->name('dashboard.report.cylinder');
		Route::post('/reporte/despacho-retorno/validar', 'ReportCylinderController@validateForm')->name('dashboard.report.cylinder.validate_form');
		Route::post('/reporte/despacho-retorno/listar', 'ReportCylinderController@list')->name('dashboard.report.cylinder.list');


	/** Control de Glp y Cilindros > Placa de Vehículos */
	Route::get('/vehiculos-placa', 'VehiclePlateController@index')->name('dashboard.vehicle_plate');
	Route::post('/vehiculos-placa/crear', 'VehiclePlateController@store')->name('dashboard.vehicle_plate.store');
	Route::post('/vehiculos-placa/listar', 'VehiclePlateController@list')->name('dashboard.vehicle_plate.list');
	Route::post('/vehiculos-placa/eliminar', 'VehiclePlateController@delete')->name('dashboard.vehicle_plate.delete');
	Route::post('/vehiculos-placa/detalle', 'VehiclePlateController@detail')->name('dashboard.vehicle_plate.detail');
	Route::post('/vehiculos-placa/actualizar', 'VehiclePlateController@update')->name('dashboard.vehicle_plate.update');
	Route::post('/vehiculos-placa/exportar', 'VehiclePlateController@exportExcel')->name('dashboard.vehicle_plate.export');

	/** Punto Motor > Vehículos */
	Route::get('/vehiculos', 'VehicleController@index')->name('dashboard.vehicle');
	Route::post('/vehiculos/crear', 'VehicleController@store')->name('dashboard.vehicle.store');
	Route::post('/vehiculos/listar', 'VehicleController@list')->name('dashboard.vehicle.list');
	Route::post('/vehiculos/eliminar', 'VehicleController@delete')->name('dashboard.vehicle.delete');
	Route::post('/vehiculos/detalle', 'VehicleController@detail')->name('dashboard.vehicle.detail');
	Route::post('/vehiculos/actualizar', 'VehicleController@update')->name('dashboard.vehicle.update');
	Route::post('/vehiculos/exportar', 'VehicleController@exportExcel')->name('dashboard.vehicle.export');

	/** Operaciones > Parte de Almacén */
	Route::get('/operaciones/parte-almacen', 'OperationsPartController@index')->name('dashboard.operations.operations_part');
	Route::post('/operaciones/parte-almacen/validar-formulario', 'OperationsPartController@validateForm')->name('dashboard.operations.operations_part.validate_form');
	Route::post('/operaciones/parte-almacen/obtener-movimientos-almacen', 'OperationsPartController@getWarehouseMovements')->name('dashboard.operations.operations_part.get_warehouse_movements');
	Route::post('/operaciones/parte-almacen/listar', 'OperationsPartController@list')->name('dashboard.operations.operations_part.list');
	Route::post('/operaciones/parte-almacen/exportar', 'OperationsPartController@export')->name('dashboard.operations.operations_part.export');
	Route::get('/operaciones/parte-almacen/exportar-guia-remision', 'OperationsPartController@exportReferralGuide')->name('dashboard.operations.operations_part.export_referral_guide');

	/** Operaciones > Retorno de Guías */
	Route::get('/operaciones/retorno-guias', 'GuidesReturnController@index')->name('dashboard.operations.guides_return');
	Route::post('/operaciones/retorno-guias/validar-formulario', 'GuidesReturnController@validateForm')->name('dashboard.operations.guides_return.validate_form');
	Route::post('/operaciones/retorno-guias/obtener-movimientos-almacen', 'GuidesReturnController@getWarehouseMovements')->name('dashboard.operations.guides_return.get_warehouse_movements');
	Route::post('/operaciones/retorno-guias/listar', 'GuidesReturnController@list')->name('dashboard.operations.guides_return.list');
	Route::post('/operaciones/retorno-guias/actualizar', 'GuidesReturnController@update')->name('dashboard.operations.guides_return.update');

	/** Comercial > Estado de Guías */
	Route::get('/comercial/estado-guias', 'GuidesCommercialReportController@index')->name('dashboard.commercial.guides_commercial');
	Route::post('/comercial/estado-guias/validar-formulario', 'GuidesCommercialReportController@validateForm')->name('dashboard.commercial.guides_commercial.validate_form');
	Route::post('/comercial/estado-guias/obtener-clientes', 'GuidesCommercialReportController@getClients')->name('dashboard.commercial.guides_commercial.get_warehouse_movements');
	Route::post('/comercial/estado-guias/listar', 'GuidesCommercialReportController@list')->name('dashboard.commercial.guides_commercial.list');
	Route::post('/comercial/estado-guias/detalle', 'GuidesCommercialReportController@detail')->name('dashboard.commercial.guides_commercial.detail');
	Route::post('/comercial/estado-guias/actualizar', 'GuidesCommercialReportController@update')->name('dashboard.commercial.guides_commercial.update');

  /** comercial > Clientes */
  Route::get('/comercial/clientes', 'ClientController@index')->name('dashboard.voucher.clients');
  Route::post('/comercial/clientes/validar-formulario', 'ClientController@validateForm')->name('dashboard.voucher.clients.validate_form');
  Route::post('/comercial/clientes/listar', 'ClientController@list')->name('dashboard.voucher.clients.list');
  Route::post('/comercial/clientes/guardar', 'ClientController@store')->name('dashboard.voucher.clients.store');
  Route::post('/comercial/clientes/detalle', 'ClientController@detail')->name('dashboard.voucher.clients.detail');
  Route::post('/comercial/clientes/obtener-ubigeos', 'ClientController@getUbigeos')->name('dashboard.voucher.clients.get_ubigeos');
  Route::post('/comercial/clientes/obtener-clientes', 'ClientController@getClients')->name('dashboard.voucher.clients.get_clients');
  Route::post('/comercial/clientes/obtener-select2', 'ClientController@getSelect2')->name('dashboard.voucher.clients.get_select2');
  Route::post('/comercial/clientes/eliminar', 'ClientController@delete')->name('dashboard.voucher.clients.delete');
  Route::post('/comercial/clientes/listar-direcciones', 'ClientController@addressList')->name('dashboard.voucher.clients.address_list');
  Route::post('/comercial/clientes/guardar-direccion', 'ClientController@addressStore')->name('dashboard.voucher.clients.address_store');
  Route::post('/comercial/clientes/detalle-direccion', 'ClientController@addressDetail')->name('dashboard.voucher.clients.address_detail');
  Route::post('/comercial/clientes/eliminar-direccion', 'ClientController@addressDelete')->name('dashboard.voucher.clients.address_delete');
  Route::post('/comercial/clientes/eliminar-precio', 'ClientController@priceDelete')->name('dashboard.voucher.clients.price_delete');
  Route::post('/comercial/clientes/listar-precios', 'ClientController@priceList')->name('dashboard.voucher.clients.price_list');
  Route::post('/comercial/clientes/listar-articulos', 'ClientController@priceArticles')->name('dashboard.voucher.clients.price_articles');
  Route::post('/comercial/clientes/obtener-dia', 'ClientController@priceMinEffectiveDate')->name('dashboard.voucher.clients.price_min_effective_date');
  Route::post('/comercial/clientes/guardar-precio', 'ClientController@priceStore')->name('dashboard.voucher.clients.price_store');
  Route::post('/comercial/clientes/obtener-sectores', 'ClientController@getClientSectors')->name('dashboard.voucher.clients.get_client_sectors');
  Route::post('/comercial/clientes/obtener-canales', 'ClientController@getClientChannels')->name('dashboard.voucher.clients.get_client_channels');
  Route::post('/comercial/clientes/obtener-rutas', 'ClientController@getClientRoutes')->name('dashboard.voucher.clients.get_client_routes');

	/** comercial > Clientes PUNTO MOTOR */
	Route::get('/comercial/clientes-pm', 'ClientPmController@index')->name('dashboard.voucher.clients-pm');
	Route::post('/comercial/clientes-pm/validar-formulario', 'ClientPmController@validateForm')->name('dashboard.voucher.clients-pm.validate_form');
	Route::post('/comercial/clientes-pm/listar', 'ClientPmController@list')->name('dashboard.voucher.clients-pm.list');
	Route::post('/comercial/clientes-pm/guardar', 'ClientPmController@store')->name('dashboard.voucher.clients-pm.store');
	Route::post('/comercial/clientes-pm/detalle', 'ClientPmController@detail')->name('dashboard.voucher.clients-pm.detail');
	Route::post('/comercial/clientes-pm/obtener-ubigeos', 'ClientPmController@getUbigeos')->name('dashboard.voucher.clients-pm.get_ubigeos');
	Route::post('/comercial/clientes-pm/obtener-clientes', 'ClientPmController@getClients')->name('dashboard.voucher.clients-pm.get_clients');
	Route::post('/comercial/clientes-pm/obtener-select2', 'ClientPmController@getSelect2')->name('dashboard.voucher.clients-pm.get_select2');
	Route::post('/comercial/clientes-pm/eliminar', 'ClientPmController@delete')->name('dashboard.voucher.clients-pm.delete');
	Route::post('/comercial/clientes-pm/listar-direcciones', 'ClientPmController@addressList')->name('dashboard.voucher.clients-pm.address_list');
	Route::post('/comercial/clientes-pm/guardar-direccion', 'ClientPmController@addressStore')->name('dashboard.voucher.clients-pm.address_store');
	Route::post('/comercial/clientes-pm/detalle-direccion', 'ClientPmController@addressDetail')->name('dashboard.voucher.clients-pm.address_detail');
	Route::post('/comercial/clientes-pm/eliminar-direccion', 'ClientPmController@addressDelete')->name('dashboard.voucher.clients-pm.address_delete');
	Route::post('/comercial/clientes-pm/listar-precios', 'ClientPmController@priceList')->name('dashboard.voucher.clients-pm.price_list');
	Route::post('/comercial/clientes-pm/listar-articulos', 'ClientPmController@priceArticles')->name('dashboard.voucher.clients-pm.price_articles');
	Route::post('/comercial/clientes-pm/obtener-dia', 'ClientPmController@priceMinEffectiveDate')->name('dashboard.voucher.clients-pm.price_min_effective_date');
	Route::post('/comercial/clientes-pm/guardar-precio', 'ClientPmController@priceStore')->name('dashboard.voucher.clients-pm.price_store');

	/** comercial > Asignar vehiculo a Cliente */
	Route::get('/servicio-asignar-vehiculo', 'AssignVehicleController@index')->name('dashboard.assign_vehicle');
	Route::post('/servicio-asignar-vehiculo/listar-vehiculos', 'AssignVehicleController@list')->name('dashboard.assign_vehicle.list');
	Route::post('/servicio-asignar-vehiculo/eliminar-asignacion', 'AssignVehicleController@delete')->name('dashboard.assign_vehicle.delete_asign');
	Route::post('/servicio-asignar-vehiculo/listar-clientes-pm', 'AssignVehicleController@getClients')->name('dashboard.assign_vehicle.list_clients');
	Route::post('/servicio-asignar-vehiculo/asignar-cliente-pm', 'AssignVehicleController@assignClient')->name('dashboard.assign_vehicle.asignar');
	Route::post('/servicio-asignar-vehiculo/ver-asignacion', 'AssignVehicleController@view_asign')->name('dashboard.assign_vehicle.view_asign');

	/** PUNTO MOTOR > LIQUIDACIONES PM */
	Route::get('/facturacion-liquidaciones-pm', 'LiquidationPmController@index')->name('dashboard.liquidation_pm');
	Route::post('/facturacion-liquidaciones-pm/listar-vouchers', 'LiquidationPmController@list')->name('dashboard.liquidation_pm.list');
	Route::post('/facturacion-liquidaciones-pm/listar-metodos-pago', 'LiquidationPmController@getPaymentMethod')->name('dashboard.liquidation_pm.list.method');
	Route::post('/facturacion-liquidaciones-pm/listar-bancos', 'LiquidationPmController@getBankAccount')->name('dashboard.liquidation_pm.list.bank_account');
	Route::post('/facturacion-liquidaciones-pm/ver-detalle', 'LiquidationPmController@getDetail')->name('dashboard.liquidation_pm.detail');
	Route::post('/facturacion-liquidaciones-pm/agregar-liquidacion', 'LiquidationPmController@getLiquidation')->name('dashboard.liquidation_pm.add_liquidation');
	Route::post('/facturacion-liquidaciones-pm/liquidar', 'LiquidationPmController@store')->name('dashboard.liquidation_pm.store');


	/** Comercial > Venta Canal*/
	Route::get('/comercial/venta-diaria', 'CommercialChannelReportController@index')->name('dashboard.commercial.commercial_channel');
	Route::post('/comercial/venta-diaria/validar-formulario', 'CommercialChannelReportController@validateForm')->name('dashboard.commercial.commercial_channel.validate_form');
	Route::post('/comercial/venta-diaria/obtener-clientes', 'CommercialChannelReportController@getClients')->name('dashboard.commercial.commercial_channel.get_clients');
	Route::post('/comercial/venta-diaria/listar', 'CommercialChannelReportController@list')->name('dashboard.commercial.commercial_channel.list');

	/** Reportes > Venta Canal Resumido*/
	Route::get('/comercial/venta-canal-resumido', 'CommercialChannelTotalReportController@index')->name('dashboard.commercial.commercial_channel_total');
	Route::post('/comercial/venta-canal-resumido/validar-formulario', 'CommercialChannelTotalReportController@validateForm')->name('dashboard.commercial.commercial_channel_total.validate_form');
	Route::post('/comercial/venta-canal-resumido/obtener-clientes', 'CommercialChannelTotalReportController@getClients')->name('dashboard.commercial.commercial_channel_total.get_clients');
	Route::post('/comercial/venta-canal-resumido/listar', 'CommercialChannelTotalReportController@list')->name('dashboard.commercial.commercial_channel_total.list');

	/** Reportes > Venta Rutas Resumido*/
	Route::get('/comercial/venta-ruta-resumido', 'LiquidationRutaTotalReportController@index')->name('dashboard.report.liquidations_ruta_total');
	Route::post('/comercial/venta-ruta-resumido/validar-formulario', 'LiquidationRutaTotalReportController@validateForm')->name('dashboard.report.liquidations_ruta_total.validate_form');
	Route::post('/comercial/venta-ruta-resumido/obtener-clientes', 'LiquidationRutaTotalReportController@getClients')->name('dashboard.report.liquidations_ruta_total.get_clients');
	Route::post('/comercial/venta-ruta-resumido/listar', 'LiquidationRutaTotalReportController@list')->name('dashboard.report.liquidations_ruta_total.list');



	/** Reportes > Venta Diaria*/
	Route::get('/reporte/venta-diaria', 'DaysSaleReportController@index')->name('dashboard.report.days_sale');
	Route::post('/reporte/venta-diaria/validar-formulario', 'DaysSaleReportController@validateForm')->name('dashboard.report.days_sale.validate_form');
	Route::post('/reporte/venta-diaria/listar', 'DaysSaleReportController@list')->name('dashboard.report.days_sale.list');

	/** Reportes > Gerencia Diaria*/
	Route::get('/reporte/gerencia-diaria', 'GerenciaReportController@index')->name('dashboard.report.gerencia');
	Route::post('/reporte/gerencia-diaria/validar-formulario', 'GerenciaReportController@validateForm')->name('dashboard.report.gerencia.validate_form');
	Route::post('/reporte/gerencia-diaria/listar', 'GerenciaReportController@list')->name('dashboard.report.gerencia.list');


	/** Reportes > Proyección*/
	Route::get('/reporte/proyeccion-diaria', 'ProyectionReportController@index')->name('dashboard.report.proyection');
	Route::post('/reporte/proyeccion-diaria/validar-formulario', 'ProyectionReportController@validateForm')->name('dashboard.report.proyection.validate_form');
	Route::post('/reporte/proyeccion-diaria/listar', 'ProyectionReportController@list')->name('dashboard.report.proyection.list');

	/** Reportes > Cobertura*/
	Route::get('/reporte/cobertura-diaria', 'CobertReportController@index')->name('dashboard.report.cobert');
	Route::post('/reporte/cobertura-diaria/validar-formulario', 'CobertReportController@validateForm')->name('dashboard.report.cobert.validate_form');
	Route::post('/reporte/cobertura-diaria/listar', 'CobertReportController@list')->name('dashboard.report.cobert.list');

	/** Reportes > Rutas*/
	Route::get('/reporte/rutas-diaria', 'RutasReportController@index')->name('dashboard.report.rutas');
	Route::post('/reporte/rutas-diaria/validar-formulario', 'RutasReportController@validateForm')->name('dashboard.report.rutas.validate_form');
	Route::post('/reporte/rutas-diaria/listar', 'RutasReportController@list')->name('dashboard.report.rutas.list');


	/** Reportes > GLP COSTO*/
	Route::get('/reporte/glp-diaria', 'DaysGlpReportController@index')->name('dashboard.report.days_glp');
	Route::post('/reporte/glp-diaria/validar-formulario', 'DaysGlpReportController@validateForm')->name('dashboard.report.days_glp.validate_form');
	Route::post('/reporte/glp-diaria/listar', 'DaysGlpReportController@list')->name('dashboard.report.days_glp.list');



	/** Creditos y Cobranzas> Reporte Resumido Creditos*/
	Route::get('/creditos-consolidado', 'CreditTotalReportController@index')->name('dashboard.report.credit_total');
	Route::post('/creditos-consolidado/validar-formulario', 'CreditTotalReportController@validateForm')->name('dashboard.report.credit_total.validate_form');
	Route::post('/creditos-consolidado/obtener-clientes', 'CreditTotalReportController@getClients')->name('dashboard.report.credit_total.get_clients');
	Route::post('/creditos-consolidado/listar', 'CreditTotalReportController@list')->name('dashboard.report.credit_total.list');


	/** Creditos y Cobranzas> Liquidaciones */
	Route::get('creditos/reporte/liquidaciones-creditos', 'LiquidationCreditsReportController@index')->name('dashboard.credits.report.liquidations_credits');
	Route::post('creditos/reporte/liquidaciones-creditos/validar-formulario', 'LiquidationCreditsReportController@validateForm')->name('dashboard.credits.report.liquidations_credits.validate_form');
	Route::post('creditos/reporte/liquidaciones-creditos/obtener-clientes', 'LiquidationCreditsReportController@getClients')->name('dashboard.credits.report.liquidations_credits.get_clients');
	Route::post('creditos/reporte/liquidaciones-creditos/listar', 'LiquidationCreditsReportController@list')->name('dashboard.credits.report.liquidations_credits.list');


	/** Creditos y Cobranzas > Relación de Documentos Pendientes Internos*/
	Route::get('/reporte/relacion-de-documentos-pendientes-internos', 'PendingDocumentIntReportController@index')->name('dashboard.report.pending_document_int_report');
	Route::post('/reporte/relacion-de-documentos-pendientes-internos/obtener-clientes', 'PendingDocumentIntReportController@getClients')->name('dashboard.report.pending_document_int_report.get_clients');
	Route::post('/reporte/relacion-de-documentos-pendientes-internos/validar-formulario', 'PendingDocumentIntReportController@validateForm')->name('dashboard.report.pending_document_int_report.validate_form');
	Route::post('/reporte/relacion-de-documentos-pendientes-internos/listar', 'PendingDocumentIntReportController@list')->name('dashboard.report.pending_document_int_report.list');

	/** Creditos y Cobranzas > Reporte de Ingresos de Caja */
	Route::get('/reporte/ingresos', 'ReportOtherCashController@index')->name('dashboard.report.other_cash');
	Route::post('/reporte/ingresos/listar', 'ReportOtherCashController@list')->name('dashboard.report.other_cash.list');
	Route::post('/reporte/ingresos/listar-cliente', 'ReportOtherCashController@getAccounts')->name('dashboard.report.register_other_cash.get_accounts');
	Route::post('/reporte/ingresos/validar', 'ReportOtherCashController@validateForm')->name('dashboard.report.register_other_cash.validate_form');

	/** Reportes > Descuento Personal*/
	Route::get('/creditos/descuento', 'DescuentoReportController@index')->name('dashboard.voucher.descuento');
	Route::post('/creditos/descuento/validar-formulario', 'DescuentoReportController@validateForm')->name('dashboard.voucher.descuento.validate_form');
	Route::post('/creditos/descuento/obtener-clientes', 'DescuentoReportController@getClients')->name('dashboard.voucher.descuento.get_clients');
	Route::post('/creditos/descuento/listar', 'DescuentoReportController@list')->name('dashboard.voucher.descuento.list');

	/** Contabilidad > Generación masiva de Comprobantes */
	Route::get('/contabilidad/generacion-masiva-comprobantes', 'VoucherMassiveGenerationController@index')->name('dashboard.voucher.massive_generation');
	Route::post('/contabilidad/generacion-masiva-comprobantes/validar-formulario', 'VoucherMassiveGenerationController@validateForm')->name('dashboard.voucher.massive_generation.validate_form');
	Route::post('/contabilidad/generacion-masiva-comprobantes/listar', 'VoucherMassiveGenerationController@list')->name('dashboard.voucher.massive_generation.list');
	Route::post('/contabilidad/generacion-masiva-comprobantes/obtener-clientes', 'VoucherMassiveGenerationController@getClients')->name('dashboard.voucher.massive_generation.get_clients');
	Route::post('/contabilidad/generacion-masiva-comprobantes/obtener-articulos', 'VoucherMassiveGenerationController@getArticles')->name('dashboard.voucher.massive_generation.get_articles');
	Route::post('/contabilidad/generacion-masiva-comprobantes/guardar', 'VoucherMassiveGenerationController@store')->name('dashboard.voucher.massive_generation.store');

	/** Contabilidad > Precios*/
	Route::get('/contabilidad/precios', 'AccountingController@index')->name('dashboard.accounting.index');
	Route::post('/contabilidad/precios/crear', 'AccountingController@store')->name('dashboard.accounting.store');
	Route::post('/contabilidad/precios/listar', 'AccountingController@list')->name('dashboard.accounting.list');
	Route::post('/contabilidad/precios/eliminar', 'AccountingController@delete')->name('dashboard.accounting.delete');
	Route::post('/contabilidad/precios/detalle', 'AccountingController@detail')->name('dashboard.accounting.detail');
	Route::post('/contabilidad/precios/exportar', 'AccountingController@export')->name('dashboard.accounting.export');


	/** Contabilidad > Reporte de guias transportistas */
	Route::get('/contabilidad/guias-registro', 'TransportistRegisterReportController@index')->name('dashboard.report.transportist_register');
	Route::post('/contabilidad/guias-registro/validar-formulario', 'TransportistRegisterReportController@validateForm')->name('dashboard.report.transportist_register.validate_form');
	Route::post('/contabilidad/guias-registro/obtener-guias', 'TransportistRegisterReportController@getWarehouseMovements')->name('dashboard.report.transportist_register.get_warehouse_movements');
	Route::post('/contabilidad/guias-registro/listar', 'TransportistRegisterReportController@list')->name('dashboard.report.transportist_register.list');
	Route::post('/contabilidad/guias-registro/detalle', 'TransportistRegisterReportController@detail')->name('dashboard.report.transportist_register.detail');
	Route::post('/contabilidad/guias-registro/actualizar', 'TransportistRegisterReportController@update')->name('dashboard.report.transportist_register.update');

	/** Contabilidad > Facturas emitidas volumen */
	Route::get('/contabilidad/facturas-emitidas-volumen', 'FacturationSalesVolumeReportController@index')->name('dashboard.report.facturations_sales_volume');
	Route::post('/contabilidad/facturas-emitidas-volumen/validar-formulario', 'FacturationSalesVolumeReportController@validateForm')->name('dashboard.report.facturations_sales_volume.validate_form');
	Route::post('/contabilidad/facturas-emitidas-volumen/obtener-clientes', 'FacturationSalesVolumeReportController@getClients')->name('dashboard.report.facturations_sales_volume.get_clients');
	Route::post('/contabilidad/facturas-emitidas-volumen/listar', 'FacturationSalesVolumeReportController@list')->name('dashboard.report.facturations_sales_volume.list');


	/** Contabilidad > Volumen de Ventas */
	Route::get('/contabilidad/volumen-ventas', 'SalesVolumeReportController@index')->name('dashboard.report.sales_volume');
	Route::post('/contabilidad/volumen-ventas/validar-formulario', 'SalesVolumeReportController@validateForm')->name('dashboard.report.sales_volume.validate_form');
	Route::post('/contabilidad/volumen-ventas/listar', 'SalesVolumeReportController@list')->name('dashboard.report.sales_volume.list');
	Route::post('/contabilidad/volumen-ventas/exportar', 'SalesVolumeReportController@export')->name('dashboard.report.sales_volume.export');

	/** Contabilidad > Volumen de Cordia*/
	Route::get('/contabilidad/volumen-cordia', 'CordiaVolumeReportController@index')->name('dashboard.report.cordia_volume');
	Route::post('/contabilidad/volumen-cordia/validar-formulario', 'CordiaVolumeReportController@validateForm')->name('dashboard.report.cordia_volume.validate_form');
	Route::post('/contabilidad/volumen-cordia/listar', 'CordiaVolumeReportController@list')->name('dashboard.report.cordia_volume.list');
	Route::post('/contabilidad/volumen-cordia/exportar', 'CordiaVolumeReportController@export')->name('dashboard.report.cordia_volume.export');


	/** Contabilidad > Facturas volumen */
	Route::get('/contabilidad/facturas-emitidas-volumen-resumido', 'FacturationTotalSalesVolumeReportController@index')->name('dashboard.report.facturations_total_sales_volume');
	Route::post('/contabilidad/facturas-emitidas-volumen-resumido/validar-formulario', 'FacturationTotalSalesVolumeReportController@validateForm')->name('dashboard.report.facturations_total_sales_volume.validate_form');
	Route::post('/contabilidad/facturas-emitidas-volumen-resumido/obtener-clientes', 'FacturationTotalSalesVolumeReportController@getClients')->name('dashboard.report.facturations_total_sales_volume.get_clients');
	Route::post('/contabilidad/facturas-emitidas-volumen-resumido/listar', 'FacturationTotalSalesVolumeReportController@list')->name('dashboard.report.facturations_total_sales_volume.list');


	/** Contabilidad > Reporte Resumido Ventas*/
	Route::get('/contabilidad/ventas-consolidado', 'ContabilidadDetailTotalReportController@index')->name('dashboard.report.contabilidad_detail_total');
	Route::post('/contabilidad/ventas-consolidado/validar-formulario', 'ContabilidadDetailTotalReportController@validateForm')->name('dashboard.report.contabilidad_detail_total.validate_form');
	Route::post('/contabilidad/ventas-consolidado/obtener-clientes', 'ContabilidadDetailTotalReportController@getClients')->name('dashboard.report.contabilidad_detail_total.get_clients');
	Route::post('/contabilidad/ventas-consolidado/listar', 'ContabilidadDetailTotalReportController@list')->name('dashboard.report.contabilidad_detail_total.list');

	/** Contabilidad > Edicion de Boleteo */
	Route::get('/boleteo', 'BoleteoController@index')->name('dashboard.boleteo');
	Route::post('/boleteo/validar-formulario', 'BoleteoController@validateForm')->name('dashboard.boleteo.validate_form');
	Route::post('/boleteo/listar', 'BoleteoController@list')->name('dashboard.boleteo.list');
	Route::post('/boleteo/guardar', 'BoleteoController@store')->name('dashboard.boleteo.store');

	/** Compras GLP > Compras GLP */
	Route::get('/controlglp/registro-movimiento-existencias', 'GuidesGlpRegisterController@index')->name('dashboard.operations.guides_glp_register');
	Route::post('/controlglp/registro-movimiento-existencias/validar-formulario', 'GuidesGlpRegisterController@validateForm')->name('dashboard.operations.guides_glp_register.validate_form');
	Route::post('/controlglp/registro-movimiento-existencias/listar', 'GuidesGlpRegisterController@list')->name('dashboard.operations.guides_glp_register.list');
	Route::post('/controlglp/registro-movimiento-existencias/listar-cuentas', 'GuidesGlpRegisterController@getAccounts')->name('dashboard.operations.guides_glp_register.get_accounts');
	Route::post('/controlglp/registro-movimiento-existencias/listar-articulos', 'GuidesGlpRegisterController@getArticles')->name('dashboard.operations.guides_glp_register.get_articles');
	Route::post('/controlglp/registro-movimiento-existencias/obtener-percepcion', 'GuidesGlpRegisterController@getPerceptionPercentage')->name('dashboard.operations.guides_glp_register.get_perception_percentage');
	Route::post('/controlglp/registro-movimiento-existencias/obtener-tasas', 'GuidesGlpRegisterController@getArticleRates')->name('dashboard.operations.guides_glp_register.get_article_rates');
	Route::post('/controlglp/registro-movimiento-existencias/obtener-articulo', 'GuidesGlpRegisterController@getArticle')->name('dashboard.operations.guides_glp_register.get_article');
	Route::post('/controlglp/registro-movimiento-existencias/guardar', 'GuidesGlpRegisterController@store')->name('dashboard.operations.guides_glp_register.store');


	/** Compras GLP > Registro de Abastecimiento*/
	Route::get('/controlglp/registro-movimiento-glp', 'StockGlpRegisterController@index')->name('dashboard.operations.stock_glp_register');
	Route::post('/controlglp/registro-movimiento-glp/validar-formulario', 'StockGlpRegisterController@validateForm')->name('dashboard.operations.stock_glp_register.validate_form');
	Route::post('/controlglp/registro-movimiento-glp/listar', 'StockGlpRegisterController@list')->name('dashboard.operations.stock_glp_register.list');
	Route::post('/controlglp/registro-movimiento-glp/listar-cuentas', 'StockGlpRegisterController@getAccounts')->name('dashboard.operations.stock_glp_register.get_accounts');
	Route::post('/controlglp/registro-movimiento-glp/listar-articulos', 'StockGlpRegisterController@getArticles')->name('dashboard.operations.stock_glp_register.get_articles');
	Route::post('/controlglp/registro-movimiento-glp/obtener-percepcion', 'StockGlpRegisterController@getPerceptionPercentage')->name('dashboard.operations.stock_glp_register.get_perception_percentage');
	Route::post('/controlglp/registro-movimiento-glp/obtener-tasas', 'StockGlpRegisterController@getArticleRates')->name('dashboard.operations.stock_glp_register.get_article_rates');
	Route::post('/controlglp/registro-movimiento-glp/obtener-articulo', 'StockGlpRegisterController@getArticle')->name('dashboard.operations.stock_glp_register.get_article');
	Route::post('/controlglp/registro-movimiento-glp/guardar', 'StockGlpRegisterController@store')->name('dashboard.operations.stock_glp_register.store');

	/** Operaciones GLP > Registro de Abastecimiento Planta*/
	Route::get('/controlglp/registro-planta', 'StockPlantRegisterController@index')->name('dashboard.operations.stock_plant_register');
	Route::post('/controlglp/registro-planta/validar-formulario', 'StockPlantRegisterController@validateForm')->name('dashboard.operations.stock_plant_register.validate_form');
	Route::post('/controlglp/registro-planta/listar', 'StockPlantRegisterController@list')->name('dashboard.operations.stock_plant_register.list');
	Route::post('/controlglp/registro-planta/listar-cuentas', 'StockPlantRegisterController@getAccounts')->name('dashboard.operations.stock_plant_register.get_accounts');
	Route::post('/controlglp/registro-planta/listar-articulos', 'StockPlantRegisterController@getArticles')->name('dashboard.operations.stock_plant_register.get_articles');
	Route::post('/controlglp/registro-planta/obtener-percepcion', 'StockPlantRegisterController@getPerceptionPercentage')->name('dashboard.operations.stock_plant_register.get_perception_percentage');
	Route::post('/controlglp/registro-planta/obtener-tasas', 'StockPlantRegisterController@getArticleRates')->name('dashboard.operations.stock_plant_register.get_article_rates');
	Route::post('/controlglp/registro-planta/obtener-articulo', 'StockPlantRegisterController@getArticle')->name('dashboard.operations.stock_plant_register.get_article');
	Route::post('/controlglp/registro-planta/guardar', 'StockPlantRegisterController@store')->name('dashboard.operations.stock_plant_register.store');

	/** Operaciones > Reporte DJ Mensual Osinergmin */
	Route::get('/reporte/osinergmin', 'ReportOsinergminController@index')->name('dashboard.report.osinergmin');
	Route::post('/reporte/osinergmin/validar', 'ReportOsinergminController@validateForm')->name('dashboard.report.osinergmin.validate_form');
	Route::post('/reporte/osinergmin/listar', 'ReportOsinergminController@list')->name('dashboard.report.osinergmin.list');

	/** Compras GLP > Reporte  de Compras GLP */
	Route::get('/controlglp/compras-registro', 'StockSalesRegisterReportController@index')->name('dashboard.report.stock_sales_register');
	Route::post('/controlglp/compras-registro/validar-formulario', 'StockSalesRegisterReportController@validateForm')->name('dashboard.report.stock_sales_register.validate_form');
	Route::post('/controlglp/compras-registro/obtener-guias', 'StockSalesRegisterReportController@getWarehouseMovements')->name('dashboard.report.stock_sales_register.get_warehouse_movements');
	Route::post('/controlglp/compras-registro/listar', 'StockSalesRegisterReportController@list')->name('dashboard.report.stock_sales_register.list');
	Route::post('/controlglp/compras-registro/detalle', 'StockSalesRegisterReportController@detail')->name('dashboard.report.stock_sales_register.detail');
	Route::post('/controlglp/compras-registro/actualizar', 'StockSalesRegisterReportController@update')->name('dashboard.report.stock_sales_register.update');




	/** Compras GLP > Reporte Reusmido de Compras GLP */
	Route::get('/controlglp/compras-resumen', 'StockSalesResumenReportController@index')->name('dashboard.report.stock_sales_resumen');
	Route::post('/controlglp/compras-resumen/validar-formulario', 'StockSalesResumenReportController@validateForm')->name('dashboard.report.stock_sales_resumen.validate_form');
	Route::post('/controlglp/compras-resumen/obtener-guias', 'StockSalesResumenReportController@getWarehouseMovements')->name('dashboard.report.stock_sales_resumen.get_warehouse_movements');
	Route::post('/controlglp/compras-resumen/listar', 'StockSalesResumenReportController@list')->name('dashboard.report.stock_sales_resumen.list');
	Route::post('/controlglp/compras-resumen/detalle', 'StockSalesResumenReportController@detail')->name('dashboard.report.stock_sales_resumen.detail');
	Route::post('/controlglp/compras-resumen/actualizar', 'StockSalesResumenReportController@update')->name('dashboard.report.stock_sales_resumen.update');


	/** Compras GLP > Edición de Facturas */
	Route::get('/control-glp/editor', 'GlpFactReportController@index')->name('dashboard.report.glp_fact');
	Route::post('/control-glp/editor/validar-formulario', 'GlpFactReportController@validateForm')->name('dashboard.report.glp_fact.validate_form');
	Route::post('/control-glp/editor/obtener-guias', 'GlpFactReportController@getWarehouseMovements')->name('dashboard.report.glp_fact.get_warehouse_movements');
	Route::post('/control-glp/editor/listar', 'GlpFactReportController@list')->name('dashboard.report.glp_fact.list');
	Route::post('/control-glp/editor/detalle', 'GlpFactReportController@detail')->name('dashboard.report.glp_fact.detail');
	Route::post('/control-glp/editor/actualizar', 'GlpFactReportController@update')->name('dashboard.report.glp_fact.update');

	/** Compras GLP > Reporte de Abastecimiento */
	Route::get('/controlglp/movimiento-abastecimiento', 'TerminalsReportController@index')->name('dashboard.report.terminals');
	Route::post('/controlglp/movimiento-abastecimiento/validar-formulario', 'TerminalsReportController@validateForm')->name('dashboard.report.terminals.validate_form');
	Route::post('/controlglp/movimiento-abastecimiento/listar', 'TerminalsReportController@list')->name('dashboard.report.terminals.list');
	Route::post('/controlglp/movimiento-abastecimiento/detalle', 'TerminalsReportController@detail')->name('dashboard.report.terminals.detail');
	Route::post('/controlglp/movimiento-abastecimiento/eliminar', 'TerminalsReportController@delete')->name('dashboard.report.terminals.delete');
	Route::post('/controlglp/movimiento-abastecimiento/actualizar', 'TerminalsReportController@update')->name('dashboard.report.terminals.update');

    /** Compras GLP > Reporte de Abastecimiento con Particiones */
	Route::get('/reporte/abastecimientos-partidos', 'ReportPartitionController@index')->name('dashboard.report.partition');
	Route::post('/reporte/abastecimientos-partidos/validar', 'ReportPartitionController@validateForm')->name('dashboard.report.partition.validate_form');
	Route::post('/reporte/abastecimientos-partidos/listar', 'ReportPartitionController@list')->name('dashboard.report.partition.list');

	/** Compras GLP > Registro de Costos */
	Route::get('/controlglp/costo', 'CostGlpRegisterController@index')->name('dashboard.report.cost_glp_register');
	Route::post('/controlglp/costo/validar-formulario', 'CostGlpRegisterController@validateForm')->name('dashboard.report.cost_glp_register.validate_form');
	Route::post('/controlglp/costo/guardar', 'CostGlpRegisterController@store')->name('dashboard.report.cost_glp_register.store');

	/** Compras GLP > Edición de Presupuestos */
	Route::get('/control-budget/editor', 'BudgetReportController@index')->name('dashboard.report.budget');
	Route::post('/control-budget/editor/validar-formulario', 'BudgetReportController@validateForm')->name('dashboard.report.budget.validate_form');
	Route::post('/control-budget/editor/obtener-presupuestos', 'BudgetReportController@getConsReports')->name('dashboard.report.budget.get_cons_reports');
	Route::post('/control-budget/editor/listar', 'BudgetReportController@list')->name('dashboard.report.budget.list');
	Route::post('/control-budget/editor/detalle', 'BudgetReportController@detail')->name('dashboard.report.budget.detail');
	Route::post('/control-budget/editor/actualizar', 'BudgetReportController@update')->name('dashboard.report.budget.update');

	/** Finanzas > Cobranza Resumida*/
	Route::get('/administracion/cobranza-resumido', 'CobranzaDetailTotalReportController@index')->name('dashboard.administration.cobranzas_detail_total');
	Route::post('/administracion/cobranza-resumido/validar-formulario', 'CobranzaDetailTotalReportController@validateForm')->name('dashboard.administration.cobranzas_detail_total.validate_form');
	Route::post('/administracion/cobranza-resumido/obtener-clientes', 'CobranzaDetailTotalReportController@getClients')->name('dashboard.administration.cobranzas_detail_total.get_clients');
	Route::post('/administracion/cobranza-resumido/listar', 'CobranzaDetailTotalReportController@list')->name('dashboard.administration.cobranzas_detail_total.list');


    /** Comercial > REPORTE VENTA Y MARGEN DIARIA*/
	Route::get('/reportes/venta-margen', 'SaleMarginReportController@index')->name('dashboard.sale_margin_report.index');
	Route::post('/reportes/venta-margen/exportar', 'SaleMarginReportController@export')->name('dashboard.sale_margin_report.export');
	Route::post('/reportes/venta-margen/validar-formulario', 'SaleMarginReportController@validateForm')->name('dashboard.sale_margin_report.validate_form');


	/** Finanzas > Reporte Recaudacion de Caja */
	Route::get('/finanzas/reporte-recaudacion-caja', 'RecCajaReportController@index')->name('dashboard.finanzas.report.rec_caja.index');
	Route::post('/finanzas/reporte-recaudacion-caja/exportar', 'RecCajaReportController@export')->name('dashboard.finanzas.report.rec_caja.export');

	/** Reportes > Liquidación Detallado Resumido*/
	Route::get('/administracion/liquidacion-detallado-resumido', 'LiquidationDetailTotalReportController@index')->name('dashboard.report.liquidations_detail_total');
	Route::post('/administracion/liquidacion-detallado-resumido/validar-formulario', 'LiquidationDetailTotalReportController@validateForm')->name('dashboard.report.liquidations_detail_total.validate_form');
	Route::post('/administracion/liquidacion-detallado-resumido/obtener-clientes', 'LiquidationDetailTotalReportController@getClients')->name('dashboard.report.liquidations_detail_total.get_clients');
	Route::post('/administracion/liquidacion-detallado-resumido/listar', 'LiquidationDetailTotalReportController@list')->name('dashboard.report.liquidations_detail_total.list');


	/** Reportes > Liquidación Canales Detallado Resumido*/
	Route::get('/administracion/relacion-de-cobranzas', 'CollectionTotalReportController@index')->name('dashboard.report.collection_total_report');
	Route::post('/administracion/relacion-de-cobranzas/obtener-clientes', 'CollectionTotalReportController@getClients')->name('dashboard.report.collection_total_report.get_clients');
	Route::post('/administracion/relacion-de-cobranzas/validar-formulario', 'CollectionTotalReportController@validateForm')->name('dashboard.report.collection_total_report.validate_form');
	Route::post('/administracion/relacion-de-cobranzas/listar', 'CollectionTotalReportController@list')->name('dashboard.report.collection_total_report.list');


	/** Reportes > Liquidación Detallado Resumido*/
	Route::get('/administracion/finanzas-canales-resumido', 'FinanzasDetailTotalReportController@index')->name('dashboard.report.finanzas_detail_total');
	Route::post('/administracion/finanzas-canales-resumido/validar-formulario', 'FinanzasDetailTotalReportController@validateForm')->name('dashboard.report.finanzas_detail_total.validate_form');
	Route::post('/administracion/finanzas-canales-resumido/obtener-clientes', 'FinanazasDetailTotalReportController@getClients')->name('dashboard.report.finanzas_detail_total.get_clients');
	Route::post('/administracion/finanzas-canales-resumido/listar', 'FinanzasDetailTotalReportController@list')->name('dashboard.report.finanzas_detail_total.list');

	/** Reportes > Flujo de Finanzas Resumido*/
	Route::get('/administracion/flujo-finanzas', 'FlujoReportController@index')->name('dashboard.report.flujo');
	Route::post('/administracion/flujo-finanzas/validar-formulario', 'FlujoReportController@validateForm')->name('dashboard.report.flujo.validate_form');
	Route::post('/administracion/flujo-finanzas/obtener-clientes', 'FlujoReportController@getClients')->name('dashboard.report.flujo.get_clients');
	Route::post('/administracion/flujo-finanzas/listar', 'FlujoReportController@list')->name('dashboard.report.flujo.list');

	/** Reportes > Control de Liquidaciones*/
	Route::get('/administracion/control-liquidacion', 'ControlLiqReportController@index')->name('dashboard.report.control_liq');
	Route::post('/administracion/control-liquidacion/validar-formulario', 'ControlLiqReportController@validateForm')->name('dashboard.report.control_liq.validate_form');
	Route::post('/administracion/control-liquidacion/obtener-clientes', 'ControlLiqReportController@getClients')->name('dashboard.report.control_liq.get_clients');
	Route::post('/administracion/control-liquidacion/listar', 'ControlLiqReportController@list')->name('dashboard.report.control_liq.list');

	/** Reportes > Cierre de Ventas*/
	Route::get('/administracion/cierre-venta', 'CloseSaleReportController@index')->name('dashboard.report.close_sale');
	Route::post('/administracion/cierre-venta/validar-formulario', 'CloseSaleReportController@validateForm')->name('dashboard.report.close_sale.validate_form');
	Route::post('/administracion/cierre-venta/obtener-clientes', 'CloseSaleReportController@getClients')->name('dashboard.report.close_sale.get_clients');
	Route::post('/administracion/cierre-venta/listar', 'CloseSaleReportController@list')->name('dashboard.report.close_sale.list');

	/** Reportes > Liquidación Detallado Resumido*/
	Route::get('/administracion/finanzas-resumen', 'FinanzasResumenReportController@index')->name('dashboard.report.finanzas_resumen');
	Route::post('/administracion/finanzas-resumen/validar-formulario', 'FinanzasResumenReportController@validateForm')->name('dashboard.report.finanzas_resumen.validate_form');
	Route::post('/administracion/finanzas-resumen/obtener-clientes', 'FinanazasResumenReportController@getClients')->name('dashboard.report.finanzas_resumen.get_clients');
	Route::post('/administracion/finanzas-resumen/listar', 'FinanzasResumenReportController@list')->name('dashboard.report.finanzas_resumen.list');

	/** Seguridad > Usuarios*/
	Route::get('/seguridad/usuarios', 'SecurityController@index')->name('dashboard.security.index');
	Route::post('/seguridad/usuarios/crear', 'SecurityController@store')->name('dashboard.security.store');
	Route::post('/seguridad/usuarios/listar', 'SecurityController@list')->name('dashboard.security.list');
	Route::post('/seguridad/usuarios/eliminar', 'SecurityController@delete')->name('dashboard.security.delete');
	Route::post('/seguridad/usuarios/detalle', 'SecurityController@detail')->name('dashboard.security.detail');
	Route::post('/seguridad/usuarios/actualizar', 'SecurityController@update')->name('dashboard.security.update');
	Route::post('/seguridad/usuarios/exportar', 'SecurityController@export')->name('dashboard.security.export');



	/** Transportes > Registro Movimiento de Servicios y Factura */
	Route::get('/transportes/registro-facturas', 'CocheraLogRegisterController@index')->name('dashboard.transport_cochera_log_register');
	Route::post('/transportes/registro-facturas/validar-formulario', 'CocheraLogRegisterController@validateForm')->name('dashboard.transport_cochera_log_register.validate_form');
	Route::post('/transportes/registro-facturas/obtener-areas', 'CocheraLogRegisterController@getAreas')->name('dashboard.transport.cochera_log_register.get_areas');
	Route::post('/transportes/registro-facturas/obtener-costos', 'CocheraLogRegisterController@getCostos')->name('dashboard.transport.cochera_log_register.get_costos');
	Route::post('/transportes/registro-facturas/obtener-costo-secundarios', 'CocheraLogRegisterController@getCostoSecs')->name('dashboard.transport.cochera_log_register.get_costo_secs');
	Route::post('/transportes/registro-facturas/listar', 'CocheraLogRegisterController@list')->name('dashboard.transport.cochera_log_register.list');
	Route::post('/transportes/registro-facturas/listar-cuentas', 'CocheraLogRegisterController@getAccounts')->name('dashboard.transport.cochera_log_register.get_accounts');
	Route::post('/transportes/registro-facturas/listar-articulos', 'CocheraLogRegisterController@getArticles')->name('dashboard.transport.cochera_log_register.get_articles');
	Route::post('/transportes/registro-facturas/obtener-percepcion', 'CocheraLogRegisterController@getPerceptionPercentage')->name('dashboard.transport.cochera_log_register.get_perception_percentage');
	Route::post('/transportes/registro-facturas/obtener-tasas', 'CocheraLogRegisterController@getArticleRates')->name('dashboard.transport.cochera_log_register.get_article_rates');
	Route::post('/transportes/registro-facturas/obtener-articulo', 'CocheraLogRegisterController@getArticle')->name('dashboard.transport.cochera_log_register.get_article');
	Route::post('/transportes/registro-facturas/guardar', 'CocheraLogRegisterController@store')->name('dashboard.transport.cochera_log_register.store');



	/** ADMINISTRACION > Registro facturaciuon caja */
	Route::get('/administracion/registro-facturas', 'CajaRegisterController@index')->name('dashboard.caja_register');
	Route::post('/administracion/registro-facturas/validar-formulario', 'CajaRegisterController@validateForm')->name('dashboard.caja_register.validate_form');
	Route::post('/administracion/registro-facturas/obtener-areas', 'CajaRegisterController@getAreas')->name('dashboard.transport.caja_register.get_areas');
	Route::post('/administracion/registro-facturas/obtener-costos', 'CajaRegisterController@getCostos')->name('dashboard.transport.caja_register.get_costos');
	Route::post('/administracion/registro-facturas/obtener-costo-secundarios', 'CajaRegisterController@getCostoSecs')->name('dashboard.transport.caja_register.get_costo_secs');
	Route::post('/administracion/registro-facturas/listar', 'CajaRegisterController@list')->name('dashboard.transport.caja_register.list');
	Route::post('/administracion/registro-facturas/listar-cuentas', 'CajaRegisterController@getAccounts')->name('dashboard.transport.caja_register.get_accounts');
	Route::post('/administracion/registro-facturas/listar-articulos', 'CajaRegisterController@getArticles')->name('dashboard.transport.caja_register.get_articles');
	Route::post('/administracion/registro-facturas/obtener-percepcion', 'CajaRegisterController@getPerceptionPercentage')->name('dashboard.transport.caja_register.get_perception_percentage');
	Route::post('/administracion/registro-facturas/obtener-tasas', 'CajaRegisterController@getArticleRates')->name('dashboard.transport.caja_register.get_article_rates');
	Route::post('/administracion/registro-facturas/obtener-articulo', 'CajaRegisterController@getArticle')->name('dashboard.transport.caja_register.get_article');
	Route::post('/administracion/registro-facturas/guardar', 'CajaRegisterController@store')->name('dashboard.transport.caja_register.store');



	/** Transportes> Reporte de Facturas */
	Route::get('/transportes/facturas-registradas', 'FacturationCocheraReportController@index')->name('dashboard.report.facturation_cochera');
	Route::post('/transportes/facturas-registradas/validar-formulario', 'FacturationCocheraReportController@validateForm')->name('dashboard.report.facturation_cochera.validate_form');
	Route::post('/transportes/facturas-registradas/listar', 'FacturationCocheraReportController@list')->name('dashboard.report.facturation_cochera.list');
	Route::post('/transportes/facturas-registradas/aprobar', 'FacturationCocheraReportController@aprobar')->name('dashboard.report.facturation_cochera.aprobar_asign');
	Route::post('/transportes/facturas-registradas/desaprobar', 'FacturationCocheraReportController@delete')->name('dashboard.report.facturation_cochera.delete_asign');
	Route::post('/transportes/facturas-registradas/ver-factura', 'FacturationCocheraReportController@view_asign')->name('dashboard.report.facturation_cochera.view_asign');
	Route::post('/transportes/facturas-registradas/listar-metodos-pago', 'FacturationCocheraReportController@getPaymentMethod')->name('dashboard.report.facturation_cochera.method');
	Route::post('/transportes/facturas-registradas/listar-bancos', 'FacturationCocheraReportController@getBankAccount')->name('dashboard.report.facturation_cochera.bank');
	Route::post('/transportes/facturas-registradas/agregar-liquidacion', 'FacturationCocheraReportController@getLiquidation')->name('dashboard.report.facturation_cochera.add_liquidar');
	Route::post('/transportes/facturas-registradas/liquidar', 'FacturationCocheraReportController@store')->name('dashboard.report.facturation_cochera.store');
	Route::post('/transportes/facturas-registradas/descargar-archivo', 'FacturationCocheraReportController@getFile')->name('dashboard.report.facturation_cochera.get_file');

	/** ADMINISTRACION> Reporte liquidacion caja */
	Route::get('/administracion/facturas-registradas', 'CajaReportController@index')->name('dashboard.report.facturation_report_caja');
	Route::post('/administracion/facturas-registradas/validar-formulario', 'CajaReportController@validateForm')->name('dashboard.report.facturation_report_caja.validate_form');
	Route::post('/administracion/facturas-registradas/listar', 'CajaReportController@list')->name('dashboard.report.facturation_report_caja.list');
	Route::post('/administracion/facturas-registradas/aprobar', 'CajaReportController@aprobar')->name('dashboard.report.facturation_report_caja.aprobar_asign');
	Route::post('/administracion/facturas-registradas/desaprobar', 'CajaReportController@delete')->name('dashboard.report.facturation_report_caja.delete_asign');
	Route::post('/administracion/facturas-registradas/ver-factura', 'CajaReportController@view_asign')->name('dashboard.report.facturation_report_caja.view_asign');
	Route::post('/administracion/facturas-registradas/listar-metodos-pago', 'CajaReportController@getPaymentMethod')->name('dashboard.report.facturation_report_caja.method');
	Route::post('/administracion/facturas-registradas/listar-bancos', 'CajaReportController@getBankAccount')->name('dashboard.report.facturation_report_caja.bank');
	Route::post('/administracion/facturas-registradas/agregar-liquidacion', 'CajaReportController@getLiquidation')->name('dashboard.report.facturation_report_caja.add_liquidar');
	Route::post('/administracion/facturas-registradas/liquidar', 'CajaReportController@store')->name('dashboard.report.facturation_report_caja.store');
	Route::post('/administracion/facturas-registradas/descargar-archivo', 'CajaReportController@getFile')->name('dashboard.report.facturation_report_caja.get_file');


	/** ADMINISTRACION > Reporte de Margen por Ruta*/
	Route::get('/reporte/margen-ruta', 'RouteReportController@index')->name('dashboard.report.route');
	Route::post('/reporte/margen-ruta/validar-formulario', 'RouteReportController@validateForm')->name('dashboard.report.route.validate_form');
	Route::post('/reporte/margen-ruta/obtener-clientes', 'RouteReportController@getClients')->name('dashboard.report.route.get_clients');
	Route::post('/reporte/margen-ruta/listar', 'RouteReportController@list')->name('dashboard.report.route.list');


	/* Andromeda> Reporte de Andromeda*/
	Route::get('/beta/reporte-andromeda', 'AndromedaController@Index')->name('dashboard.report_andromeda');

	/* Andromeda> Reporte de excedende*/
	/** Reportes > Venta Canal*/
	Route::get('/beta/excedent-report', 'AndExcedentReportController@index')->name('dashboard.report.excedent');
	Route::post('/beta/excedent-report/validar-formulario', 'AndExcedentReportController@validateForm')->name('dashboard.report.excedent.validate_form');
	Route::post('/beta/excedent-report/listar', 'AndExcedentReportController@list')->name('dashboard.report.excedent.list');

	/* REPORTE CON GRAFICOS*/

	/* Reporte de ventas congráficos */
	Route::get('/grafico/volumen-ventas', 'SalesReportGraficController@index')->name('dashboard.report.sales_grafic');
	Route::post('/grafico/volumen-ventas/validar-formulario', 'SalesReportGraficController@validateForm')->name('dashboard.report.sales_grafic.validate_form');
	Route::post('/grafico/volumen-ventas/listar', 'SalesReportGraficController@list')->name('dashboard.report.sales_grafic.list');
	Route::post('/grafico/volumen-ventas/exportar', 'SalesReportGraficController@export')->name('dashboard.report.sales_grafic.export');



	/** Reportes > Venta Sistemas*/
	Route::get('/reporte/venta-sistemas', 'LiquidationSistemasReportController@index')->name('dashboard.report.liquidations_sistemas');
	Route::post('/reporte/venta-sistemas/validar-formulario', 'LiquidationSistemasReportController@validateForm')->name('dashboard.report.liquidations_sistemas.validate_form');
	Route::post('/reporte/venta-sistemas/obtener-clientes', 'LiquidationSistemasReportController@getClients')->name('dashboard.report.liquidations_sistemas.get_clients');
	Route::post('/reporte/venta-sistemas/listar', 'LiquidationSistemasReportController@list')->name('dashboard.report.liquidations_sistemas.list');


	/** Reportes > Venta Total Sistemas*/
	Route::get('/reporte/total-sistemas', 'LiquidationTotalSistemasReportController@index')->name('dashboard.report.liquidations_total_sistemas');
	Route::post('/reporte/total-sistemas/validar-formulario', 'LiquidationTotalSistemasReportController@validateForm')->name('dashboard.report.liquidations_total_sistemas.validate_form');
	Route::post('/reporte/total-sistemas/obtener-clientes', 'LiquidationTotalSistemasReportController@getClients')->name('dashboard.report.liquidations_total_sistemas.get_clients');
	Route::post('/reporte/total-sistemas/listar', 'LiquidationTotalSistemasReportController@list')->name('dashboard.report.liquidations_total_sistemas.list');



	// testeos
	Route::get('/test-mail', function () {
		Mail::raw('Correo de prueba desde Laravel 5', function ($message) {
			$message->to('u20227716@utp.edu.pe')
				->subject('Prueba de envío');
		});

		return 'Correo enviado';
	});
});
