@php
    $placaAnterior = null;
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
        <h1>{{ $title }}</h1>
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
                    $dias = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($data['fecha']), false);
                    $textoDias = $dias >= 0 ? "Faltan $dias días para vencer" : abs($dias) . ' días vencido';
                    $color = $dias >= 0 ? 'green' : 'red';
                @endphp
                <div class="kt-notification__custom d-flex align-items-center mb-2"
                    style="margin-left: 15px; margin-right: 15px;">
                    <span
                        class="badge p-1 rounded-circle d-flex align-items-center justify-content-center me-2 
									{{ $color == 'red' ? 'bg-danger' : 'bg-success' }}"
                        style="width: 35px; height: 35px; font-size: 16px;">
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
