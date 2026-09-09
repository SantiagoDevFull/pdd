@extends('backend.templates.app')

@section('title', 'Reporte de Inventario de Cilindros')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-sm-12">
            <h1 class="m-0"><i class="fas fa-gas-pump"></i> Reporte de Inventario de Cilindros</h1>
        </div>
    </div>

    <!-- Card Principal -->
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-file-excel"></i> Generar Reporte de Inventario</h3>
        </div>
        <div class="card-body">
            <form id="formReporte" method="GET" action="{{ route('dashboard.envases.export') }}">
                <div class="row align-items-end">
                    <!-- Selector de Planta -->
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label for="warehouse_type_index">
                                <i class="fas fa-warehouse"></i> Planta
                            </label>
                            <select name="warehouse_type_index" id="warehouse_type_index" class="form-control">
                                <option value="">-- Todas las Plantas (Consolidado) --</option>
                                @foreach($plantas as $planta)
                                    <option value="{{ $planta->id }}">{{ $planta->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Selector de Año -->
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label for="year">
                                <i class="fas fa-calendar-alt"></i> Año del Reporte
                            </label>
                            <select name="year" id="year" class="form-control" required>
                                @for($y = 2019; $y <= date('Y'); $y++)
                                    <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Botón de Exportar -->
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-download"></i> Exportar a Excel
                        </button>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <small class="text-muted">El reporte incluirá movimientos históricos desde 2019 hasta el año seleccionado.</small>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Card de Vista Previa de Stock Actual -->
    <div class="card card-info card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-eye"></i> Vista Previa - Stock Actual por Condición</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3 align-items-end">
                <div class="col-md-4">
                    <label>Filtrar Vista Previa:</label>
                    <select id="warehouse_type_preview" class="form-control">
                        <option value="">-- Todas las Plantas --</option>
                        @foreach($plantas as $planta)
                            <option value="{{ $planta->id }}">{{ $planta->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary btn-block" id="btnCargarPreview">
                        <i class="fas fa-sync-alt"></i> Cargar
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="tableStockPreview">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Tipo de Cilindro</th>
                            <th>Código</th>
                            <th class="text-center">Buenos</th>
                            <th class="text-center">Reparación</th>
                            <th class="text-center">Devolución</th>
                            <th class="text-center">Dañados</th>
                            <th class="text-center">Mínimo</th>
                            <th class="text-center bg-success">Stock Total</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyStockPreview">
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                Cargando datos...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    cargarStockPreview('');

    $('#btnCargarPreview').on('click', function() {
        cargarStockPreview($('#warehouse_type_preview').val());
    });

    function cargarStockPreview(warehouseTypeId) {
        const $tbody = $('#tbodyStockPreview');
        const $btn = $('#btnCargarPreview');
        
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '{{ route("dashboard.envases.stock") }}',
            method: 'GET',
            data: { warehouse_type_index: warehouseTypeId },
            success: function(resp) {
                const data = Array.isArray(resp) ? resp : (resp.data || []);
                $tbody.empty();

                if (!data.length) {
                    $tbody.html('<tr><td colspan="8" class="text-center text-muted">No hay datos disponibles</td></tr>');
                    return;
                }

                data.forEach(function(item) {
                    $tbody.append(`
                        <tr>
                            <td><strong>${item.name || ''}</strong></td>
                            <td><span class="badge badge-secondary">${item.code || ''}</span></td>
                            <td class="text-center">${formatNumber(item.stock_good)}</td>
                            <td class="text-center">${formatNumber(item.stock_repair)}</td>
                            <td class="text-center">${formatNumber(item.stock_return)}</td>
                            <td class="text-center">${formatNumber(item.stock_damaged)}</td>
                            <td class="text-center">${formatNumber(item.stock_minimum)}</td>
                            <td class="text-center bg-light"><strong>${formatNumber(item.stock)}</strong></td>
                        </tr>
                    `);
                });
            },
            error: function(xhr) {
                console.error('Error AJAX:', xhr.status, xhr.responseText);
                $tbody.html('<tr><td colspan="8" class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> Error al cargar datos</td></tr>');
            },
            complete: function() {
                $btn.prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Cargar');
            }
        });
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('es-PE').format(num || 0);
    }
});
</script>
@endpush


@push('styles')
<style>
    .bg-primary { background-color: #4e73df !important; }
    .bg-success { background-color: #1cc88a !important; }
    .card-outline { border-top: 3px solid #4e73df; }
    .table thead th { font-size: 0.85rem; text-transform: uppercase; }
</style>
@endpush