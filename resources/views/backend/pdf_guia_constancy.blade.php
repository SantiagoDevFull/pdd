<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Constancia de Verificación de Pesos y Medidas</title>
<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 11px;
        margin: -20px 20px;
        color: #000;
    }
    .container {
        width: 100%;
        margin: 0 auto;
    }
    .title {
        text-align: center;
        font-weight: bold;
        font-size: 18px;
    }
    .subtitle {
        text-align: center;
        font-size: 10px;
        margin-top: 3px;
        font-weight: bold;
    }
    .logo {
        text-align: center;
        font-size: 14px;
    }
    .registro {
        border: 1px solid #000;
        padding: 5px 10px;
        font-weight: bold;
        font-size: 14px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 8px;
    }
    td, th {
        border: 1px solid #000;
        padding: 4px;
        vertical-align: middle;
    }
    th {
        font-weight: bold;
        text-align: center;
    }
    .no-border {
        border: none !important;
    }
    .center {
        text-align: center;
    }
    .bold {
        font-weight: bold;
    }
    .small {
        font-size: 9px;
    }
    .checkbox {
        width: 14px;
        height: 14px;
        border: 1px solid #000;
        display: inline-block;
        text-align: center;
        line-height: 14px;
        font-weight: bold;
    }
    .observaciones {
        height: 40px;
    }
    .firma {
        margin-top: 30px;
        width: 300px;
        text-align: center;
    }
