@php
    $placaAnterior = null;
@endphp

@foreach ($notificaciones_drivers as $not)
    @php
        
        $fechas = [
            '📅 Licencia A3' => [
                'fecha' => $not->f_license_a3,
                'mostrar' => $not->mostrar_f_license_a3,
            ],
            '📅 Licencia A4' => [
                'fecha' => $not->f_license_a4,
                'mostrar' => $not->mostrar_f_license_a4,
            ],
            '⏳ EMO' => [
                'fecha' => $not->f_emo,
                'mostrar' => $not->mostrar_f_emo,
            ],
            '📅 CARNET SOLGAS' => [
                'fecha' => $not->f_carnet_solgas,
                'mostrar' => $not->mostrar_f_carnet_solgas,
            ],
            '📅 Antecedentes Penales' => [
                'fecha' => $not->f_ant_pen,
                'mostrar' => $not->mostrar_f_ant_pen,
            ],
            '📅 Antecedentes Policiales' => [
                'fecha' => $not->f_ant_pol,
                'mostrar' => $not->mostrar_f_ant_pol,
            ],
            '📅 Curso Normativo de Transporte de Hidrocarburos' => [
                'fecha' => $not->f_curso_nor,
                'mostrar' => $not->mostrar_f_curso_nor,
            ],
            '📅 Libreta tripulante terrestre' => [
                'fecha' => $not->f_lib_ter,
                'mostrar' => $not->mostrar_f_lib_ter,
            ]
        ];

        $tieneNotificaciones = collect($fechas)->contains(function ($item) {
            return $item['fecha'] && $item['mostrar'];
        });
    @endphp

    @if ($tieneNotificaciones)
        <h1>{{ $title }}</h1>
        @if ($placaAnterior !== $not->id)
        
            @if (!is_null($placaAnterior))
                <hr>
            @endif
            <h5 style="margin:20px 0; color:#333;">
                👨‍✈️ Chofer: <strong>{{ $not->document_number }} - {{ $not->first_name }} {{ $not->last_name }}</strong>
            </h5>
            @php
                $placaAnterior = $not->id;
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

