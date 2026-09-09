<?php

namespace App\Console\Commands;

use App\BlVehicle;
use App\Mail\NotificacionBlVehiculo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacionesBlVehiculos extends Command
{
    protected $signature = 'notificaciones:vehiculos';
    protected $description = 'Envia notificaciones de vencimientos por primera vez y luego cada 15 días';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $hoy = Carbon::today();

        $notificaciones = BlVehicle::select(
            'transportist_id',
            'plate',
            'fecha_tanque_cil',
            'fecha_tanque',
            'fecha_vcto',
            'fecha_soat',
            'fecha_rt',
            'fecha_rtmg',
            'fecha_bon',
            'f_p_callao',
            'f_p_lima',
            'f_circulation_card',
            'f_cert_insp',
            'f_curso_nor',
            'f_lib_ter',
            'f_cer_fum'

        )
            ->get()
            ->map(function ($item) use ($hoy) {

                $item->mostrar_fecha_tanque_cil = $item->fecha_tanque_cil
                    && Carbon::parse($item->fecha_tanque_cil)->lessThanOrEqualTo($hoy->copy()->addMonth());
                $item->mostrar_fecha_tanque = $item->fecha_tanque
                    && Carbon::parse($item->fecha_tanque)->lessThanOrEqualTo($hoy->copy()->addMonth());
                $item->mostrar_fecha_vcto = $item->fecha_vcto
                    && Carbon::parse($item->fecha_vcto)->lessThanOrEqualTo($hoy->copy()->addMonth());

                $item->mostrar_fecha_soat = $item->fecha_soat
                    && Carbon::parse($item->fecha_soat)->lessThanOrEqualTo($hoy->copy()->addDays(20));
                $item->mostrar_fecha_rt = $item->fecha_rt
                    && Carbon::parse($item->fecha_rt)->lessThanOrEqualTo($hoy->copy()->addDays(20));
                $item->mostrar_fecha_rtmg = $item->fecha_rtmg
                    && Carbon::parse($item->fecha_rtmg)->lessThanOrEqualTo($hoy->copy()->addDays(20));
                $item->mostrar_fecha_bon = $item->fecha_bon
                    && Carbon::parse($item->fecha_bon)->lessThanOrEqualTo($hoy->copy()->addDays(60));
                $item->mostrar_fecha_f_p_callao = $item->f_p_callao
                    && Carbon::parse($item->f_p_callao)->lessThanOrEqualTo($hoy->copy()->addDays(60));
                $item->mostrar_fecha_f_p_lima = $item->f_p_lima
                    && Carbon::parse($item->f_p_lima)->lessThanOrEqualTo($hoy->copy()->addDays(60));
                $item->mostrar_fecha_f_circulation_card = $item->f_circulation_card
                    && Carbon::parse($item->f_circulation_card)->lessThanOrEqualTo($hoy->copy()->addDays(30));
                $item->mostrar_fecha_f_cert_insp = $item->f_cert_insp
                    && Carbon::parse($item->f_cert_insp)->lessThanOrEqualTo($hoy->copy()->addMonths(2));
                $item->mostrar_fecha_f_curso_nor = $item->f_curso_nor
                    && Carbon::parse($item->f_curso_nor)->lessThanOrEqualTo($hoy->copy()->addDays(20));
                $item->mostrar_fecha_f_lib_ter = $item->f_lib_ter
                    && Carbon::parse($item->f_lib_ter)->lessThanOrEqualTo($hoy->copy()->addDays(30));
                $item->mostrar_fecha_f_cer_fum = $item->f_cer_fum
                    && Carbon::parse($item->f_cer_fum)->lessThanOrEqualTo($hoy->copy()->addDays(20));

                return $item;
            });


        //Mail::to('juan.olivas@puntodedistribucion.com')->send(new NotificacionBlVehiculo($notificaciones));

        $notificaciones_1 = $notificaciones->filter(function ($item) {
            return $item->transportist_id == 1;
        });

        $notificaciones_4 = $notificaciones->filter(function ($item) {
            return $item->transportist_id == 4;
        });

        $nombre_1 = 'PUNTO DE DISTRIBUCIÓN';
        $nombre_4 = 'BLUE LINE';


        if ($notificaciones_1->isNotEmpty()) {
            $destinatarios = [
                'danfeer.ingeniero.de.sistema@gmail.com',
                'juan.olivas@puntodedistribucion.com',
                'analista.abastecimiento@puntodedistribucion.com',
                'administracion@blueline.pe',
                'analista.distribucion@puntodedistribucion.com',
                'analista.ventadirecta@puntodedistribucion.com',
                'cesar.quispe@puntodedistribucion.com'
            ];

            Mail::to(array_shift($destinatarios))
                ->bcc($destinatarios)
                ->send(new NotificacionBlVehiculo($notificaciones_1, $nombre_1));
        }

        // BLUE LINE
        if ($notificaciones_4->isNotEmpty()) {
            $destinatarios = [
                'danfeer.ingeniero.de.sistema@gmail.com',
                'juan.olivas@puntodedistribucion.com',
                'operaciones@blueline.pe',
                'administracion@blueline.pe',
                'grosales@blueline.pe',
                'supervisor@blueline.pe',
                'renting@blueline.pe',
                'control@blueline.pe'
            ];

            Mail::to(array_shift($destinatarios))
                ->bcc($destinatarios)
                ->send(new NotificacionBlVehiculo($notificaciones_4, $nombre_4));
        }

    }
}