</style>
</head>
<body>
    <div class="container">

        <div class="title">CONSTANCIA DE VERIFICACIÓN DE PESOS Y MEDIDAS</div>
        <div class="subtitle">
            ALMACENES, TERMINALES DE ALMACENAMIENTO, TERMINALES PORTUARIOS O AEROPORTUARIOS, GENERADORES, DADORES O REMITENTES DE LA MERCANCÍA
        </div>
        <div class="subtitle">
            DECRETO SUPREMO N° 058-2003-MTC REGLAMENTO NACIONAL DE VEHÍCULOS Y SUS NORMAS MODIFICATORIAS
        </div>

        <div class="logo">
            <img src="{{ asset('backend/img/logo-pdf-puntod.png') }}" alt="">
        </div>

        <table>
            <tr>
                <th>Fecha</th>
                <td colspan="5">{{ $obj->traslate_date }}</td>
                <th>REGISTRO N°</th>
                <td colspan="5">115-{{ $obj->id }}</td>
            </tr>
            <tr>
                <td class="bold">NOMBRE DE LA EMPRESA</td>
                <td colspan="5">PUNTO DE DISTRIBUCION S.A.C.</td>
                <td class="bold">N° RUC</td>
                <td>20293658146</td>
                <td class="bold">TELEF.</td>
                <td colspan="3">01-415-4747</td>
            </tr>
            <tr>
                <td class="bold">DIRECCIÓN</td>
                <td colspan="11">AV CAJAMARCA 586 - EL DORADO</td>
            </tr>
            <tr>
                <td class="bold">DISTRITO</td>
                <td colspan="5">PUENTE PIEDRA</td>
                <td class="bold">PROVINCIA</td>
                <td>LIMA</td>
                <td class="bold">DEPARTAMENTO</td>
                <td colspan="3">LIMA</td>
            </tr>
        </table>

        <table>
            <tr>
                <td class="bold" colspan="6">I) TIPO DE MERCANCÍA TRANSPORTADA:</td>
            </tr>
            <tr>
                <td colspan="6">
                    @foreach($details as $detail)
                    {{ $detail->quantity }} Cil de  {{ $detail->article_name }} 
                    @if(!$loop->last)
                        &nbsp;&nbsp;
                    @endif
                @endforeach
                </td>
            </tr>
            <tr>
                <td colspan="6" class="small">Según Guía de Remisión que se Adjunta: {{ $obj->guia }}</td>
            </tr>
        </table>

        <table>
            <tr>
                <td class="bold" colspan="6">II) TIPO DE CONTROL EFECTUADO</td>
            </tr>
            <tr class="center">
                <td>BALANZA</td>
                <td><span class="checkbox">X</span></td>
                <td>SOFTWARE</td>
                <td></td>
                <td>CUBICACIÓN</td>
                <td>OTROS</td>
            </tr>
        </table>

        <table>
            <tr>
                <td class="bold" colspan="9">IV) DATOS DEL VEHÍCULO</td>
            </tr>
            <tr class="center" style="font-size: 9px">
                <td>PLACAS</td>
                <td colspan="3">DIMENSIÓN TOTAL DEL VEHÍCULO<br><span class="small">(Incluida la mercancía)</span></td>
                <td>CONFIGURACIÓN VEHICULAR</td>
                <td>PESO BRUTO VEHICULAR MAX. PERMITIDO (Kg.)</td>
                <td>PESO BRUTO TOTAL TRANSPORTADO (Kg.)</td>
                <td>PBMax. Para no control de pesos por ejes (DS 058-2003-MTC)</td>
                <td>PBMax. Para no control de pesos por ejes (DS 058-2003-MTC)</td>
            </tr>
            <tr class="center">
                <td>{{ $obj->vehicle_placa }}</td>
                <td>LARGO<br>{{ $obj->vehicle_largo }}</td>
                <td>ANCHO<br>{{ $obj->vehicle_ancho }}</td>
                <td>ALTO<br>{{ $obj->vehicle_alto }}</td>
                <td>{{ $obj->vehicle_conf }}</td>
                <td>{{ $obj->vehicle_capacity }}</td>
                <td>{{ $kg }}</td>
                <td></td>
                <td></td>
            </tr>
        </table>

        <div>
            <p style="font-size: 7px">
                (2) EL GENERADOR DEBERÁ CONTROLAR QUE EL PESO BRUTO TRANSPORTADO NO SEA MAYOR 
                QUE EL 95% DE LA SUMATORIA DE LOS PESOS POR EJES O CONJUNTOS DE EJES INDICADOS EN EL ANEXO IV DEL
                RNV
                <br><br>
                (3) PESO BRUTO MAXIMO PARA NO CONTROL POR EJES A VEHÍCULOS CON BONIFICACIONES PERMITIDAS PARA SUSPENSIÓN NEUMÁTICA Y NEUMÁTICOS EXTRA ANCHOS
            </p>
        </div>

        <table>
            <tr>
                <td class="bold" colspan="7">
                    V) CONTROL DE PESOS POR EJE O CONJUNTO DE EJES:<br>
                    <span class="small">Para aquellos vehículos que exceden el 95% de la suma de los pesos por ejes.</span>
                </td>
            </tr>
            <tr class="center">
                <td colspan="7">
                    DISTRIBUCIÓN DE PESOS POR CONJUNTO DE EJES EN KG,
                </td>
            </tr>
            <tr class="center">
                <th>PESOS</th>
                <th>1er cto</th>
                <th>2do cto</th>
                <th>3er cto</th>
                <th>4to cto</th>
                <th>5to cto</th>
                <th>6to cto</th>
            </tr>
            <tr>
                <td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
        </table>

        <table>
            <tr>
                <td class="small">
                    <b>DECRETO SUPREMO N° 058-2003-MTC, modificado por D.S. N° 006-2008-MTC, ANEXO IV: PESOS Y MEDIDAS.</b><br>
                    Artículo 37°.- Pesos máximos permitidos: (...) están exonerados del control de peso por eje o conjunto de ejes,
                    los vehículos o combinaciones vehiculares que transiten con un peso bruto vehicular que no exceda del 95% de la
                    sumatoria de pesos por eje o conjunto de ejes, en tanto este valor no supere el Peso Bruto Vehicular máximo
                    permitido por el presente reglamento o sus normas complementarias.
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td class="bold">OBSERVACIONES:</td>
            </tr>
            <tr>
                <td class="observaciones"></td>
            </tr>
        </table>

        <div class="firma">
            _______________________________<br>
            Representante del Generador de Carga<br>
            Firma y Sello
        </div>

        <div>
            <p><b>NOTA</b></p>
            <p style="font-size: 9px">
                1,- LO CONSIGNADO EN EL PRESENTE FORMATO TIENE CARÁCTER DE DECLARACIÓN JURADA, POR LO QUE ESTARÁ SUJETO A LO ESTABLECIDO EN EL ART.
                32 NUMERAL 32.3 DE LA LEY N° 27444; SIN PERJUICIO DE LA SANCIÓN ADMINISTRATIVA CORRESPONDIENTE. TENIENDO QUE CUMPLIR QUIEN GENERA LA
                CARGA EL LLENADO DE LA PRESENTE CONSTANCIA
                <br>
                2,- Solo para Terminales Portuarios, Aeroportuarios, Almacenes Aduaneros y de carga de Hidrocarburos, LA GUÍA DE SALIDA , CONSTANCIA DE PESO O TICKET DE
                PESO DE SALIDA, reemplazará a la presente constancia, la cual deberá contener lo indicado en el punto N° I y adicionalmente las Placas, Tipo de Vehiculo y Peso Bruto
                Total del Vehículo. Cuando el destino de la mercancia es local no se requiere la emisión de esta constancia de control de pesos y medidas
                <br>
                3,- Del punto IV - "Dimensión Total del Vehiculo y Carga", será llenado cuando excedan las dimensiones permitidas.
                <br>
                4.- Para el transporte de contenedores vaciosla presentación del EIR (Equipment Interchance Reception) reemplaza al presente formato; Asimismo, los contenedores
                no están sujetos al control de pesos por ejes.
                <br>
                5.- Para el control en las balanzas de las Estaciones de Pesaje; "Peso Bruto Total Transportado", se consideraran las tolerancias del 3% vigente en el pesaje dinamico
                <br>
                6.- De no consignar los datos en el punto V, cuando corresponda, el generador declara que los pesos por eje estan dentro de lo permitido en el RNV.
            </p>
        </div>

    </div>
</body>
</html>
