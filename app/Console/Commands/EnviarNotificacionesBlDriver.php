<?php

namespace App\Console\Commands;

use App\BlDriver;
use App\Mail\NotificacionBlDriver;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacionesBlDriver extends Command
{
    protected $signature = 'notificaciones:choferes';
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

        $notificaciones_drivers = BlDriver::select(
            'id',
            'transportist_id',
            'document_number',
            'first_name',
            'last_name',
            'f_license_a3',
            'f_license_a4',
            'f_emo',
            'f_carnet_solgas',
            'f_ant_pen',
            'f_ant_pol',
            'f_curso_nor',
            'f_lib_ter'
        )
            ->get()
            ->map(function ($item) use ($hoy) {

                $item->mostrar_f_license_a3 = $item->f_license_a3
                    && Carbon::parse($item->f_license_a3)->lessThanOrEqualTo($hoy->copy()->addMonth());

                $item->mostrar_f_license_a4 = $item->f_license_a4
                    && Carbon::parse($item->f_license_a4)->lessThanOrEqualTo($hoy->copy()->addMonth());
                $item->mostrar_f_emo = $item->f_emo
                    && Carbon::parse($item->f_emo)->lessThanOrEqualTo($hoy->copy()->addDays(7));
                $item->mostrar_f_carnet_solgas = $item->f_carnet_solgas
                    && Carbon::parse($item->f_carnet_solgas)->lessThanOrEqualTo($hoy->copy()->addMonth());
                $item->mostrar_f_ant_pen = $item->f_ant_pen
                    && Carbon::parse($item->f_ant_pen)->lessThanOrEqualTo($hoy->copy()->addDays(15));
                $item->mostrar_f_ant_pol = $item->f_ant_pol
                    && Carbon::parse($item->f_ant_pol)->lessThanOrEqualTo($hoy->copy()->addDays(15));
                $item->mostrar_f_curso_nor = $item->f_curso_nor
                    && Carbon::parse($item->f_curso_nor)->lessThanOrEqualTo($hoy->copy()->addMonth());
                $item->mostrar_f_lib_ter = $item->f_lib_ter
                    && Carbon::parse($item->f_lib_ter)->lessThanOrEqualTo($hoy->copy()->addMonth());

                return $item;
            });


        //BLUE
        //Mail::to('operaciones@blueline.pe')->send(new NotificacionBlDriver($paraEnviar));
        //Mail::to('grosales@blueline.pe')->send(new NotificacionBlDriver($paraEnviar));
        //Mail::to('supervisor@blueline.pe')->send(new NotificacionBlDriver($paraEnviar));
        //Mail::to('renting@blueline.pe')->send(new NotificacionBlDriver($paraEnviar));
        //Mail::to('control@blueline.pe')->send(new NotificacionBlDriver($paraEnviar));


        //PUNTO
        //Mail::to('analista.abastecimiento@puntodedistribucion.com')->send(new NotificacionBlDriver($paraEnviar));
        //Mail::to('administracion@blueline.pe')->send(new NotificacionBlDriver($paraEnviar));
        //Mail::to('analista.distribucion@puntodedistribucion.com')->send(new NotificacionBlDriver($paraEnviar));
        //Mail::to('analista.ventadirecta@puntodedistribucion.com')->send(new NotificacionBlDriver($paraEnviar));

        $notificaciones_1 = $notificaciones_drivers->filter(function ($item) {
            return $item->transportist_id == 1;
        });

        $notificaciones_4 = $notificaciones_drivers->filter(function ($item) {
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
                ->send(new NotificacionBlDriver($notificaciones_1, $nombre_1));
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
                ->send(new NotificacionBlDriver($notificaciones_4, $nombre_4));
        }


    }
}
