<template>
    <!--begin::Portlet-->
    <div class="kt-portlet">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Resultado
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-wrapper">
                    <div class="dropdown dropdown-inline">
                        <a href="#" @click.prevent="openModal()" class="btn btn-success disabled" id="add_service">
                            <i class="fa fa-plus"></i> Crear servicio
                        </a>
						<a href="#" @click.prevent="exportRecord()" class="btn btn-primary disabled" id="exportRecord">
                            <i class="fa fa-file-excel"></i> Exportar excel
                        </a>
                        <a href="/logistica/servicios" class="btn btn-info">Load</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="kt-portlet__body kt-portlet__body--fit" @click="manageActions">
            <!--begin: Datatable -->
            <div class="kt-datatable"></div>
            <!--end: Datatable -->
        </div>
    </div>
    <!--end::Portlet-->
</template>

<script>
    import EventBus from '../event-bus';
    export default {
        props: {
            url: {
                type: String,
                default: ''
            },
            url_detail: {
                type: String,
                default: ''
            },
            url_export_record: {
                type: String,
                default: ''
            },
            url_delete: {
                type: String,
                default: ''
            },
        },
        data() {
            return {
                datatable: undefined,
                company_id: '',
				flag_export: false,
            }
        },
        created() {

        },
        mounted() {
            EventBus.$on('show_table', function(response) {
                this.company_id = response.data;
                if ( this.datatable == undefined ) {
                    this.fillTableX();
                } else {
                    this.datatable.setDataSourceParam('company_id', this.company_id);
                    this.datatable.load();
                }

                this.datatable.on('kt-datatable--on-ajax-done', function() {
                    EventBus.$emit('loading', false);
                });

                document.getElementById('add_service').classList.remove('disabled');
                document.getElementById('exportRecord').classList.remove('disabled');
            }.bind(this));

            EventBus.$on('refresh_table', function() {
                this.datatable.load();
            }.bind(this));
        },
        methods: {
            openModal: function() {
                EventBus.$emit('create_modal');
            },
            exportRecord: function() {
                EventBus.$emit('loading', true);

                axios.post(this.url_export_record, {
                    company_id: this.company_id,
                }, {
                    responseType: 'blob',
                }).then(response => {
                    // console.log(response);
                    EventBus.$emit('loading', false);

                    let creation_date = new Date();
                    let date = creation_date.getDate();
                    let month = creation_date.getMonth();
                    let year = creation_date.getFullYear();
                    creation_date = year+'-'+month+'-'+date;

                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', 'reporte-servicios-'+creation_date+'.xlsx'); //or any other extension
                    document.body.appendChild(link);
                    link.click();
                }).catch(error => {
                    console.log(error);
                    console.log(error.response);
                });
            },
            fillTableX: function() {
                let vm = this;
                let token = document.head.querySelector('meta[name="csrf-token"]').content;

                this.datatable = $('.kt-datatable').KTDatatable({
                    // datasource definition
                    data: {
                        type: 'remote',
                        source: {
                            read: {
                                url: vm.url,
                                params: {
                                    _token: token,
                                    company_id: vm.company_id,
                                }
                            },
                        }
                    },

                    // layout definition
                    layout: {
                        scroll: true, // enable/disable datatable scroll both horizontal and vertical when needed.
                        height: 400,
                        footer: false // display/hide footer
                    },

                    // column sorting
                    sortable: true,
                    pagination: false,

                    translate: {
                        records: {
                            processing: 'Espere porfavor...',
                            noRecords: 'No hay registros'
                        },
                        toolbar: {
                            pagination: {
                                items: {
                                    default: {
                                        first: 'Primero',
                                        prev: 'Anterior',
                                        next: 'Siguiente',
                                        last: 'Último',
                                        more: 'Más páginas',
                                        input: 'Número de página',
                                        select: 'Seleccionar tamaño de página'
                                    },
                                    info: 'Mostrando {{start}} - {{end}} de {{total}} registros'
                                }
                            }
                        }
                    },

                    rows: {
                        autoHide: false,
                    },

                    // columns definition
                    columns: [
                    {
                            field: 'options',
                            title: 'Opciones',
                            sortable: false,
                            width: 80,
                            overflow: 'visible',
                            autoHide: false,
                            textAlign: 'right',
                            template: function(row) {
                                let actions = '<div class="actions">';
                                    actions += '<a href="#" class="edit btn btn-sm btn-clean btn-icon btn-icon-md" title="Editar">';
                                    actions += '<i class="la la-edit"></i>';
                                    actions += '</a>';
                                    actions += '<a href="#" class="delete btn btn-sm btn-clean btn-icon btn-icon-md" title="Eliminar">';
                                    actions += '<i class="la la-trash"></i>';
                                    actions += '</a>';
                                    actions += '</div>';
                                return actions;
                            },
                        },
                        {
                            field: 'service_id',
                            title: 'ID',
                            width: 50,
                            textAlign: 'center',
                        },
                        {
                            field: 'company_name',
                            title: 'Compañía',
                            width: 150,
                            textAlign: 'center'
                        },
                        {
                            field: 'sede_name',
                            title: 'Sede',
                            width: 80,
                            textAlign: 'center',
                        },
                        {
                            field: 'area_name',
                            title: 'Área',
                            width: 150,
                            textAlign: 'center',
                        },
                        {
                            field: 'service_code',
                            title: 'Code',
                            width: 80,
                            textAlign: 'center',
                        },
                        {
                            field: 'service_name',
                            title: 'Servicio',
                            width: 100,
                            textAlign: 'center',
                        },
						{
                            field: 'service_cost',
                            title: 'Costo',
                            width: 80,
                            textAlign: 'center',
                        },
						{
                            field: 'service_price',
                            title: 'Precio',
                            width: 80,
                            textAlign: 'center',
                        },
                        {
                            field: 'id',
                            title: '',
                            width: 0,
                            responsive: {
                                hidden: 'sm',
                                hidden: 'md',
                                hidden: 'lg',
                                hidden: 'xl'
                            }
                        },
                        
                    ]
                });

                this.datatable.columns('service_id').visible(false);
            },
            manageActions: function(event) {
                if ( $(event.target).hasClass('delete') ) {
                    event.preventDefault();
                    let id = $(event.target).parents('tr').find('td[data-field="service_id"] span').html();

                    Swal.fire({
                        title: '¡Cuidado!',
                        text: '¿Seguro que desea eliminar el registro?',
                        type: "warning",
                        heightAuto: false,
                        showCancelButton: true,
                        confirmButtonText: 'Sí',
                        cancelButtonText: 'No'
                    }).then(result => {
                        EventBus.$emit('loading', true);

                        if ( result.value ) {
                            axios.post(this.url_delete, {
                                id: id,
                            }).then(response => {
                                this.datatable.load();
                                EventBus.$emit('loading', false);
                                Swal.fire({
                                    title: '¡Ok!',
                                    text: 'Se ha eliminado correctamente',
                                    type: "success",
                                    heightAuto: false,
                                });
                            }).catch(error => {
                                console.log(error);
                                console.log(error.response);
                            });
                        } else if ( result.dismiss == Swal.DismissReason.cancel ) {
                            EventBus.$emit('loading', false);
                        }
                    });
                } else if ( $(event.target).hasClass('edit') ) {
                    event.preventDefault();
                    let id = $(event.target).parents('tr').find('td[data-field="service_id"] span').html();
                    EventBus.$emit('loading', true);

                    axios.post(this.url_detail, {
                        id: id,
                    }).then(response => {
                        EventBus.$emit('edit_modal', response.data);
                        EventBus.$emit('loading', false);
                        this.datatable.load();
                    }).catch(error => {
                        console.log(error);
                        console.log(error.response);
                    });
                }
            },
        }
    };
</script>