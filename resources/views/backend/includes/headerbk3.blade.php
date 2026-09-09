<div class="container-fluid">
<!-- begin:: Header -->
<div id="kt_header" class="kt-header kt-grid__item  kt-header--fixed">

	<!-- begin:: Header Menu -->
	<button class="kt-header-menu-wrapper-close" id="kt_header_menu_mobile_close_btn"><i class="la la-close"></i></button>
	<div class="kt-header-menu-wrapper" id="kt_header_menu_wrapper">

	</div>
	<!-- end:: Header Menu -->

	@if (Auth::user()->id == 61)
	<!-- begin:: Header Topbar -->
	<div class="kt-header__topbar">
		<!--begin: User Bar -->
		<div class="kt-header__topbar-item kt-header__topbar-item--user">
			<div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
				<div class="kt-header__topbar-user">
					<img class="kt-hidden" alt="Pic" src="{{ asset('backend/media/users/300_25.jpg') }}" />
					<!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
					<span
						class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold notification-icon">
						<i class="fa fa-solid fa-bell"></i>
						<span class="notification-count text-danger"> {{ $contador_notificaciones }}</span>
					</span>
				</div>
			</div>

			<div class="dropdown-menu dropdown-menu-fit dropdown-menu-left dropdown-menu-anim dropdown-menu-top-unround w-45"
				onclick="event.stopPropagation();">
				<!--begin: Head -->
				<div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x"
					style="background-image: url({{ asset('backend/media/misc/bg-1.jpg') }})">
					<div class="kt-user-card__avatar">
						<img class="kt-hidden" alt="Pic" src="{{ asset('backend/media/users/300_25.jpg') }}" />
						<span class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-danger">
							{{ $contador_notificaciones }}
						</span>
					</div>
					<div class="kt-user-card__name">
						<i class="fa fa-solid fa-bell"></i> Notificaciones
					</div>
				</div>


				
				<!--end: Head -->


				<!--NOTIFICACIONES -->

				<!--
				<div class="kt-notification" style="max-height: 600px; overflow-y: auto; font-size: 16px;">
					@php
						$placaAnterior = null;
						$cont = 0;
					@endphp

					@foreach ($notificaciones as $not)
						@php
							$fechas = [
								'📅 FECHA TANQUE CILINDRO' => [
									'fecha' => $not->fecha_tanque_cil,
									'mostrar' => $not->mostrar_fecha_tanque_cil,
								],
								'📅 FECHA TANQUE' => [
									'fecha' => $not->fecha_tanque,
									'mostrar' => $not->mostrar_fecha_tanque,
								],
								'⏳ FECHA VENCIMIENTO' => [
									'fecha' => $not->fecha_vcto,
									'mostrar' => $not->mostrar_fecha_vcto,
								],
								'📅 FECHA SOAT' => [
									'fecha' => $not->fecha_soat,
									'mostrar' => $not->mostrar_fecha_soat,
								],
								'📅 FECHA REV. TÉCNICA GENERAL' => [
									'fecha' => $not->fecha_rt,
									'mostrar' => $not->mostrar_fecha_rt,
								],
								'📅 FECHA REV. TÉCNICA PELIGROSA' => [
									'fecha' => $not->fecha_rtmg,
									'mostrar' => $not->mostrar_fecha_rtmg,
								],
								'⏳ FECHA BONIFICACIÓN' => [
									'fecha' => $not->fecha_bon,
									'mostrar' => $not->mostrar_fecha_bon,
								],
								'📅 FECHA PERMISO CALLE CALLAO' => [
									'fecha' => $not->f_p_callao,
									'mostrar' => $not->mostrar_fecha_f_p_callao,
								],
								'📅 FECHA PERMISO CALLE LIMA' => [
									'fecha' => $not->f_p_lima,
									'mostrar' => $not->mostrar_fecha_f_p_lima,
								],
								'📅 FECHA TARJETA DE CIRCULACION' => [
									'fecha' => $not->f_circulation_card,
									'mostrar' => $not->mostrar_fecha_f_circulation_card,
								],
								'📅 FECHA CERT. INSPECCIÓN' => [
									'fecha' => $not->f_cert_insp,
									'mostrar' => $not->mostrar_fecha_f_cert_insp,
								],
								'📅 FECHA CURSO NORMATIVO' => [
									'fecha' => $not->f_curso_nor,
									'mostrar' => $not->mostrar_fecha_f_curso_nor,
								],
								'📅 FECHA LIB. TRIPULANTE TERRESTRE' => [
									'fecha' => $not->f_lib_ter,
									'mostrar' => $not->mostrar_fecha_f_lib_ter,
								],
								'📅 FECHA CERT. FUMIGACIÓN' => [
									'fecha' => $not->f_cer_fum,
									'mostrar' => $not->mostrar_fecha_f_cer_fum,
								],
							];

							$tieneNotificaciones = collect($fechas)->contains(function ($item) {
								return $item['fecha'] && $item['mostrar'];
							});
						@endphp

						@if ($tieneNotificaciones)
							@if ($placaAnterior !== $not->plate)
								@if (!is_null($placaAnterior))
									<hr>
								@endif
								<h5 style="margin:20px 0; color:#333;">
									🚗 Placa: <strong>{{ $not->plate }}</strong>
								</h5>
								@php
									$placaAnterior = $not->plate;
								@endphp
							@endif

							@foreach ($fechas as $label => $data)
								@if ($data['fecha'] && $data['mostrar'])
									@php

										$dias = \Carbon\Carbon::now()->diffInDays(
											\Carbon\Carbon::parse($data['fecha']),
											false
										);
										$textoDias =
											$dias >= 0
												? "Faltan $dias días para vencer"
												: abs($dias) . ' días vencido';
										$color = $dias >= 0 ? 'green' : 'red';
										$cont++;
									@endphp
									<div class="kt-notification__custom d-flex align-items-center mb-2"
										style="margin-left: 15px; margin-right: 15px;">
										<span
											class="badge p-1 rounded-circle d-flex align-items-center justify-content-center me-2 
									{{ $color == 'red' ? 'bg-danger' : 'bg-success' }}"
											style="width: 35px; height: 35px; font-size: 16px;">
											{{ $cont }}
										</span>

										<small style="color: {{ $color }}">
											{{ $label }} <span class="text-dark">vence :</span>
											{{ \Carbon\Carbon::parse($data['fecha'])->format('d/m/Y') }}<br>
											({{ $textoDias }})
										</small>
									</div>
								@endif
							@endforeach
						@endif
					@endforeach
				</div>-->
				<!-- FIN NOTIFICACIONES -->
			</div>
		</div>
		<!--end: User Bar -->
	</div>
	<!-- end:: Header Topbar -->
