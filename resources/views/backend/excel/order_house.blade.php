<table>
  <thead>
  <tr>
    <th colspan="42" style="text-align:center;font-weight:bold;font-size:14pt;height: 30px;">LISTA DE PEDIDOS AL {{ $datetime }}</th>
  </tr>
  <tr>
    <th></th>
    <th></th>
    <th></th>
    <th></th>
    <th></th>
    <th></th>
  </tr>
  </thead>
  <tbody>
  <tr>
          <td style="font-weight:bold;height: 25px; background-color: red; color: white;">Serie</td>
          <td style="font-weight:bold;height: 25px; background-color: red; color: white;">Correlativo</td>
          <td style="font-weight:bold;height: 25px; background-color: red; color: white;">Estado Pedido</td>
          <td style="font-weight:bold;height: 25px; background-color: blue; color: white;">Año</td>
          <td style="font-weight:bold;height: 25px; background-color: blue; color: white;">Mes</td>
          <td style="font-weight:bold;height: 25px; background-color: blue; color: white;">Dia</td>
          <td style="font-weight:bold;height: 25px; background-color: blue; color: white;">Semana</td>
          <td style="font-weight:bold;height: 25px; background-color: red; color: white;">Próxima Compra</td>
          <td style="font-weight:bold;height: 25px; background-color: blue; color: white;">Sub Canal</td>
          <td style="font-weight:bold;height: 25px; background-color: blue; color: white;">Negocio</td>
          <td style="font-weight:bold;height: 25px; background-color: yellow; color: black;">Creacion</td>
          <td style="font-weight:bold;height: 25px; background-color: yellow; color: black;">Celular</td>
          <td style="font-weight:bold;height: 25px; background-color: yellow; color: black;">Recuperado</td>
          <td style="font-weight:bold;height: 25px; background-color: yellow; color: black;">Nuevo</td>
          <td style="font-weight:bold;height: 25px; background-color: yellow; color: black;">Cliente</td>
          <td style="font-weight:bold;height: 25px; background-color: yellow; color: black;">Nombre</td>
          <td style="font-weight:bold;height: 25px; background-color: yellow; color: black;">Estado</td>
          <td style="font-weight:bold;height: 25px; background-color: yellow; color: black;">Razón</td>
          <td style="font-weight:bold;height: 25px; background-color: yellow; color: black;">Ruta</td>
          <td style="font-weight:bold;height: 25px; background-color: yellow; color: black;">Zona</td>
          <td style="font-weight:bold;height: 25px; background-color: yellow; color: black;">Sector</td>
          <td style="font-weight:bold;height: 25px; background-color: blue; color: white;">Origen</td>
          <td style="font-weight:bold;height: 25px; background-color: red; color: white;">Serie</td>
          <td style="font-weight:bold;height: 25px; background-color: red; color: white;">Comprobante</td>
          <td style="font-weight:bold;height: 25px; background-color: blue; color: white;">Tipo de Producto</td>
          <td style="font-weight:bold;height: 25px; background-color: blue; color: white;">Artículo</td>
          <td style="font-weight:bold;height: 25px; background-color: blue; color: white;">Precio</td>
          <td style="font-weight:bold;height: 25px; background-color: blue; color: white;">Cantidad</td>
          <td style="font-weight:bold;height: 25px; background-color: blue; color: white;">Total</td>
          <td style="font-weight:bold;height: 25px; background-color: blue; color: white;">Pago</td>
          <td style="font-weight:bold;height: 25px; background-color:red; color: white;">Monto</td>
          <td style="font-weight:bold;height: 25px; background-color:red; color: white;">Vuelto</td>
          <td style="font-weight:bold;height: 25px; background-color: green; color: black;">Llamada Cliente</td>
          <td style="font-weight:bold;height: 25px; background-color: green; color: black;">Entrega Moto</td>
          <td style="font-weight:bold;height: 25px; background-color: green; color: black;">Salida Local</td>
          <td style="font-weight:bold;height: 25px; background-color: green; color: black;">Entrega Cliente</td>
          <td style="font-weight:bold;height: 25px; background-color: green; color: black;">Retorno Local</td>
          <td style="font-weight:bold;height: 25px; background-color: blue; color: white;">Tiempo Total</td>
          <td style="font-weight:bold;height: 25px; background-color: green; color: black;">Turno</td>
          <td style="font-weight:bold;height: 25px; background-color: green; color: black;">Chofer</td>
          <td style="font-weight:bold;height: 25px; background-color: blue; color: white;">Motivo de Demora</td>
          <td style="font-weight:bold;height: 25px; background-color: blue; color: white;">Observaciones del Cliente</td>

  </tr>
  @foreach ($elements as $element)
  <tr>
          <td>{{ $element->serie_pedido }}</td>
          <td>{{ $element->correlative_pedido }}</td>
          <td>{{ $element->estado }}</td>
          <td>{{ $element->anio }}</td>
          <td>{{ $element->mes }}</td>
          <td>{{ $element->dia }}</td>
          <td>{{ $element->semana }}</td>
          <td>{{ $element->prox_compra }}</td>
          <td>{{ $element->canal }}</td>
          <td>{{ $element->negocio }}</td>
          <td>{{ $element->creacion }}</td>
          <td>{{ $element->celular }}</td>
          <td>{{ $element->recuperado }}</td>
          <td>{{ $element->nuevo }}</td>
          <td>{{ $element->cliente }}</td>
          <td>{{ $element->nombre }}</td>
          <td>{{ $element->estado_cli }}</td>
          <td>{{ $element->razon }}</td>
          <td>{{ $element->ruta }}</td>
          <td>{{ $element->zona }}</td>
          <td>{{ $element->sector }}</td>
          <td>{{ $element->origen }}</td>
          <td>{{ $element->serie }}</td>
          <td>{{ $element->voucher }}</td>
          <td>{{ $element->tipo_producto }}</td>
          <td>{{ $element->articulo }}</td>
          <td>{{ $element->precio }}</td>
          <td>{{ $element->cantidad }}</td>
          <td>{{ $element->total }}</td>
          <td>{{ $element->pago }}</td>
          <td>{{ $element->monto }}</td>
          <td>{{ $element->vuelto }}</td>
          <td>{{ $element->hora_llamada }}</td>
          <td>{{ $element->hora_entrega_balon }}</td>
          <td>{{ $element->hora_salida_pedido }}</td>
          <td>{{ $element->hora_entrega_pedido }}</td>
          <td>{{ $element->hora_retorno }}</td>
          <td>{{ $element->tiempo_total }}</td>
          <td>{{ $element->turno }}</td>
          <td>{{ $element->empleado }}</td>
          <td>{{ $element->motivo_demora }}</td>
          <td>{{ $element->house_order_obs_cli }}</td>
  </tr>
  @endforeach
  </tbody>
</table>
