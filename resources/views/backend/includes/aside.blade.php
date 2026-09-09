<!-- begin:: Aside -->



<button class="kt-aside-close " id="kt_aside_close_btn"><i class="la la-close"></i></button>
<div class="kt-aside  kt-aside--fixed  kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop" id="kt_aside">
    <!-- begin:: Aside -->
    <div class="kt-aside__brand kt-grid__item " id="kt_aside_brand">
        <div class="kt-aside__brand-logo">
            <a href="{{ route('login') }}">


                @if (Auth::id() == 61)
                    <img alt="Logo" src="{{ asset('backend/img/blue_line_logo_oficial.jpeg') }}" />
                @else
                    <img alt="Logo" src="{{ asset('backend/img/logo-dashboard-2.png') }}" />
                @endif

            </a>
        </div>
        <div class="kt-aside__brand-tools">
            <button class="kt-aside__brand-aside-toggler" id="kt_aside_toggler">
                <span><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon id="Shape" points="0 0 24 0 24 24 0 24" />
                            <path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" id="Path-94" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999) " />
                            <path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999) " />
                        </g>
                    </svg></span>
                <span><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <polygon id="Shape" points="0 0 24 0 24 24 0 24" />
                            <path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" id="Path-94" fill="#000000" fill-rule="nonzero" />
                            <path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999) " />
                        </g>
                    </svg></span>
            </button>

            <!--
            <button class="kt-aside__brand-aside-toggler kt-aside__brand-aside-toggler--left" id="kt_aside_toggler"><span></span></button>
            -->
        </div>
    </div>
    <!-- end:: Aside -->



    <!-- begin:: Aside Menu -->
    <div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
        <div id="kt_aside_menu" class="kt-aside-menu " data-ktmenu-vertical="1" data-ktmenu-scroll="1" data-ktmenu-dropdown-timeout="500">
            <ul class="kt-menu__nav ">
                <li class="kt-menu__item" aria-haspopup="true">
                    <a href="#" class="kt-menu__link ">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon id="Bound" points="0 0 24 0 24 24 0 24" />
                                    <path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" id="Shape" fill="#000000" fill-rule="nonzero" />
                                    <path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" id="Path" fill="#000000" opacity="0.3" />
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">Dashboard</span>
                    </a>
                </li>
                @if(Auth::user()->id == 7 || Auth::user()->id == 45 || Auth::user()->id == 60 || Auth::user()->id == 46 || Auth::user()->id == 21 || Auth::user()->id == 44 || Auth::user()->id == 1 || Auth::user()->id == 67 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 42 || Auth::user()->id == 28 || Auth::user()->id == 17 )
                <li class="kt-menu__item kt-menu__item--submenu {{ ( strpos(url()->current(), '/facturacion/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect id="bound" x="0" y="0" width="24" height="24" />
                                    <rect id="Rectangle" fill="#000000" opacity="0.3" x="11.5" y="2" width="2" height="4" rx="1" />
                                    <rect id="Rectangle-Copy-3" fill="#000000" opacity="0.3" x="11.5" y="16" width="2" height="5" rx="1" />
                                    <path d="M15.493,8.044 C15.2143319,7.68933156 14.8501689,7.40750104 14.4005,7.1985 C13.9508311,6.98949895 13.5170021,6.885 13.099,6.885 C12.8836656,6.885 12.6651678,6.90399981 12.4435,6.942 C12.2218322,6.98000019 12.0223342,7.05283279 11.845,7.1605 C11.6676658,7.2681672 11.5188339,7.40749914 11.3985,7.5785 C11.2781661,7.74950085 11.218,7.96799867 11.218,8.234 C11.218,8.46200114 11.2654995,8.65199924 11.3605,8.804 C11.4555005,8.95600076 11.5948324,9.08899943 11.7785,9.203 C11.9621676,9.31700057 12.1806654,9.42149952 12.434,9.5165 C12.6873346,9.61150047 12.9723317,9.70966616 13.289,9.811 C13.7450023,9.96300076 14.2199975,10.1308324 14.714,10.3145 C15.2080025,10.4981676 15.6576646,10.7419985 16.063,11.046 C16.4683354,11.3500015 16.8039987,11.7268311 17.07,12.1765 C17.3360013,12.6261689 17.469,13.1866633 17.469,13.858 C17.469,14.6306705 17.3265014,15.2988305 17.0415,15.8625 C16.7564986,16.4261695 16.3733357,16.8916648 15.892,17.259 C15.4106643,17.6263352 14.8596698,17.8986658 14.239,18.076 C13.6183302,18.2533342 12.97867,18.342 12.32,18.342 C11.3573285,18.342 10.4263378,18.1741683 9.527,17.8385 C8.62766217,17.5028317 7.88033631,17.0246698 7.285,16.404 L9.413,14.238 C9.74233498,14.6433354 10.176164,14.9821653 10.7145,15.2545 C11.252836,15.5268347 11.7879973,15.663 12.32,15.663 C12.5606679,15.663 12.7949989,15.6376669 13.023,15.587 C13.2510011,15.5363331 13.4504991,15.4540006 13.6215,15.34 C13.7925009,15.2259994 13.9286662,15.0740009 14.03,14.884 C14.1313338,14.693999 14.182,14.4660013 14.182,14.2 C14.182,13.9466654 14.1186673,13.7313342 13.992,13.554 C13.8653327,13.3766658 13.6848345,13.2151674 13.4505,13.0695 C13.2161655,12.9238326 12.9248351,12.7908339 12.5765,12.6705 C12.2281649,12.5501661 11.8323355,12.420334 11.389,12.281 C10.9583312,12.141666 10.5371687,11.9770009 10.1255,11.787 C9.71383127,11.596999 9.34650161,11.3531682 9.0235,11.0555 C8.70049838,10.7578318 8.44083431,10.3968355 8.2445,9.9725 C8.04816568,9.54816454 7.95,9.03200304 7.95,8.424 C7.95,7.67666293 8.10199848,7.03700266 8.406,6.505 C8.71000152,5.97299734 9.10899753,5.53600171 9.603,5.194 C10.0970025,4.85199829 10.6543302,4.60183412 11.275,4.4435 C11.8956698,4.28516587 12.5226635,4.206 13.156,4.206 C13.9160038,4.206 14.6918294,4.34533194 15.4835,4.624 C16.2751706,4.90266806 16.9686637,5.31433061 17.564,5.859 L15.493,8.044 Z" id="Combined-Shape" fill="#000000" />
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">Facturación</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text">Facturación</span>
                                </span>
                            </li>



                            @if(Auth::user()->id == 7 || Auth::user()->id == 45 || Auth::user()->id == 46  || Auth::user()->id == 42 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.api.send_efact' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.api.send_efact') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">EFACT - Fact. - Bol. - Comp. de Percep.</span>
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->id == 7 || Auth::user()->id == 42 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ Route::currentRouteName() === 'dashboard.guide.efact' ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.guide.efact') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">EFACT - Guías Remisión</span>
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->id == 7 )
                            <li class="kt-menu__item {{ Route::currentRouteName() === 'dashboard.guide_transport.efact' ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.guide_transport.efact') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">EFACT - Guías Transportista</span>
                                </a>
                            </li>
                            @endif


                            @if(Auth::user()->id == 7 || Auth::user()->id == 60 || Auth::user()->id == 42 || Auth::user()->id == 44 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.boleta.send_ose' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.boleta.send_ose') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">EFACT - Resumen Boletas</span>
                                </a>
                            </li>
                            @endif


                            @if(Auth::user()->id == 7 || Auth::user()->id == 42 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.register_document_charge' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.register_document_charge') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">NOTAS DE CRÉDITO Y COMP. DE RETENCIÓN </span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 45 || Auth::user()->id == 46 || Auth::user()->id == 42 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.register_document_fact' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.register_document_fact') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Facturación Manual</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 42 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.collection_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.collection_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Registro de Cobranzas</span>
                                </a>
                            </li>

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.descuento_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.descuento_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Cancelación de Depositos Pendientes</span>
                                </a>
                            </li>

                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 42 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.register_voucher' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.register_voucher') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Registro de Vouchers</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 42 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.efective.register_efective' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.efective.register_efective') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Registro de Efectivo</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 42 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.efectives' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.efectives') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Reg. Efectivo</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 42 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.deposits' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.deposits') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Vouchers</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 42 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.liquidations_envasado' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.liquidations_envasado') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Liquidaciones Final</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 42 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.liquidation_preventa.index' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.liquidation_preventa.index') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Liquidacion Nuevo</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 42 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.liquidations_other' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.liquidations_other') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Liquidación de Cilindros</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 3 || Auth::user()->id == 9 ||Auth::user()->id == 42 || Auth::user()->id == 28)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.register_other_cash' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.register_other_cash') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Otros Ingresos de Caja</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 42 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.register_charge' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.register_charge') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Otros Egresos de Caja</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 42 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.un_document_report' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.un_document_report') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Egresos de Caja</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 42 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.liquidations' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.liquidations') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Liquidaciones</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 42 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.liquidations' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.liquidations') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Facturación entre Empresas</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 42 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.uncollected_document_report' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.uncollected_document_report') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Relación de Documentos Emitidos</span>
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->id == 7 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.fact_edit' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.fact_edit') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Modificación de Facturas</span>
                                </a>
                            </li>
                            @endif

                        </ul>
                    </div>
                </li>
                @endif

                @if ( Auth::user()->id == 16 || Auth::user()->id == 7 || Auth::user()->id == 19  || Auth::user()->id == 45  || Auth::user()->id == 46 || Auth::user()->id == 57)
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/comercial/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <rect fill="#000000" opacity="0.3" x="2" y="3" width="20" height="18" rx="2"></rect>
                                    <path d="M9.9486833,13.3162278 C9.81256925,13.7245699 9.43043041,14 9,14 L5,14 C4.44771525,14 4,13.5522847 4,13 C4,12.4477153 4.44771525,12 5,12 L8.27924078,12 L10.0513167,6.68377223 C10.367686,5.73466443 11.7274983,5.78688777 11.9701425,6.75746437 L13.8145063,14.1349195 L14.6055728,12.5527864 C14.7749648,12.2140024 15.1212279,12 15.5,12 L19,12 C19.5522847,12 20,12.4477153 20,13 C20,13.5522847 19.5522847,14 19,14 L16.118034,14 L14.3944272,17.4472136 C13.9792313,18.2776054 12.7550291,18.143222 12.5298575,17.2425356 L10.8627389,10.5740611 L9.9486833,13.3162278 Z" fill="#000000" fill-rule="nonzero"></path>
                                    <circle fill="#000000" opacity="0.3" cx="19" cy="6" r="1"></circle>
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">Casa del Gas</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text">Casa del Gas</span>
                                </span>
                            </li>

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.order_house.index' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.order_house.index') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Registro de Pedidos</span>
                                </a>
                            </li>

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.order_house' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.order_house') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Pedidos</span>
                                </a>
                            </li>
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.employee' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.employee') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Choferes</span>
                                </a>
                            </li>
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report_casa_gas.liquidations_channel' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report_casa_gas.liquidations_channel') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Lista de Ventas</span>
                                </a>
                            </li>

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report_casa_gas.liquidations_channel_total' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report_casa_gas.liquidations_channel_total') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Resumen de Ventas</span>
                                </a>
                            </li>

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.clients_casa_gas' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.clients_casa_gas') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Registro de Clientes</span>
                                </a>
                            </li>



                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.list_clients' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.list_clients') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Lista de Clientes</span>
                                </a>
                            </li>


                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.price_list' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.price_list') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Modificación de Precios</span>
                                </a>
                            </li>

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.portfolio_local.index' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.portfolio_local.index') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte Cartera Clientes Locales</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>
                @endif

                @if ( Auth::user()->id == 7 || Auth::user()->id == 61  || Auth::user()->id == 63 || Auth::user()->id == 64)
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/comercial/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <rect fill="#000000" opacity="0.3" x="2" y="3" width="20" height="18" rx="2"></rect>
                                    <path d="M9.9486833,13.3162278 C9.81256925,13.7245699 9.43043041,14 9,14 L5,14 C4.44771525,14 4,13.5522847 4,13 C4,12.4477153 4.44771525,12 5,12 L8.27924078,12 L10.0513167,6.68377223 C10.367686,5.73466443 11.7274983,5.78688777 11.9701425,6.75746437 L13.8145063,14.1349195 L14.6055728,12.5527864 C14.7749648,12.2140024 15.1212279,12 15.5,12 L19,12 C19.5522847,12 20,12.4477153 20,13 C20,13.5522847 19.5522847,14 19,14 L16.118034,14 L14.3944272,17.4472136 C13.9792313,18.2776054 12.7550291,18.143222 12.5298575,17.2425356 L10.8627389,10.5740611 L9.9486833,13.3162278 Z" fill="#000000" fill-rule="nonzero"></path>
                                    <circle fill="#000000" opacity="0.3" cx="19" cy="6" r="1"></circle>
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">BLUE LINE - FLOTA</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text">BLUE LINE - FLOTA</span>
                                </span>
                            </li>


                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.blue_plate' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.blue_plate') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Gestión de Flotas</span>
                                </a>
                            </li>
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.blue_driver' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.blue_driver') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Gestión de Choferes</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                @if ( Auth::user()->id == 7 || Auth::user()->id == 61  || Auth::user()->id == 63)
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/comercial/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <rect fill="#000000" opacity="0.3" x="2" y="3" width="20" height="18" rx="2"></rect>
                                    <path d="M9.9486833,13.3162278 C9.81256925,13.7245699 9.43043041,14 9,14 L5,14 C4.44771525,14 4,13.5522847 4,13 C4,12.4477153 4.44771525,12 5,12 L8.27924078,12 L10.0513167,6.68377223 C10.367686,5.73466443 11.7274983,5.78688777 11.9701425,6.75746437 L13.8145063,14.1349195 L14.6055728,12.5527864 C14.7749648,12.2140024 15.1212279,12 15.5,12 L19,12 C19.5522847,12 20,12.4477153 20,13 C20,13.5522847 19.5522847,14 19,14 L16.118034,14 L14.3944272,17.4472136 C13.9792313,18.2776054 12.7550291,18.143222 12.5298575,17.2425356 L10.8627389,10.5740611 L9.9486833,13.3162278 Z" fill="#000000" fill-rule="nonzero"></path>
                                    <circle fill="#000000" opacity="0.3" cx="19" cy="6" r="1"></circle>
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">BLUE LINE - CLIENTES</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text">BLUE LINE - CLIENTES</span>
                                </span>
                            </li>
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.blue_clients' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.blue_clients') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Gestión de Clientes</span>
                                </a>
                            </li>
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.blue_list_clients' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.blue_list_clients') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Clientes</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                </li>
                @endif

                <!-- creado el 17/09/2025, módulo para los mantenimientos de BL -->
                @if ( Auth::user()->id == 2 || Auth::user()->id==61)
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/comercial/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear" viewBox="0 0 16 16">
                            <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492M5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0"/>
                            <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115z"/>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">BLUE LINE - MANTENIMIENTOS</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text">BLUE LINE - MANTENIMIENTOS</span>
                                </span>
                            </li>
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.gestion.blue_maintenance' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.gestion.blue_maintenance') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Gestión de Válvulas</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif


                @if ( Auth::user()->id == 7 || Auth::user()->id == 61  || Auth::user()->id == 63)
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/comercial/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <rect fill="#000000" opacity="0.3" x="2" y="3" width="20" height="18" rx="2"></rect>
                                    <path d="M9.9486833,13.3162278 C9.81256925,13.7245699 9.43043041,14 9,14 L5,14 C4.44771525,14 4,13.5522847 4,13 C4,12.4477153 4.44771525,12 5,12 L8.27924078,12 L10.0513167,6.68377223 C10.367686,5.73466443 11.7274983,5.78688777 11.9701425,6.75746437 L13.8145063,14.1349195 L14.6055728,12.5527864 C14.7749648,12.2140024 15.1212279,12 15.5,12 L19,12 C19.5522847,12 20,12.4477153 20,13 C20,13.5522847 19.5522847,14 19,14 L16.118034,14 L14.3944272,17.4472136 C13.9792313,18.2776054 12.7550291,18.143222 12.5298575,17.2425356 L10.8627389,10.5740611 L9.9486833,13.3162278 Z" fill="#000000" fill-rule="nonzero"></path>
                                    <circle fill="#000000" opacity="0.3" cx="19" cy="6" r="1"></circle>
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">BLUE LINE - OPERACIONES</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text">BLUE LINE - OPERACIONES</span>
                                </span>
                            </li>
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.blue_clients' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.blue_clients') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Gestión de Rutas</span>
                                </a>
                            </li>
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.blue_list_clients' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.blue_list_clients') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Paradas Técnicas</span>
                                </a>
                            </li>
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.operations.blue_guides_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.operations.blue_guides_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Registro de Guía de Remisión Electrónica</span>
                                </a>
                            </li>

                        </ul>
                    </div>

                </li>
                @endif

                @if ( Auth::user()->id == 7 || Auth::user()->id == 61  || Auth::user()->id == 63)
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/comercial/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <rect fill="#000000" opacity="0.3" x="2" y="3" width="20" height="18" rx="2"></rect>
                                    <path d="M9.9486833,13.3162278 C9.81256925,13.7245699 9.43043041,14 9,14 L5,14 C4.44771525,14 4,13.5522847 4,13 C4,12.4477153 4.44771525,12 5,12 L8.27924078,12 L10.0513167,6.68377223 C10.367686,5.73466443 11.7274983,5.78688777 11.9701425,6.75746437 L13.8145063,14.1349195 L14.6055728,12.5527864 C14.7749648,12.2140024 15.1212279,12 15.5,12 L19,12 C19.5522847,12 20,12.4477153 20,13 C20,13.5522847 19.5522847,14 19,14 L16.118034,14 L14.3944272,17.4472136 C13.9792313,18.2776054 12.7550291,18.143222 12.5298575,17.2425356 L10.8627389,10.5740611 L9.9486833,13.3162278 Z" fill="#000000" fill-rule="nonzero"></path>
                                    <circle fill="#000000" opacity="0.3" cx="19" cy="6" r="1"></circle>
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">BLUE LINE - ADMINISTRACIÓN</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text">BLUE LINE - ADMINISTRACIÓN</span>
                                </span>
                            </li>
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.blue_clients' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.blue_clients') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Gestión de Anticipos</span>
                                </a>
                            </li>
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.blue_list_clients' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.blue_list_clients') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Anticipos</span>
                                </a>
                            </li>
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.operations.blue_guides_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.operations.blue_guides_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Liquidación de Rutas</span>
                                </a>
                            </li>
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.operations.blue_guides_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.operations.blue_guides_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Envío Efact Facturas</span>
                                </a>
                            </li>
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.blue_register_document_charge' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.blue_register_document_charge') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Generación de Documentos</span>
                                </a>
                            </li>
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.blue_uncollected_document_report' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.blue_uncollected_document_report') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Relación de Documentos Emitidos</span>
                                </a>
                            </li>

                        </ul>
                    </div>

                </li>
                @endif


                @if ( Auth::user()->id == 7 || Auth::user()->id == 67 || Auth::user()->id == 4 || Auth::user()->id == 15 || Auth::user()->id == 11  || Auth::user()->id == 5 || Auth::user()->id == 17)
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/logistica/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path d="M10,4 L21,4 C21.5522847,4 22,4.44771525 22,5 L22,7 C22,7.55228475 21.5522847,8 21,8 L10,8 C9.44771525,8 9,7.55228475 9,7 L9,5 C9,4.44771525 9.44771525,4 10,4 Z M10,10 L21,10 C21.5522847,10 22,10.4477153 22,11 L22,13 C22,13.5522847 21.5522847,14 21,14 L10,14 C9.44771525,14 9,13.5522847 9,13 L9,11 C9,10.4477153 9.44771525,10 10,10 Z M10,16 L21,16 C21.5522847,16 22,16.4477153 22,17 L22,19 C22,19.5522847 21.5522847,20 21,20 L10,20 C9.44771525,20 9,19.5522847 9,19 L9,17 C9,16.4477153 9.44771525,16 10,16 Z" fill="#000000" />
                                    <rect fill="#000000" opacity="0.3" x="2" y="4" width="5" height="16" rx="1" />
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">Osinergmin</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text">Osinergmin</span>
                                </span>
                            </li>
                            @if(Auth::user()->id == 7 || Auth::user()->id == 4 || Auth::user()->id == 67 || Auth::user()->id == 15 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 5 || Auth::user()->id == 17)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.osinergmin' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.osinergmin') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte DJ Mensual Osinergmin</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif


                @if ( Auth::user()->id == 7 || Auth::user()->id == 60 || Auth::user()->id == 72 || Auth::user()->id == 66 ||  Auth::user()->id == 15 || Auth::user()->id == 1  || Auth::user()->id == 51 || Auth::user()->id == 52 || Auth::user()->id == 21 || Auth::user()->id == 44 || Auth::user()->id == 2 || Auth::user()->id == 4 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 5 || Auth::user()->id == 17 || Auth::user()->id == 30 || Auth::user()->id==45 || Auth::user()->id == 53 || Auth::user()->id == 54 || Auth::user()->id == 55 || Auth::user()->id == 56 || Auth::user()->id == 67 || Auth::user()->id == 11  || Auth::user()->id == 62)
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/logistica/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path d="M10,4 L21,4 C21.5522847,4 22,4.44771525 22,5 L22,7 C22,7.55228475 21.5522847,8 21,8 L10,8 C9.44771525,8 9,7.55228475 9,7 L9,5 C9,4.44771525 9.44771525,4 10,4 Z M10,10 L21,10 C21.5522847,10 22,10.4477153 22,11 L22,13 C22,13.5522847 21.5522847,14 21,14 L10,14 C9.44771525,14 9,13.5522847 9,13 L9,11 C9,10.4477153 9.44771525,10 10,10 Z M10,16 L21,16 C21.5522847,16 22,16.4477153 22,17 L22,19 C22,19.5522847 21.5522847,20 21,20 L10,20 C9.44771525,20 9,19.5522847 9,19 L9,17 C9,16.4477153 9.44771525,16 10,16 Z" fill="#000000" />
                                    <rect fill="#000000" opacity="0.3" x="2" y="4" width="5" height="16" rx="1" />
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">Logística</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text">Logística</span>
                                </span>
                            </li>
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66  || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70||  Auth::user()->id == 11  || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ Route::currentRouteName() === 'dashboard.logistics.index' ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.logistics.index') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">REGISTRO DE ORDEN DE COMPRA</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66   || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70||  Auth::user()->id == 11  || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41)
                            <li class="kt-menu__item {{ Route::currentRouteName() === 'dashboard.logistics.report.order.index' ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.logistics.report.order.index') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">REPORTE DE ORDEN DE COMPRA</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7  || Auth::user()->id == 1 || Auth::user()->id == 5)
                            <li class="kt-menu__item {{ Route::currentRouteName() === 'dashboard.guide_edit' ? 'kt-menu__item--active' : '' }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.guide_edit') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Modificación de Guías</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 || Auth::user()->id == 72   || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70||  Auth::user()->id == 11  || Auth::user()->id == 1 || Auth::user()->id == 53 || Auth::user()->id==54 || Auth::user()->id == 55|| Auth::user()->id==56 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 || Auth::user()->id == 62)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.logistics.stock_log_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.logistics.stock_log_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Registro Movimiento de Existencias</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66  || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70||  Auth::user()->id == 11  || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.logistics.inventories' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.logistics.inventories') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Inventario</span>
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 || Auth::user()->id == 72  || Auth::user()->id == 60  || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70||  Auth::user()->id == 11  || Auth::user()->id == 1 || Auth::user()->id == 44 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.articles_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.articles_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Gestión de Bienes</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66  || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70||  Auth::user()->id == 11  || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.services' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.services') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Gestión de Servicios</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66  || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70||  Auth::user()->id == 11  || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.classifications' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.classifications') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Clasificaciones</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66  || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70||  Auth::user()->id == 11  || Auth::user()->id == 1 || Auth::user()->id == 51 || Auth::user()->id == 52 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 || Auth::user()->id == 2)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.providers' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.providers') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Proveedores</span>
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->id == 7 || Auth::user()->id == 72 || Auth::user()->id == 66  || Auth::user()->id == 60  || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70||  Auth::user()->id == 11  || Auth::user()->id == 15 ||  Auth::user()->id == 1 || Auth::user()->id == 44 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 || Auth::user()->id == 17 || Auth::user()->id==45)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.movements' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.movements') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Movimiento de Existencias</span>
                                </a>
                            </li>
                        @endif

                        </ul>
                    </div>
                </li>
                @endif


                @if ( Auth::user()->id == 7 || Auth::user()->id == 65)
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/beta/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path d="M10,4 L21,4 C21.5522847,4 22,4.44771525 22,5 L22,7 C22,7.55228475 21.5522847,8 21,8 L10,8 C9.44771525,8 9,7.55228475 9,7 L9,5 C9,4.44771525 9.44771525,4 10,4 Z M10,10 L21,10 C21.5522847,10 22,10.4477153 22,11 L22,13 C22,13.5522847 21.5522847,14 21,14 L10,14 C9.44771525,14 9,13.5522847 9,13 L9,11 C9,10.4477153 9.44771525,10 10,10 Z M10,16 L21,16 C21.5522847,16 22,16.4477153 22,17 L22,19 C22,19.5522847 21.5522847,20 21,20 L10,20 C9.44771525,20 9,19.5522847 9,19 L9,17 C9,16.4477153 9.44771525,16 10,16 Z" fill="#000000" />
                                    <rect fill="#000000" opacity="0.3" x="2" y="4" width="5" height="16" rx="1" />
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">DESPACHO GLP</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <!--Contenedor de listas-->
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text"></span>
                                </span>
                            </li>
                            <!--Contenedor de submódulo 1-->
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.operations.plant_order_cor_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.operations.plant_order_cor_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">COORDINADOR</span>
                                </a>
                            </li>

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.operations.plant_order_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.operations.plant_order_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">REGISTRO DE PEDIDOS</span>
                                </a>
                            </li>

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.operations.partition_guide_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.operations.partition_guide_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">GUÍAS</span>
                                </a>
                            </li>

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.operations.vigilancia_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.operations.vigilancia_register') }}" target="_blank" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">VIGILANCIA</span>
                                </a>
                            </li>

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.operations.reception_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.operations.reception_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">RECEPCION</span>
                                </a>
                            </li>

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.operations.guide_change' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.operations.guide_change') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">REGISTRO DE CAMBIOS</span>
                                </a>
                            </li>

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.constancy_guide.report' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.constancy_guide.report') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">GUIAS - CONSTANCIA DE PESOS Y MEDIDAS</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>
                @endif

                @if ( Auth::user()->id == 7 || Auth::user()->id == 3 || Auth::user()->id == 66 || Auth::user()->id == 9 || Auth::user()->id == 42 || Auth::user()->id == 17 || Auth::user()->id == 10 || Auth::user()->id == 19 || Auth::user()->id == 60 || Auth::user()->id == 44 || Auth::user()->id == 51 || Auth::user()->id == 52 || Auth::user()->id == 16 || Auth::user()->id == 48 || Auth::user()->id == 21 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 15 || Auth::user()->id == 10 )
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/operaciones/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path d="M17,20 L17,12 C17,9.23857625 14.7614237,7 12,7 C9.23857625,7 7,9.23857625 7,12 L7,20 L5,20 C3.8954305,20 3,19.1045695 3,18 L3,6 C3,4.8954305 3.8954305,4 5,4 L19,4 C20.1045695,4 21,4.8954305 21,6 L21,18 C21,19.1045695 20.1045695,20 19,20 L17,20 Z" fill="#000000" opacity="0.3" />
                                    <path d="M12.9717525,11.7005668 C12.8097937,13.3201542 12.3239175,14.1866868 11.5141238,14.3001646 C11.5141238,14.3001646 12.2429381,11.4576287 11.2711857,10 C11.2711857,10 11.1681401,11.5618236 10.126941,13.4359819 C9.63887975,14.3144921 9.08474261,14.9067082 9.08474261,16.0734529 C9.08474261,17.7393714 10.7908674,18.6003292 12.002779,18.6003292 C13.2146906,18.6003292 14.9152574,18.0172577 14.9152574,15.9765075 C14.9152574,15.1373628 14.2674224,13.7120493 12.9717525,11.7005668 Z" fill="#000000" />
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">Control de GLP y Cilindros</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            @if(Auth::user()->id == 7 || Auth::user()->id == 19 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 || Auth::user()->id == 15 )
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text">Control de GLP y Cilindros</span>
                                </span>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 || Auth::user()->id == 19 || Auth::user()->id == 1 || Auth::user()->id == 48 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.operations.guides_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.operations.guides_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Registro de Guías de Remisión</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.guide_edit' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.guide_edit') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Edición de Guías de Remisión</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 || Auth::user()->id == 19 || Auth::user()->id == 48 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.guias_electronic.report' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.guias_electronic.report') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Impresión de Guías Electrónicas</span>
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 || Auth::user()->id == 19 || Auth::user()->id == 48 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.preventa.report' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.preventa.report') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">PDF PREVENTA</span>
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 || Auth::user()->id == 19 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.operations.operations_part' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.operations.operations_part') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Impresión de Guías Físicas</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 67 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 15 || Auth::user()->id == 51 || Auth::user()->id == 52)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.stores' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.stores') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Gestión de Almacén</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66  || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70||  Auth::user()->id == 11  || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.operations.inventories' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.operations.inventories') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Inventario de planta diario</span>
                                </a>
                            </li>
                            @endif


                            @if(Auth::user()->id == 7 || Auth::user()->id == 66  || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70||  Auth::user()->id == 11  || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.envases.index' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.envases.index') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Masa</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66  || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70||  Auth::user()->id == 11  || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.cylinder' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.cylinder') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Despacho y Retorno

                                    </span>
                                </a>
                            </li>
                            @endif






                            @if(Auth::user()->id == 7 || Auth::user()->id == 17 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 51 || Auth::user()->id == 52)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.vehicle_plate' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.vehicle_plate') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Placas vehiculares</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 || Auth::user()->id == 9 || Auth::user()->id == 42 || Auth::user()->id == 17 || Auth::user()->id == 51 || Auth::user()->id == 52 || Auth::user()->id == 19 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.employee' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.employee') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Choferes</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 || Auth::user()->id == 19 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.operations.guides_return' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.operations.guides_return') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Retorno de Guías de Remisión</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 || Auth::user()->id == 19 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.stock_seek_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.stock_seek_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Guías Control Cilindros</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 || Auth::user()->id == 3 || Auth::user()->id == 19 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 25 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.commercial.guides_commercial' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.commercial.guides_commercial') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Listado de Guías</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 || Auth::user()->id == 19 || Auth::user()->id == 1 || Auth::user()->id == 25 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.movements_final' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.movements_final') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Guías</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 || Auth::user()->id == 19 || Auth::user()->id == 1 || Auth::user()->id == 25 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.guides_seek' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.guides_seek') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Buscador de Guías</span>
                                </a>
                            </li>
                            @endif
                            <!--  <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.warehouse_part' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.warehouse_part') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Parte de Almacén</span>
                                </a>
                            </li>-->

                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 || Auth::user()->id == 17  || Auth::user()->id == 10  || Auth::user()->id == 60 || Auth::user()->id == 19 || Auth::user()->id == 44 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.operations.guides_glp_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.operations.guides_glp_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Compras GLP</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 ||  Auth::user()->id == 17  || Auth::user()->id == 10  || Auth::user()->id == 60 || Auth::user()->id == 21 || Auth::user()->id == 44 || Auth::user()->id == 19 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.stock_sales_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.stock_sales_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Compras GLP</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 || Auth::user()->id == 17 || Auth::user()->id == 60 || Auth::user()->id == 10 || Auth::user()->id == 19 || Auth::user()->id == 44 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.stock_sales_resumen' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.stock_sales_resumen') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Resumen de Compras GLP</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 || Auth::user()->id == 17 || Auth::user()->id == 19  || Auth::user()->id == 10  || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.operations.stock_glp_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.operations.stock_glp_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Abastecimiento de GLP</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 10  || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 21 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.terminals' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.terminals') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Abastecimientos </span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 10  || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 21 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.partition' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.partition') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte Análisis Costo</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 || Auth::user()->id == 17 || Auth::user()->id == 19  || Auth::user()->id == 10  || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.days_glp' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.days_glp') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Costo GLP</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 17 || Auth::user()->id == 19  || Auth::user()->id == 10  || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.budget' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.budget') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Registro de Presupuestos</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.operations.stock_plant_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.operations.stock_plant_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Abastecimiento de Planta</span>
                                </a>
                            </li>
                            @endif

                        </ul>
                    </div>
                </li>
                @endif
                @if ( Auth::user()->id == 7 || Auth::user()->id == 71 || Auth::user()->id == 43 || Auth::user()->id ==49 || Auth::user()->id ==50 || Auth::user()->id == 1 || Auth::user()->id == 4 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 28 || Auth::user()->id == 19 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 29 || Auth::user()->id == 7 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 8  || Auth::user()->id == 58|| Auth::user()->id == 59 || Auth::user()->id == 45)
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/comercial/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <rect fill="#000000" opacity="0.3" x="2" y="3" width="20" height="18" rx="2"></rect>
                                    <path d="M9.9486833,13.3162278 C9.81256925,13.7245699 9.43043041,14 9,14 L5,14 C4.44771525,14 4,13.5522847 4,13 C4,12.4477153 4.44771525,12 5,12 L8.27924078,12 L10.0513167,6.68377223 C10.367686,5.73466443 11.7274983,5.78688777 11.9701425,6.75746437 L13.8145063,14.1349195 L14.6055728,12.5527864 C14.7749648,12.2140024 15.1212279,12 15.5,12 L19,12 C19.5522847,12 20,12.4477153 20,13 C20,13.5522847 19.5522847,14 19,14 L16.118034,14 L14.3944272,17.4472136 C13.9792313,18.2776054 12.7550291,18.143222 12.5298575,17.2425356 L10.8627389,10.5740611 L9.9486833,13.3162278 Z" fill="#000000" fill-rule="nonzero"></path>
                                    <circle fill="#000000" opacity="0.3" cx="19" cy="6" r="1"></circle>
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">Comercial</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text">Comercial</span>
                                </span>
                            </li>


                            @if(Auth::user()->id == 7 || Auth::user()->id == 43 || Auth::user()->id ==49 || Auth::user()->id ==50 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11 || Auth::user()->id == 57 || Auth::user()->id == 58|| Auth::user()->id == 59)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.liquidations_channel_total' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.liquidations_channel_total') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Resumen de Ventas</span>
                                </a>
                            </li>
                            @endif


                            @if(Auth::user()->id == 7 || Auth::user()->id == 43 || Auth::user()->id ==49 || Auth::user()->id ==50 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11 || Auth::user()->id == 57 || Auth::user()->id == 58|| Auth::user()->id == 59)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.proyection' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.proyection') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Rutas </span>
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->id == 7 || Auth::user()->id == 71 || Auth::user()->id == 43 || Auth::user()->id == 49 || Auth::user()->id ==50 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11 || Auth::user()->id == 57 || Auth::user()->id == 58|| Auth::user()->id == 59)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.liquidations_channel' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.liquidations_channel') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Lista de Ventas</span>
                                </a>
                            </li>
                            @endif

                            <!--
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.days_sale' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.days_sale') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Avance de Venta Resumen</span>
                                </a>
                            </li>
                        -->
                            @if(Auth::user()->id == 7 || Auth::user()->id == 45 || Auth::user()->id == 46 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 57 || Auth::user()->id == 58|| Auth::user()->id == 59)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.sales-report.budgets' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.sales-report.budgets') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Administración de Presupuestos</span>
                                </a>
                            </li>
                            @endif


                            @if(Auth::user()->id == 7 || Auth::user()->id == 45 || Auth::user()->id == 46 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 57 || Auth::user()->id == 58|| Auth::user()->id == 59)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.clients' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.clients') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Registro de Clientes</span>
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->id == 7 || Auth::user()->id == 45 || Auth::user()->id == 43 || Auth::user()->id == 49 || Auth::user()->id ==50 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 || Auth::user()->id == 57 || Auth::user()->id == 58|| Auth::user()->id == 59)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.list_clients' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.list_clients') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Lista de Clientes</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 45 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.price_list' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.price_list') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Modificación de Precios</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 35  || Auth::user()->id == 45 || Auth::user()->id == 46 || Auth::user()->id == 43 || Auth::user()-> id==49 || Auth::user()-> id==50 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 || Auth::user()->id == 57 || Auth::user()->id == 58|| Auth::user()->id == 59)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.price_list' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.price_list') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Lista de Precios</span>
                                </a>
                            </li>
                            @endif

                            <!--    <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.commercial.commercial_channel' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                            <a href="{{ route('dashboard.commercial.commercial_channel') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Consulta de Ventas Detallado</span>
                                </a>
                            </li>
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.liquidations_channel_total' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.liquidations_channel_total') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Consulta de Ventas Resumido</span>
                                </a>
                            </li>

                            @if(Auth::user()->id == 7 || Auth::user()->id == 43 || Auth::user()->id == 49 || Auth::user()->id ==50 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 || Auth::user()->id == 57 || Auth::user()->id == 58|| Auth::user()->id == 59)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.liquidations_comercial' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.liquidations_comercial') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Perdidos Mensual</span>
                                </a>
                            </li>
                            @endif-->

                            @if(Auth::user()->id == 7 || Auth::user()->id == 43 || Auth::user()->id == 49 || Auth::user()->id ==50 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 || Auth::user()->id == 57 || Auth::user()->id == 58|| Auth::user()->id == 59)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.liquidations_six_comercial' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.liquidations_six_comercial') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Cartera Envasado</span>
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->id == 7 || Auth::user()->id == 43 || Auth::user()->id == 49 || Auth::user()->id ==50 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 || Auth::user()->id == 57 || Auth::user()->id == 58|| Auth::user()->id == 59)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.vdc_six_comercial' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.vdc_six_comercial') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Cartera Comercio</span>
                                </a>
                            </li>
                            @endif


                            @if(Auth::user()->id == 7 || Auth::user()->id == 43 || Auth::user()->id == 49 || Auth::user()->id ==50 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 || Auth::user()->id == 57 || Auth::user()->id == 58|| Auth::user()->id == 59)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.granel_six_comercial' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.granel_six_comercial') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Cartera Granel</span>
                                </a>
                            </li>
                            @endif



                            @if(Auth::user()->id == 7 || Auth::user()->id == 43 || Auth::user()->id == 49 || Auth::user()->id ==50 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 || Auth::user()->id == 57 || Auth::user()->id == 58|| Auth::user()->id == 59)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.clients_six_comercial' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.clients_six_comercial') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Clientes Nuevos Envasado</span>
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->id == 7 || Auth::user()->id == 43 || Auth::user()->id == 49 || Auth::user()->id ==50 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 || Auth::user()->id == 57 || Auth::user()->id == 58|| Auth::user()->id == 59)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.sale_presupuesto' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.sale_presupuesto') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">PRESUPUESTO MENSUAL</span>
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->id == 7 || Auth::user()->id == 43 || Auth::user()->id == 49 || Auth::user()->id ==50 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 41 || Auth::user()->id == 57 || Auth::user()->id == 58)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.sale_day_report.index' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.sale_day_report.index') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">REPORTE RESUMEN VENTA DIARIA</span>
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->id == 7 || Auth::user()->id == 43 || Auth::user()->id == 49 || Auth::user()->id ==50 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 || Auth::user()->id == 57 || Auth::user()->id == 58|| Auth::user()->id == 59)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.list_price_report.index' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.list_price_report.index') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">REPORTE DE PRECIOS</span>
                                </a>
                            </li>
                            @endif




                        </ul>
                    </div>
                </li>
                @endif
                @if ( Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 42 || Auth::user()->id == 3 || Auth::user()->id == 28 || Auth::user()->id == 9 || Auth::user()->id == 10)
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/creditos/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <polygon fill="#000000" opacity="0.3" points="12 20.0218549 8.47346039 21.7286168 6.86905972 18.1543453 3.07048824 17.1949849 4.13894342 13.4256452 1.84573388 10.2490577 5.08710286 8.04836581 5.3722735 4.14091196 9.2698837 4.53859595 12 1.72861679 14.7301163 4.53859595 18.6277265 4.14091196 18.9128971 8.04836581 22.1542661 10.2490577 19.8610566 13.4256452 20.9295118 17.1949849 17.1309403 18.1543453 15.5265396 21.7286168" />
                                    <polygon fill="#000000" points="14.0890818 8.60255815 8.36079737 14.7014391 9.70868621 16.049328 15.4369707 9.950447" />
                                    <path d="M10.8543431,9.1753866 C10.8543431,10.1252593 10.085524,10.8938719 9.13585777,10.8938719 C8.18793881,10.8938719 7.41737243,10.1252593 7.41737243,9.1753866 C7.41737243,8.22551387 8.18793881,7.45690126 9.13585777,7.45690126 C10.085524,7.45690126 10.8543431,8.22551387 10.8543431,9.1753866" fill="#000000" opacity="0.3" />
                                    <path d="M14.8641422,16.6221564 C13.9162233,16.6221564 13.1456569,15.8535438 13.1456569,14.9036711 C13.1456569,13.9520555 13.9162233,13.1851857 14.8641422,13.1851857 C15.8138085,13.1851857 16.5826276,13.9520555 16.5826276,14.9036711 C16.5826276,15.8535438 15.8138085,16.6221564 14.8641422,16.6221564 Z" fill="#000000" opacity="0.3" />
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">Creditos y Cobranzas</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text">Créditos y Cobranzas</span>
                                </span>
                            </li>

                            <!--  <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.clients' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.clients') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Clientes</span>
                                </a>
                            </li> -->
                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.checking_account_report' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.checking_account_report') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Cuentas Corrientes Clientes</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.credits.report.liquidations_credits' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.credits.report.liquidations_credits') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Registro de liquidaciones detallado </span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.pending_document_int_report' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.pending_document_int_report') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Documentos Pendientes Internos</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.descuento' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.descuento') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Depositos Pendientes</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 42 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.collection_report' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.collection_report') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Relación de Cobranzas</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.pending_document_report' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.pending_document_report') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Relación de Documentos Pendientes</span>
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.credit_total' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.credit_total') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte Consolidado de Creditos</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.uncollected_document_report' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.uncollected_document_report') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Relación de Documentos Emitidos</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 3 || Auth::user()->id == 9 ||Auth::user()->id == 42 || Auth::user()->id == 28)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.other_cash' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.other_cash') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte - Otros Ingresos de Caja</span>
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->id == 7 || Auth::user()->id == 3 || Auth::user()->id == 9 ||Auth::user()->id == 42 || Auth::user()->id == 28)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.credit_history' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.credit_history') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte - Crédito Histórico</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif
                @if ( Auth::user()->id == 7 ||  Auth::user()->id == 1 ||  Auth::user()->id == 15  || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 11  || Auth::user()->id == 45 || Auth::user()->id == 46 || Auth::user()->id == 1 || Auth::user()->id == 60 || Auth::user()->id == 28 || Auth::user()->id == 44 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 5 || Auth::user()->id == 4 || Auth::user()->id == 21 || Auth::user()->id == 10 || Auth::user()->id == 17)
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/contabilidad/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <rect fill="#000000" opacity="0.3" x="7" y="4" width="10" height="4" />
                                    <path d="M7,2 L17,2 C18.1045695,2 19,2.8954305 19,4 L19,20 C19,21.1045695 18.1045695,22 17,22 L7,22 C5.8954305,22 5,21.1045695 5,20 L5,4 C5,2.8954305 5.8954305,2 7,2 Z M8,12 C8.55228475,12 9,11.5522847 9,11 C9,10.4477153 8.55228475,10 8,10 C7.44771525,10 7,10.4477153 7,11 C7,11.5522847 7.44771525,12 8,12 Z M8,16 C8.55228475,16 9,15.5522847 9,15 C9,14.4477153 8.55228475,14 8,14 C7.44771525,14 7,14.4477153 7,15 C7,15.5522847 7.44771525,16 8,16 Z M12,12 C12.5522847,12 13,11.5522847 13,11 C13,10.4477153 12.5522847,10 12,10 C11.4477153,10 11,10.4477153 11,11 C11,11.5522847 11.4477153,12 12,12 Z M12,16 C12.5522847,16 13,15.5522847 13,15 C13,14.4477153 12.5522847,14 12,14 C11.4477153,14 11,14.4477153 11,15 C11,15.5522847 11.4477153,16 12,16 Z M16,12 C16.5522847,12 17,11.5522847 17,11 C17,10.4477153 16.5522847,10 16,10 C15.4477153,10 15,10.4477153 15,11 C15,11.5522847 15.4477153,12 16,12 Z M16,16 C16.5522847,16 17,15.5522847 17,15 C17,14.4477153 16.5522847,14 16,14 C15.4477153,14 15,14.4477153 15,15 C15,15.5522847 15.4477153,16 16,16 Z M16,20 C16.5522847,20 17,19.5522847 17,19 C17,18.4477153 16.5522847,18 16,18 C15.4477153,18 15,18.4477153 15,19 C15,19.5522847 15.4477153,20 16,20 Z M8,18 C7.44771525,18 7,18.4477153 7,19 C7,19.5522847 7.44771525,20 8,20 L12,20 C12.5522847,20 13,19.5522847 13,19 C13,18.4477153 12.5522847,18 12,18 L8,18 Z M7,4 L7,8 L17,8 L17,4 L7,4 Z" fill="#000000" />
                                </g>
                            </svg>

                        </span>
                        <span class="kt-menu__link-text">Contabilidad</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text">Contabilidad</span>
                                </span>
                            </li>
                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 44 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 || Auth::user()->id == 17 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.accounting.index' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.accounting.index') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Lista de Precios Boleteo</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 60 || Auth::user()->id == 44 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 || Auth::user()->id == 17 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.massive_generation' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.massive_generation') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Generación masiva de comprobantes</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 4 || Auth::user()->id == 60)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.boleteo' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.boleteo') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">BOLETEO - Modificación de Precio</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 44 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.transportist_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.transportist_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Guias de Transportistas</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 60 || Auth::user()->id == 44 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.sales_volume' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.sales_volume') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Volumen de Boletas</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 44 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.cordia_volume' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.cordia_volume') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Volumen de Boletas Cordia</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 60 || Auth::user()->id == 15 ||   Auth::user()->id == 1 || Auth::user()->id == 44 || Auth::user()->id == 28 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.facturations_sales_volume' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.facturations_sales_volume') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Facturación Volumen</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 44 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.facturations_total_sales_volume' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.facturations_total_sales_volume') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Facturación Volumen Resumido</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 45 || Auth::user()->id == 46 || Auth::user()->id == 44 || Auth::user()->id == 28 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.facturations_sales' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.facturations_sales') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Facturas Emitidas</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 44 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.contabilidad_boletas' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.contabilidad_boletas') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Boletas Emitidas</span>
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 44 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.contabilidad_detail_total' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.contabilidad_detail_total') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte Contable de Ventas Consolidado </span>
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 44 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.sales-register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.sales-register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Percepciones</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif
                <!--   <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/comprasglp/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <rect fill="#000000" opacity="0.3" x="2" y="3" width="20" height="18" rx="2"></rect>
                                    <path d="M9.9486833,13.3162278 C9.81256925,13.7245699 9.43043041,14 9,14 L5,14 C4.44771525,14 4,13.5522847 4,13 C4,12.4477153 4.44771525,12 5,12 L8.27924078,12 L10.0513167,6.68377223 C10.367686,5.73466443 11.7274983,5.78688777 11.9701425,6.75746437 L13.8145063,14.1349195 L14.6055728,12.5527864 C14.7749648,12.2140024 15.1212279,12 15.5,12 L19,12 C19.5522847,12 20,12.4477153 20,13 C20,13.5522847 19.5522847,14 19,14 L16.118034,14 L14.3944272,17.4472136 C13.9792313,18.2776054 12.7550291,18.143222 12.5298575,17.2425356 L10.8627389,10.5740611 L9.9486833,13.3162278 Z" fill="#000000" fill-rule="nonzero"></path>
                                    <circle fill="#000000" opacity="0.3" cx="19" cy="6" r="1"></circle>
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">Control GLP</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text">Control GLP</span>
                                </span>
                            </li>
                      </ul>
                    </div>
                </li> -->



                <!--Menú de Finanzas-->
                @if ( Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 8 || Auth::user()->id == 10 )
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/administracion/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <rect fill="#000000" opacity="0.3" x="11.5" y="2" width="2" height="4" rx="1" />
                                    <rect fill="#000000" opacity="0.3" x="11.5" y="16" width="2" height="5" rx="1" />
                                    <path d="M15.493,8.044 C15.2143319,7.68933156 14.8501689,7.40750104 14.4005,7.1985 C13.9508311,6.98949895 13.5170021,6.885 13.099,6.885 C12.8836656,6.885 12.6651678,6.90399981 12.4435,6.942 C12.2218322,6.98000019 12.0223342,7.05283279 11.845,7.1605 C11.6676658,7.2681672 11.5188339,7.40749914 11.3985,7.5785 C11.2781661,7.74950085 11.218,7.96799867 11.218,8.234 C11.218,8.46200114 11.2654995,8.65199924 11.3605,8.804 C11.4555005,8.95600076 11.5948324,9.08899943 11.7785,9.203 C11.9621676,9.31700057 12.1806654,9.42149952 12.434,9.5165 C12.6873346,9.61150047 12.9723317,9.70966616 13.289,9.811 C13.7450023,9.96300076 14.2199975,10.1308324 14.714,10.3145 C15.2080025,10.4981676 15.6576646,10.7419985 16.063,11.046 C16.4683354,11.3500015 16.8039987,11.7268311 17.07,12.1765 C17.3360013,12.6261689 17.469,13.1866633 17.469,13.858 C17.469,14.6306705 17.3265014,15.2988305 17.0415,15.8625 C16.7564986,16.4261695 16.3733357,16.8916648 15.892,17.259 C15.4106643,17.6263352 14.8596698,17.8986658 14.239,18.076 C13.6183302,18.2533342 12.97867,18.342 12.32,18.342 C11.3573285,18.342 10.4263378,18.1741683 9.527,17.8385 C8.62766217,17.5028317 7.88033631,17.0246698 7.285,16.404 L9.413,14.238 C9.74233498,14.6433354 10.176164,14.9821653 10.7145,15.2545 C11.252836,15.5268347 11.7879973,15.663 12.32,15.663 C12.5606679,15.663 12.7949989,15.6376669 13.023,15.587 C13.2510011,15.5363331 13.4504991,15.4540006 13.6215,15.34 C13.7925009,15.2259994 13.9286662,15.0740009 14.03,14.884 C14.1313338,14.693999 14.182,14.4660013 14.182,14.2 C14.182,13.9466654 14.1186673,13.7313342 13.992,13.554 C13.8653327,13.3766658 13.6848345,13.2151674 13.4505,13.0695 C13.2161655,12.9238326 12.9248351,12.7908339 12.5765,12.6705 C12.2281649,12.5501661 11.8323355,12.420334 11.389,12.281 C10.9583312,12.141666 10.5371687,11.9770009 10.1255,11.787 C9.71383127,11.596999 9.34650161,11.3531682 9.0235,11.0555 C8.70049838,10.7578318 8.44083431,10.3968355 8.2445,9.9725 C8.04816568,9.54816454 7.95,9.03200304 7.95,8.424 C7.95,7.67666293 8.10199848,7.03700266 8.406,6.505 C8.71000152,5.97299734 9.10899753,5.53600171 9.603,5.194 C10.0970025,4.85199829 10.6543302,4.60183412 11.275,4.4435 C11.8956698,4.28516587 12.5226635,4.206 13.156,4.206 C13.9160038,4.206 14.6918294,4.34533194 15.4835,4.624 C16.2751706,4.90266806 16.9686637,5.31433061 17.564,5.859 L15.493,8.044 Z" fill="#000000" />
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">Finanzas</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text">Finanzas</span>
                                </span>
                            </li>

                            <!--  <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.administration.cobranzas_detail_total' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.administration.cobranzas_detail_total') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Cobranzas Detallado Resumido</span>
                                </a>
                            </li>-->
                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.liquidations_detail_total' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.liquidations_detail_total') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Liquidación Canales Resumido</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 10 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.finanzas.report.rec_caja.index' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.finanzas.report.rec_caja.index') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte Recaudación de Caja</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.finanzas_detail_total' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.finanzas_detail_total') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Cierre de Caja Diario</span>
                                </a>
                            </li>
                            @endif


                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.finanzas_resumen' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.finanzas_resumen') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte consolidado de ventas y cobranzas </span>
                                </a>
                            </li>
                            @endif


                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.flujo' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.flujo') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Flujo de Finanzas</span>
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.control_liq' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.control_liq') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Control de Liquidación</span>
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.close_sale' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.close_sale') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Cierre de Venta</span>
                                </a>
                            </li>
                            @endif

                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.collection_total_report') ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.collection_total_report') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Consolidado de Liquidaciones</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 1 || Auth::user()->id == 2 || Auth::user()->id == 3 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.sale_margin_report.index') ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.sale_margin_report.index') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">RESUMEN VENTA Y MARGEN</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif
                <!-- cierre Menú de Finanzas-->

                @if ( Auth::user()->id == 7 || Auth::user()->id == 15 )
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/administracion/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path d="M15.9497475,3.80761184 L13.0246125,6.73274681 C12.2435639,7.51379539 12.2435639,8.78012535 13.0246125,9.56117394 L14.4388261,10.9753875 C15.2198746,11.7564361 16.4862046,11.7564361 17.2672532,10.9753875 L20.1923882,8.05025253 C20.7341101,10.0447871 20.2295941,12.2556873 18.674559,13.8107223 C16.8453326,15.6399488 14.1085592,16.0155296 11.8839934,14.9444337 L6.75735931,20.0710678 C5.97631073,20.8521164 4.70998077,20.8521164 3.92893219,20.0710678 C3.1478836,19.2900192 3.1478836,18.0236893 3.92893219,17.2426407 L9.05556629,12.1160066 C7.98447038,9.89144078 8.36005124,7.15466739 10.1892777,5.32544095 C11.7443127,3.77040588 13.9552129,3.26588995 15.9497475,3.80761184 Z" fill="#000000" />
                                    <path d="M16.6568542,5.92893219 L18.0710678,7.34314575 C18.4615921,7.73367004 18.4615921,8.36683502 18.0710678,8.75735931 L16.6913928,10.1370344 C16.3008685,10.5275587 15.6677035,10.5275587 15.2771792,10.1370344 L13.8629656,8.7228208 C13.4724413,8.33229651 13.4724413,7.69913153 13.8629656,7.30860724 L15.2426407,5.92893219 C15.633165,5.5384079 16.26633,5.5384079 16.6568542,5.92893219 Z" fill="#000000" opacity="0.3" />
                                </g>
                            </svg>

                        </span>
                        <span class="kt-menu__link-text">Pruebas</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text">Pruebas</span>
                                </span>
                            </li>



                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.kardex' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.kardex') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Kardex</span>
                                </a>
                            </li>

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'facturations.import' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('facturations.import') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Importar Facturación</span>
                                </a>
                            </li>



                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.gerencia' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.gerencia') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Gerencia Diaria</span>
                                </a>
                            </li>

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.proyection' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.proyection') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Proyección Diaria</span>
                                </a>
                            </li>

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.cobert' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.cobert') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Cobertura Diaria</span>
                                </a>
                            </li>

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.rutas' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.rutas') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Rutas Diarias</span>
                                </a>
                            </li>


                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.glp_fact' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.glp_fact') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Editor de Compras GLP</span>
                                </a>
                            </li>

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.liquidations' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.liquidations') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Liquidaciones</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                @if ( Auth::user()->id == 7 )
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/administracion/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path d="M15.9497475,3.80761184 L13.0246125,6.73274681 C12.2435639,7.51379539 12.2435639,8.78012535 13.0246125,9.56117394 L14.4388261,10.9753875 C15.2198746,11.7564361 16.4862046,11.7564361 17.2672532,10.9753875 L20.1923882,8.05025253 C20.7341101,10.0447871 20.2295941,12.2556873 18.674559,13.8107223 C16.8453326,15.6399488 14.1085592,16.0155296 11.8839934,14.9444337 L6.75735931,20.0710678 C5.97631073,20.8521164 4.70998077,20.8521164 3.92893219,20.0710678 C3.1478836,19.2900192 3.1478836,18.0236893 3.92893219,17.2426407 L9.05556629,12.1160066 C7.98447038,9.89144078 8.36005124,7.15466739 10.1892777,5.32544095 C11.7443127,3.77040588 13.9552129,3.26588995 15.9497475,3.80761184 Z" fill="#000000" />
                                    <path d="M16.6568542,5.92893219 L18.0710678,7.34314575 C18.4615921,7.73367004 18.4615921,8.36683502 18.0710678,8.75735931 L16.6913928,10.1370344 C16.3008685,10.5275587 15.6677035,10.5275587 15.2771792,10.1370344 L13.8629656,8.7228208 C13.4724413,8.33229651 13.4724413,7.69913153 13.8629656,7.30860724 L15.2426407,5.92893219 C15.633165,5.5384079 16.26633,5.5384079 16.6568542,5.92893219 Z" fill="#000000" opacity="0.3" />
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">Seguridad</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text">Seguridad</span>
                                </span>
                            </li>
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.security.index' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.security.index') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Usuarios</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                @if ( Auth::user()->id == 7 || Auth::user()->id == 66 ||  Auth::user()->id == 51 ||  Auth::user()->id == 60 || Auth::user()->id == 52 || Auth::user()->id == 1 || Auth::user()->id == 10 || Auth::user()->id == 21 || Auth::user()->id == 44 || Auth::user()->id == 2 || Auth::user()->id == 4 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 5 || Auth::user()->id == 17 || Auth::user()->id == 30)
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/transportes/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path d="M10,4 L21,4 C21.5522847,4 22,4.44771525 22,5 L22,7 C22,7.55228475 21.5522847,8 21,8 L10,8 C9.44771525,8 9,7.55228475 9,7 L9,5 C9,4.44771525 9.44771525,4 10,4 Z M10,10 L21,10 C21.5522847,10 22,10.4477153 22,11 L22,13 C22,13.5522847 21.5522847,14 21,14 L10,14 C9.44771525,14 9,13.5522847 9,13 L9,11 C9,10.4477153 9.44771525,10 10,10 Z M10,16 L21,16 C21.5522847,16 22,16.4477153 22,17 L22,19 C22,19.5522847 21.5522847,20 21,20 L10,20 C9.44771525,20 9,19.5522847 9,19 L9,17 C9,16.4477153 9.44771525,16 10,16 Z" fill="#000000" />
                                    <rect fill="#000000" opacity="0.3" x="2" y="4" width="5" height="16" rx="1" />
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">Transportes</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text">Transportes</span>
                                </span>
                            </li>

                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 || Auth::user()->id ==2 ||Auth::user()->id == 1 || Auth::user()->id == 51 || Auth::user()->id == 52 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.transport_cochera_log_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.transport_cochera_log_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Registro de Facturas Transportes</span>
                                </a>
                            </li>
                            @endif


                            @if(Auth::user()->id == 7 || Auth::user()->id == 66 ||  Auth::user()->id == 60 || Auth::user()->id == 44 || Auth::user()->id == 1 || Auth::user()->id == 2|| Auth::user()->id == 10 || Auth::user()->id == 52 || Auth::user()->id == 51 || Auth::user()->id == 4 || Auth::user()->id == 5 || Auth::user()->id == 7 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 67 || Auth::user()->id == 68 || Auth::user()->id == 69 || Auth::user()->id == 70 || Auth::user()->id == 11  || Auth::user()->id == 12 || Auth::user()->id == 15 || Auth::user()->id == 16 || Auth::user()->id == 17 || Auth::user()->id == 19 || Auth::user()->id == 20 || Auth::user()->id == 21 || Auth::user()->id == 23 || Auth::user()->id == 24 || Auth::user()->id == 25 || Auth::user()->id == 26 || Auth::user()->id == 28 || Auth::user()->id == 29 || Auth::user()->id == 30 || Auth::user()->id == 31 || Auth::user()->id == 32 || Auth::user()->id == 33 || Auth::user()->id == 34 || Auth::user()->id == 35 || Auth::user()->id == 36 || Auth::user()->id == 37 || Auth::user()->id == 38 || Auth::user()->id == 39 || Auth::user()->id == 40 || Auth::user()->id == 41 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.facturation_cochera' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.facturation_cochera') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Facturas Por Pagar</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>

                @endif





                @if ( Auth::user()->id == 7  || Auth::user()->id == 47 )
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/administracion/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path d="M15.9497475,3.80761184 L13.0246125,6.73274681 C12.2435639,7.51379539 12.2435639,8.78012535 13.0246125,9.56117394 L14.4388261,10.9753875 C15.2198746,11.7564361 16.4862046,11.7564361 17.2672532,10.9753875 L20.1923882,8.05025253 C20.7341101,10.0447871 20.2295941,12.2556873 18.674559,13.8107223 C16.8453326,15.6399488 14.1085592,16.0155296 11.8839934,14.9444337 L6.75735931,20.0710678 C5.97631073,20.8521164 4.70998077,20.8521164 3.92893219,20.0710678 C3.1478836,19.2900192 3.1478836,18.0236893 3.92893219,17.2426407 L9.05556629,12.1160066 C7.98447038,9.89144078 8.36005124,7.15466739 10.1892777,5.32544095 C11.7443127,3.77040588 13.9552129,3.26588995 15.9497475,3.80761184 Z" fill="#000000" />
                                    <path d="M16.6568542,5.92893219 L18.0710678,7.34314575 C18.4615921,7.73367004 18.4615921,8.36683502 18.0710678,8.75735931 L16.6913928,10.1370344 C16.3008685,10.5275587 15.6677035,10.5275587 15.2771792,10.1370344 L13.8629656,8.7228208 C13.4724413,8.33229651 13.4724413,7.69913153 13.8629656,7.30860724 L15.2426407,5.92893219 C15.633165,5.5384079 16.26633,5.5384079 16.6568542,5.92893219 Z" fill="#000000" opacity="0.3" />
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">PUNTO MOTOR</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text">PUNTO MOTOR</span>
                                </span>
                            </li>
                            @if(Auth::user()->id == 7 || Auth::user()->id == 47 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.articles_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.articles_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Gestión de Bienes</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 47 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.services' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.services') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Gestión de Servicios</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 47 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.vehicle' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.vehicle') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Vehículos</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 47 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.clients-pm' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.voucher.clients-pm') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Registro de Clientes PM</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 47 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.assign_vehicle' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.assign_vehicle') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Asignar Cliente - Vehículo</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 47 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.logistics.ot_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.logistics.ot_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Registro de OT</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 47 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.order_work' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.order_work') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de OT</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 47 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.logistics.cotizacion_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.logistics.cotizacion_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Registro de Cotización</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 47 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.logistics.cotizacion_report' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.logistics.cotizacion_report') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Cotizaciones x Facturar</span>
                                </a>
                            </li>
                            @endif
                            @if(Auth::user()->id == 7 || Auth::user()->id == 47 )
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.liquidation_pm' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.liquidation_pm') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Liquidar</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                <!--TEST DE REPORTES DE ANTONIO-->
                @if ( Auth::user()->id == 2)
                <!--Módulo principal-->
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/beta/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path d="M10,4 L21,4 C21.5522847,4 22,4.44771525 22,5 L22,7 C22,7.55228475 21.5522847,8 21,8 L10,8 C9.44771525,8 9,7.55228475 9,7 L9,5 C9,4.44771525 9.44771525,4 10,4 Z M10,10 L21,10 C21.5522847,10 22,10.4477153 22,11 L22,13 C22,13.5522847 21.5522847,14 21,14 L10,14 C9.44771525,14 9,13.5522847 9,13 L9,11 C9,10.4477153 9.44771525,10 10,10 Z M10,16 L21,16 C21.5522847,16 22,16.4477153 22,17 L22,19 C22,19.5522847 21.5522847,20 21,20 L10,20 C9.44771525,20 9,19.5522847 9,19 L9,17 C9,16.4477153 9.44771525,16 10,16 Z" fill="#000000" />
                                    <rect fill="#000000" opacity="0.3" x="2" y="4" width="5" height="16" rx="1" />
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">Beta</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <!--Contenedor de listas-->
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text"></span>
                                </span>
                            </li>
                            <!--Contenedor de submódulo 1-->
                            @if(Auth::user()->id == 2)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report_andromeda' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report_andromeda') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Andromeda</span>
                                </a>
                            </li>
                            @endif
                            <!--CONTENEDOR DE SUBMODULO 2-->
                            @if(Auth::user()->id == 2)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.excedent' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.excedent') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Excedente</span>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif
                <!--TEST DE REPORTES DE ANTONIO-->

                @if ( Auth::user()->id == 7)
                <!--Módulo principal-->
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/beta/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path d="M10,4 L21,4 C21.5522847,4 22,4.44771525 22,5 L22,7 C22,7.55228475 21.5522847,8 21,8 L10,8 C9.44771525,8 9,7.55228475 9,7 L9,5 C9,4.44771525 9.44771525,4 10,4 Z M10,10 L21,10 C21.5522847,10 22,10.4477153 22,11 L22,13 C22,13.5522847 21.5522847,14 21,14 L10,14 C9.44771525,14 9,13.5522847 9,13 L9,11 C9,10.4477153 9.44771525,10 10,10 Z M10,16 L21,16 C21.5522847,16 22,16.4477153 22,17 L22,19 C22,19.5522847 21.5522847,20 21,20 L10,20 C9.44771525,20 9,19.5522847 9,19 L9,17 C9,16.4477153 9.44771525,16 10,16 Z" fill="#000000" />
                                    <rect fill="#000000" opacity="0.3" x="2" y="4" width="5" height="16" rx="1" />
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">Reportes 3D</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <!--Contenedor de listas-->
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text"></span>
                                </span>
                            </li>
                            <!--Contenedor de submódulo 1-->
                            @if(Auth::user()->id == 7)
                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.sales_grafic' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.sales_grafic') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Ventas con gráfico</span>
                                </a>
                            </li>


                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.liquidations_sistemas' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.liquidations_sistemas') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Liquidaciones Sistemas</span>
                                </a>
                            </li>


                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.liquidations_total_sistemas' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.liquidations_total_sistemas') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte Resumido Sistemas</span>
                                </a>
                            </li>

                            @endif
                        </ul>
                    </div>
                </li>
                @endif
                <!--TEST DE REPORTES CON GRAFICOS-->

                @if ( Auth::user()->id == 7)
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/beta/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path d="M10,4 L21,4 C21.5522847,4 22,4.44771525 22,5 L22,7 C22,7.55228475 21.5522847,8 21,8 L10,8 C9.44771525,8 9,7.55228475 9,7 L9,5 C9,4.44771525 9.44771525,4 10,4 Z M10,10 L21,10 C21.5522847,10 22,10.4477153 22,11 L22,13 C22,13.5522847 21.5522847,14 21,14 L10,14 C9.44771525,14 9,13.5522847 9,13 L9,11 C9,10.4477153 9.44771525,10 10,10 Z M10,16 L21,16 C21.5522847,16 22,16.4477153 22,17 L22,19 C22,19.5522847 21.5522847,20 21,20 L10,20 C9.44771525,20 9,19.5522847 9,19 L9,17 C9,16.4477153 9.44771525,16 10,16 Z" fill="#000000" />
                                    <rect fill="#000000" opacity="0.3" x="2" y="4" width="5" height="16" rx="1" />
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">Sede Punto Motor</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <!--Contenedor de listas-->
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text"></span>
                                </span>
                            </li>
                            <!--Contenedor de submódulo 1-->

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === '' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="http://98.84.121.151/proyPuntoMotor/public/" target="_blank" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Sede</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                <!--ADMINISTRACION-->

                @if ( Auth::user()->id == 7)
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/beta/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path d="M10,4 L21,4 C21.5522847,4 22,4.44771525 22,5 L22,7 C22,7.55228475 21.5522847,8 21,8 L10,8 C9.44771525,8 9,7.55228475 9,7 L9,5 C9,4.44771525 9.44771525,4 10,4 Z M10,10 L21,10 C21.5522847,10 22,10.4477153 22,11 L22,13 C22,13.5522847 21.5522847,14 21,14 L10,14 C9.44771525,14 9,13.5522847 9,13 L9,11 C9,10.4477153 9.44771525,10 10,10 Z M10,16 L21,16 C21.5522847,16 22,16.4477153 22,17 L22,19 C22,19.5522847 21.5522847,20 21,20 L10,20 C9.44771525,20 9,19.5522847 9,19 L9,17 C9,16.4477153 9.44771525,16 10,16 Z" fill="#000000" />
                                    <rect fill="#000000" opacity="0.3" x="2" y="4" width="5" height="16" rx="1" />
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">ADMINISTRACIÓN</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <!--Contenedor de listas-->
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text"></span>
                                </span>
                            </li>
                            <!--Contenedor de submódulo 1-->

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.caja_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.caja_register') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Registro Rendición de caja</span>
                                </a>
                            </li>

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.facturation_report_caja' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.facturation_report_caja') }}" target="_blank" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Rendiciones de caja</span>
                                </a>
                            </li>

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.report.route' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.report.route') }}" target="_blank" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Reporte de Margen por Ruta</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif


                <!--ADMINISTRACION-->

                @if ( Auth::user()->id == 7)
                <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/beta/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                    <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                        <span class="kt-menu__link-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path d="M10,4 L21,4 C21.5522847,4 22,4.44771525 22,5 L22,7 C22,7.55228475 21.5522847,8 21,8 L10,8 C9.44771525,8 9,7.55228475 9,7 L9,5 C9,4.44771525 9.44771525,4 10,4 Z M10,10 L21,10 C21.5522847,10 22,10.4477153 22,11 L22,13 C22,13.5522847 21.5522847,14 21,14 L10,14 C9.44771525,14 9,13.5522847 9,13 L9,11 C9,10.4477153 9.44771525,10 10,10 Z M10,16 L21,16 C21.5522847,16 22,16.4477153 22,17 L22,19 C22,19.5522847 21.5522847,20 21,20 L10,20 C9.44771525,20 9,19.5522847 9,19 L9,17 C9,16.4477153 9.44771525,16 10,16 Z" fill="#000000" />
                                    <rect fill="#000000" opacity="0.3" x="2" y="4" width="5" height="16" rx="1" />
                                </g>
                            </svg>
                        </span>
                        <span class="kt-menu__link-text">CONTROL DE PLANTA</span>
                        <i class="kt-menu__ver-arrow la la-angle-right"></i>
                    </a>
                    <!--Contenedor de listas-->
                    <div class="kt-menu__submenu ">
                        <span class="kt-menu__arrow"></span>
                        <ul class="kt-menu__subnav">
                            <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                <span class="kt-menu__link">
                                    <span class="kt-menu__link-text"></span>
                                </span>
                            </li>
                            <!--Contenedor de submódulo 1-->

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.opening.caja' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.opening.caja') }}" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Apertura de Caja</span>
                                </a>
                            </li>

                            <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.opening.planta' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.opening.planta') }}" target="_blank" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Apertura de Planta</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>
                @endif

                 <!--ADMINISTRACION Y FINANZAS-->

                 @if ( Auth::user()->id == 7 || Auth::user()->id == 4 || Auth::user()->id == 21 || Auth::user()->id == 60 || Auth::user()->id == 8 || Auth::user()->id == 9 || Auth::user()->id == 10 || Auth::user()->id == 42 || Auth::user()->id == 44)
                 <li class="kt-menu__item  kt-menu__item--submenu {{ ( strpos(url()->current(), '/beta/') !== false ? 'kt-menu__item--open kt-menu__item--here' : '' ) }}" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
                     <a href="javascript:;" class="kt-menu__link kt-menu__toggle">
                         <span class="kt-menu__link-icon">
                             <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                 <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                     <rect x="0" y="0" width="24" height="24" />
                                     <path d="M10,4 L21,4 C21.5522847,4 22,4.44771525 22,5 L22,7 C22,7.55228475 21.5522847,8 21,8 L10,8 C9.44771525,8 9,7.55228475 9,7 L9,5 C9,4.44771525 9.44771525,4 10,4 Z M10,10 L21,10 C21.5522847,10 22,10.4477153 22,11 L22,13 C22,13.5522847 21.5522847,14 21,14 L10,14 C9.44771525,14 9,13.5522847 9,13 L9,11 C9,10.4477153 9.44771525,10 10,10 Z M10,16 L21,16 C21.5522847,16 22,16.4477153 22,17 L22,19 C22,19.5522847 21.5522847,20 21,20 L10,20 C9.44771525,20 9,19.5522847 9,19 L9,17 C9,16.4477153 9.44771525,16 10,16 Z" fill="#000000" />
                                     <rect fill="#000000" opacity="0.3" x="2" y="4" width="5" height="16" rx="1" />
                                 </g>
                             </svg>
                         </span>
                         <span class="kt-menu__link-text">ADMI Y FINANZAS</span>
                         <i class="kt-menu__ver-arrow la la-angle-right"></i>
                     </a>
                     <!--Contenedor de listas-->
                     <div class="kt-menu__submenu ">
                         <span class="kt-menu__arrow"></span>
                         <ul class="kt-menu__subnav">
                             <li class="kt-menu__item kt-menu__item--parent" aria-haspopup="true">
                                 <span class="kt-menu__link">
                                     <span class="kt-menu__link-text"></span>
                                 </span>
                             </li>
                             <!--Contenedor de submódulo 1-->

                             <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.operations.sol_req_register' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                 <a href="{{ route('dashboard.operations.sol_req_register') }}" class="kt-menu__link ">
                                     <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                         <span></span>
                                     </i>
                                     <span class="kt-menu__link-text">Registro de Solicitudes</span>
                                 </a>
                             </li>

                             <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.voucher.state_sol' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                 <a href="{{ route('dashboard.voucher.state_sol') }}" target="_blank" class="kt-menu__link ">
                                     <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                         <span></span>
                                     </i>
                                     <span class="kt-menu__link-text">Solicitud de Pagos</span>
                                 </a>
                             </li>

                             <li class="kt-menu__item {{ ( ( Route::currentRouteName() === 'dashboard.operations.liq_rendir' ) ? 'kt-menu__item--active' : '' ) }}" aria-haspopup="true">
                                <a href="{{ route('dashboard.operations.liq_rendir') }}" target="_blank" class="kt-menu__link ">
                                    <i class="kt-menu__link-bullet kt-menu__link-bullet--dot">
                                        <span></span>
                                    </i>
                                    <span class="kt-menu__link-text">Subida de Documentos - RENDIR</span>
                                </a>
                            </li>



                         </ul>
                     </div>
                 </li>
                 @endif
            </ul>
        </div>
    </div>
    <!-- end:: Aside Menu -->
</div>
<!-- end:: Aside -->