@endif -->

	
	<!-- begin:: Header Topbar -->
	<div class="kt-header__topbar">
		<!--begin: User Bar -->
		<div class="kt-header__topbar-item kt-header__topbar-item--user">
			<div class="kt-header__topbar-wrapper" data-toggle="dropdown" data-offset="0px,0px">
				<div class="kt-header__topbar-user">
					<span class="kt-header__topbar-welcome">Hola,</span>
					<span class="kt-header__topbar-username">{{ Auth::user()->name }}</span>
					<img class="kt-hidden" alt="Pic" src="{{ asset('backend/media/users/300_25.jpg') }}" />

					<!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
					<span class="kt-badge kt-badge--username kt-badge--unified-success kt-badge--lg kt-badge--rounded kt-badge--bold">
						@php
							echo substr(Auth::user()->name, 0, 1)
						@endphp
					</span>
				</div>
			</div>
			<div class="dropdown-menu dropdown-menu-fit dropdown-menu-right dropdown-menu-anim dropdown-menu-top-unround dropdown-menu-xl">
				<!--begin: Head -->
				<div class="kt-user-card kt-user-card--skin-dark kt-notification-item-padding-x" style="background-image: url({{ asset('backend/media/misc/bg-1.jpg') }})">
					<div class="kt-user-card__avatar">
						<img class="kt-hidden" alt="Pic" src="{{ asset('backend/media/users/300_25.jpg') }}" />

						<!--use below badge element instead the user avatar to display username's first letter(remove kt-hidden class to display it) -->
						<span class="kt-badge kt-badge--lg kt-badge--rounded kt-badge--bold kt-font-success">
							@php
								echo substr(Auth::user()->name, 0, 1)
							@endphp
						</span>
					</div>
					<div class="kt-user-card__name">
						{{ Auth::user()->name }}
					</div>
				</div>
				<!--end: Head -->

				<!--begin: Navigation -->
				<div class="kt-notification">
					<div class="kt-notification__custom">
						<a href="{{ route('logout') }}" class="btn btn-label-brand btn-sm btn-bold">Cerrar sesión</a>
					</div>
				</div>
				<!--end: Navigation -->
			</div>
		</div>
		<!--end: User Bar -->
	</div>
	<!-- end:: Header Topbar -->
</div>
<!-- end:: Header -->
</div>