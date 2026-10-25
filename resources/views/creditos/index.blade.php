@extends('layouts.app')

@section('title', 'MOTOCONS - Gestión de Créditos')

@section('content')
<div class="space-y-6">
    <!-- Barra de Herramientas y Título Estilo Desktop -->
    <div class="bg-white border border-slate-300 rounded-lg p-5 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-2.5">
                <div class="p-2 rounded bg-slate-100 border border-slate-300 text-slate-700">
                    <i data-lucide="file-badge" class="w-5 h-5 text-slate-700"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900">
                        Gestión de Créditos y Financiación de Vehículos
                    </h1>
                    <p class="text-xs text-slate-500">
                        Estudio crediticio, control documental y radicación para convenios Brilla y ServiCrédito.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <button onclick="window.location.reload()" class="px-3.5 py-1.5 rounded bg-slate-50 hover:bg-slate-100 text-slate-700 text-xs font-semibold border border-slate-300 shadow-2xs transition flex items-center gap-1.5 active:translate-y-px">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-slate-500"></i>
            </button>
        </div>
    </div>

    <!-- Panel de Indicadores de Rendimiento (KPIs) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-slate-300 rounded-lg p-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Trámites Registrados</span>
                <i data-lucide="folder" class="w-4 h-4 text-slate-400"></i>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-slate-900">{{ $solicitudes->count() }}</span>
                <span class="text-[11px] text-slate-500">totales</span>
            </div>
        </div>

        <div class="bg-white border border-slate-300 rounded-lg p-4 shadow-2xs border-l-4 border-l-amber-500">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">En Estudio de Crédito</span>
                <i data-lucide="clock" class="w-4 h-4 text-amber-500"></i>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-amber-700">{{ $solicitudes->where('estado', 'EN_PROCESO_CREDITO')->count() }}</span>
                <span class="text-[11px] text-slate-500">en validación</span>
            </div>
        </div>

        <div class="bg-white border border-slate-300 rounded-lg p-4 shadow-2xs border-l-4 border-l-emerald-600">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Créditos Aprobados</span>
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-emerald-700">{{ $solicitudes->where('estado', 'PAGADA')->count() }}</span>
                <span class="text-[11px] text-slate-500">listas entrega</span>
            </div>
        </div>

        <div class="bg-white border border-slate-300 rounded-lg p-4 shadow-2xs border-l-4 border-l-sky-600">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-sky-700 uppercase tracking-wider">Motos Disponibles</span>
                <i data-lucide="bike" class="w-4 h-4 text-sky-600"></i>
            </div>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-2xl font-bold text-sky-700">{{ $motocicletasDisponibles->count() }}</span>
                <span class="text-[11px] text-slate-500">en sala</span>
            </div>
        </div>
    </div>

    <!-- Contenedor Principal TabPane Estilo JavaFX -->
    <div class="space-y-0">
        <!-- Pestañas Superiores JavaFX TabHeader -->
        <div class="flex items-end gap-1 px-2 border-b border-slate-300 overflow-x-auto">
            <button onclick="switchTab('tab-nueva')" id="btn-tab-nueva" class="tab-button px-4 py-2.5 text-xs font-semibold rounded-t-md border-t border-l border-r border-slate-300 bg-white text-slate-900 shadow-2xs flex items-center gap-2 relative top-[1px] transition">
                <i data-lucide="file-plus-2" class="w-3.5 h-3.5 text-red-600"></i>
                <span>1. Iniciar Solicitud</span>
            </button>
            <button onclick="switchTab('tab-soportes')" id="btn-tab-soportes" class="tab-button px-4 py-2.5 text-xs font-semibold rounded-t-md border-t border-l border-r border-transparent bg-slate-200/70 text-slate-600 hover:bg-slate-200 hover:text-slate-800 flex items-center gap-2 relative top-[1px] transition">
                <i data-lucide="paperclip" class="w-3.5 h-3.5 text-slate-500"></i>
                <span>2. Cargar Soportes</span>
            </button>
            <button onclick="switchTab('tab-evaluar')" id="btn-tab-evaluar" class="tab-button px-4 py-2.5 text-xs font-semibold rounded-t-md border-t border-l border-r border-transparent bg-slate-200/70 text-slate-600 hover:bg-slate-200 hover:text-slate-800 flex items-center gap-2 relative top-[1px] transition">
                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-slate-500"></i>
                <span>3. Mesa de Control y Aprobación</span>
            </button>
            <button onclick="switchTab('tab-catalogo')" id="btn-tab-catalogo" class="tab-button px-4 py-2.5 text-xs font-semibold rounded-t-md border-t border-l border-r border-transparent bg-slate-200/70 text-slate-600 hover:bg-slate-200 hover:text-slate-800 flex items-center gap-2 relative top-[1px] transition">
                <i data-lucide="book-open" class="w-3.5 h-3.5 text-slate-500"></i>
                <span>4. Catálogo de Requisitos</span>
            </button>
        </div>

        <!-- Contenedor del Cuerpo de la Pestaña Activa -->
        <div class="bg-white border border-slate-300 rounded-b-lg p-6 shadow-2xs">

            <!-- PESTAÑA 1: INICIAR SOLICITUD -->
            <div id="tab-nueva" class="tab-content block">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Formulario de Entrada -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="border-b border-slate-200 pb-3 flex items-center justify-between">
                            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                                <i data-lucide="clipboard-list" class="w-4 h-4 text-slate-600"></i>
                                Datos de la Solicitud y Selección de Vehículo
                            </h2>
                            <span class="text-xs text-slate-400 font-mono">* Campos requeridos</span>
                        </div>

                        <form id="form-iniciar-solicitud" onsubmit="iniciarSolicitud(event)" class="space-y-5">
                            @csrf
                            <!-- Selección de Cliente -->
                            <div class="bg-slate-50/80 p-4 rounded-md border border-slate-200 space-y-3">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">
                                    Cliente Solicitante *
                                </label>
                                <select id="cliente_select" onchange="actualizarClienteDocs(this)" class="w-full bg-white border border-slate-300 rounded px-3 py-2 text-xs text-slate-800 focus:outline-none focus:border-slate-500 focus:ring-1 focus:ring-slate-500">
                                    <option value="">-- Seleccionar cliente existente o ingresar documento abajo --</option>
                                    @foreach($clientes as $cli)
                                        <option value="{{ $cli->tipo_doc }}:{{ $cli->num_doc }}">
                                            {{ $cli->nombre_razon_social }} ({{ $cli->tipo_doc }} {{ $cli->num_doc }}) - {{ $cli->municipio }}
                                        </option>
                                    @endforeach
                                </select>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tipo de Documento</label>
                                        <select id="cliente_tipo_doc" required class="w-full bg-white border border-slate-300 rounded px-3 py-1.5 text-xs text-slate-800">
                                            <option value="CC">Cédula de Ciudadanía (CC)</option>
                                            <option value="CE">Cédula de Extranjería (CE)</option>
                                            <option value="NIT">NIT Persona Jurídica</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Número de Documento *</label>
                                        <input type="text" id="cliente_num_doc" required placeholder="Ej: 1065123456" class="w-full bg-white border border-slate-300 rounded px-3 py-1.5 text-xs text-slate-800 focus:outline-none focus:border-slate-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Entidad Financiera -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2">
                                    Entidad Financiera / Medio de Financiación *
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <label class="relative flex items-start p-3.5 rounded border border-slate-300 bg-white cursor-pointer hover:border-slate-400 hover:bg-slate-50/50 transition">
                                        <input type="radio" name="medio_pago" value="BRILLA" checked class="mt-0.5 text-red-600 focus:ring-red-500 mr-3">
                                        <div>
                                            <span class="block text-xs font-bold text-slate-900">Crédito Brilla</span>
                                            <span class="block text-[11px] text-slate-500 mt-0.5">Gases del Caribe / Surtigas (Recibo de gas con cupo)</span>
                                        </div>
                                    </label>

                                    <label class="relative flex items-start p-3.5 rounded border border-slate-300 bg-white cursor-pointer hover:border-slate-400 hover:bg-slate-50/50 transition">
                                        <input type="radio" name="medio_pago" value="SERVICREDITO" class="mt-0.5 text-sky-600 focus:ring-sky-500 mr-3">
                                        <div>
                                            <span class="block text-xs font-bold text-slate-900">ServiCrédito</span>
                                            <span class="block text-[11px] text-slate-500 mt-0.5">Estudio de nómina y extractos bancarios</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Motocicleta a Financiar -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                                    Motocicleta Seleccionada (Reserva de VIN)
                                </label>
                                <select id="vin_moto" class="w-full bg-white border border-slate-300 rounded px-3 py-2 text-xs text-slate-800 focus:outline-none focus:border-slate-500">
                                    <option value="">-- Sin asignación de VIN inmediato (Trámite abierto) --</option>
                                    @foreach($motocicletasDisponibles as $moto)
                                        <option value="{{ $moto->vin }}">
                                            {{ $moto->modeloMoto->marca ?? '' }} {{ $moto->modeloMoto->linea ?? $moto->modelo_referencia }}
                                            | VIN: {{ $moto->vin }}
                                            | Color: {{ $moto->color }}
                                            | Precio: ${{ number_format($moto->precio_lista - $moto->descuento, 0, ',', '.') }} COP
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-slate-500 mt-1">La unidad seleccionada cambiará de forma atómica su estado a <strong>RESERVADA</strong>.</p>
                            </div>

                            <!-- Canal de Venta y Asesor -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Canal de Venta</label>
                                    <select id="canal" class="w-full bg-white border border-slate-300 rounded px-3 py-1.5 text-xs text-slate-800">
                                        <option value="PRESENCIAL">Presencial (Sala de Ventas)</option>
                                        <option value="WEB">Canal Digital / Portal Web</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Asesor Comercial</label>
                                    <select id="vendedor_select" class="w-full bg-white border border-slate-300 rounded px-3 py-1.5 text-xs text-slate-800">
                                        <option value="">-- Sin asesor comercial asignado --</option>
                                        @foreach($empleados as $emp)
                                            <option value="{{ $emp->tipo_doc }}:{{ $emp->num_doc }}">{{ $emp->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Botón de Envío -->
                            <div class="pt-2">
                                <button type="submit" id="btn-submit-solicitud" class="w-full sm:w-auto px-6 py-2.5 rounded bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-xs transition flex items-center justify-center gap-2 active:translate-y-px">
                                    <i data-lucide="send" class="w-3.5 h-3.5"></i>
                                    <span>Radicar Trámite de Crédito</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Panel Informativo Lateral -->
                    <div class="space-y-4">
                        <div class="bg-slate-50 border border-slate-300 rounded-lg p-4">
                            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                                <i data-lucide="info" class="w-4 h-4 text-slate-600"></i>
                                Flujo de Operación
                            </h3>
                            <ol class="text-xs text-slate-600 space-y-2 leading-relaxed">
                                <li><strong>1. Radicación:</strong> Se genera la venta con estado <span class="font-mono bg-amber-100 text-amber-800 px-1 py-0.5 rounded text-[11px]">EN_PROCESO_CREDITO</span>.</li>
                                <li><strong>2. Checklist:</strong> Se vinculan automáticamente los requisitos exigidos por el convenio financiero.</li>
                                <li><strong>3. Carga:</strong> En la pestaña <em>Cargar Soportes</em> se adjuntan los PDFs o fotos.</li>
                                <li><strong>4. Decisión:</strong> La mesa de control aprueba y transiciona a <span class="font-mono bg-emerald-100 text-emerald-800 px-1 py-0.5 rounded text-[11px]">PAGADA</span>.</li>
                            </ol>
                        </div>

                        <div class="bg-amber-50/70 border border-amber-300 rounded-lg p-4">
                            <h3 class="text-xs font-bold text-amber-900 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i data-lucide="alert-circle" class="w-4 h-4 text-amber-700"></i>
                                Regla de Dominio Brilla
                            </h3>
                            <p class="text-xs text-amber-900/80 leading-relaxed">
                                El recibo de gas del último mes con cupo aprobado es de carácter obligatorio. Si el analista intenta aprobar sin dicho soporte validado, el sistema disparará la excepción <code class="font-mono text-[11px] bg-amber-100 px-1 rounded">RequisitoCreditoInvalidoException</code>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PESTAÑA 2: CARGAR SOPORTES -->
            <div id="tab-soportes" class="tab-content hidden space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                            <i data-lucide="upload" class="w-4 h-4 text-slate-600"></i>
                            Carga de Documentación y Soportes de Crédito
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Seleccione la orden de venta para consultar los requisitos y adjuntar evidencias.</p>
                    </div>

                    <div class="min-w-[280px]">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Orden de Venta:</label>
                        <select id="selector_venta_soportes" onchange="cargarDetalleVenta(this.value)" class="w-full bg-white border border-slate-300 rounded px-3 py-1.5 text-xs font-mono text-slate-800 focus:outline-none focus:border-slate-500">
                            @forelse($solicitudes as $sol)
                                <option value="{{ $sol->numero }}">
                                    {{ $sol->numero }} ({{ $sol->cliente->nombre_razon_social ?? $sol->cliente_num_doc }}) - {{ $sol->estado }}
                                </option>
                            @empty
                                <option value="">No hay órdenes registradas</option>
                            @endforelse
                        </select>
                    </div>
                </div>

                <div id="contenedor-requisitos-soportes" class="space-y-3">
                    <!-- Dinámico vía JS -->
                </div>
            </div>

            <!-- PESTAÑA 3: MESA DE CONTROL Y APROBACIÓN -->
            <div id="tab-evaluar" class="tab-content hidden space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                            <i data-lucide="user-check" class="w-4 h-4 text-slate-600"></i>
                            Mesa de Control y Aprobación Financiera
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Validación de documentos, dictamen del analista y emisión de aprobación final.</p>
                    </div>

                    <div class="min-w-[280px]">
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Orden a Dictaminar:</label>
                        <select id="selector_venta_evaluar" onchange="cargarDetalleEvaluacion(this.value)" class="w-full bg-white border border-slate-300 rounded px-3 py-1.5 text-xs font-mono text-slate-800 focus:outline-none focus:border-slate-500">
                            @forelse($solicitudes as $sol)
                                <option value="{{ $sol->numero }}">
                                    {{ $sol->numero }} ({{ $sol->cliente->nombre_razon_social ?? $sol->cliente_num_doc }}) - {{ $sol->estado }}
                                </option>
                            @empty
                                <option value="">No hay órdenes registradas</option>
                            @endforelse
                        </select>
                    </div>
                </div>

                <div id="contenedor-evaluacion" class="space-y-5">
                    <!-- Dinámico vía JS -->
                </div>
            </div>

            <!-- PESTAÑA 4: CATÁLOGO DE REQUISITOS -->
            <div id="tab-catalogo" class="tab-content hidden space-y-4">
                <div class="border-b border-slate-200 pb-3">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                        <i data-lucide="table" class="w-4 h-4 text-slate-600"></i>
                        Matriz Paramétrica de Requisitos por Convenio
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Configuración de entidades financieras en la base de datos PostgreSQL Neon.</p>
                </div>

                <div class="border border-slate-300 rounded-md overflow-hidden shadow-2xs">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-100 border-b border-slate-300 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                                <th class="py-2.5 px-3">Código</th>
                                <th class="py-2.5 px-3">Convenio / Entidad</th>
                                <th class="py-2.5 px-3">Descripción del Documento</th>
                                <th class="py-2.5 px-3 text-center">Carácter</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-800">
                            @forelse($requisitosCatalogo as $req)
                                <tr class="hover:bg-slate-50/80 transition even:bg-slate-50/30">
                                    <td class="py-2 px-3 font-mono text-slate-500">#{{ $req->id }}</td>
                                    <td class="py-2 px-3 font-semibold text-slate-900">
                                        {{ $req->medio_pago }}
                                    </td>
                                    <td class="py-2 px-3">{{ $req->descripcion }}</td>
                                    <td class="py-2 px-3 text-center">
                                        @if($req->obligatorio)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-red-50 text-red-700 border border-red-200">
                                                Obligatorio
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                                Opcional
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-slate-500">No hay requisitos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Gestión de Pestañas Estilo JavaFX TabPane
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('bg-white', 'text-slate-900', 'border-t-slate-300', 'border-l-slate-300', 'border-r-slate-300');
            btn.classList.add('bg-slate-200/70', 'text-slate-600', 'border-transparent');
        });

        const activeTab = document.getElementById(tabId);
        const activeBtn = document.getElementById('btn-' + tabId);
        if (activeTab) activeTab.classList.remove('hidden');
        if (activeBtn) {
            activeBtn.classList.remove('bg-slate-200/70', 'text-slate-600', 'border-transparent');
            activeBtn.classList.add('bg-white', 'text-slate-900', 'border-slate-300');
        }

        if (tabId === 'tab-soportes') {
            const v = document.getElementById('selector_venta_soportes').value;
            if (v) cargarDetalleVenta(v);
        } else if (tabId === 'tab-evaluar') {
            const v = document.getElementById('selector_venta_evaluar').value;
            if (v) cargarDetalleEvaluacion(v);
        }

        if (window.createLucideIcons) window.createLucideIcons();
    }

    // Actualizar campos de cliente al seleccionar de la lista
    function actualizarClienteDocs(selectEl) {
        if (!selectEl.value) return;
        const [tipo, num] = selectEl.value.split(':');
        document.getElementById('cliente_tipo_doc').value = tipo;
        document.getElementById('cliente_num_doc').value = num;
    }

    // Iniciar Solicitud de Crédito vía API
    async function iniciarSolicitud(e) {
        e.preventDefault();
        const btn = document.getElementById('btn-submit-solicitud');
        btn.disabled = true;
        btn.innerHTML = '<span>Procesando...</span>';

        const medioPago = document.querySelector('input[name="medio_pago"]:checked').value;
        const clienteTipo = document.getElementById('cliente_tipo_doc').value;
        const clienteNum = document.getElementById('cliente_num_doc').value;
        const vinMoto = document.getElementById('vin_moto').value || null;
        const canal = document.getElementById('canal').value;

        let vendedorTipo = null;
        let vendedorNum = null;
        const vendedorVal = document.getElementById('vendedor_select').value;
        if (vendedorVal) {
            const parts = vendedorVal.split(':');
            vendedorTipo = parts[0];
            vendedorNum = parts[1];
        }

        try {
            const res = await fetch('/api/creditos/solicitudes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    cliente_tipo_doc: clienteTipo,
                    cliente_num_doc: clienteNum,
                    medio_pago: medioPago,
                    vin_moto: vinMoto,
                    canal: canal,
                    vendedor_tipo_doc: vendedorTipo,
                    vendedor_num_doc: vendedorNum
                })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                window.showToast(`Solicitud ${data.data.venta_numero} radicada satisfactoriamente`, 'success');
                setTimeout(() => window.location.reload(), 1200);
            } else {
                window.showToast(data.message || 'Error al radicar solicitud', 'error');
            }
        } catch (err) {
            window.showToast('Error de comunicación con el servicio de crédito', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i data-lucide="send" class="w-3.5 h-3.5"></i><span>Radicar Trámite de Crédito</span>';
            if (window.createLucideIcons) window.createLucideIcons();
        }
    }

    // Datos de ventas precargados
    const todasLasVentas = @json($solicitudes);

    // Cargar Detalle para Subida de Soportes
    function cargarDetalleVenta(numeroVenta) {
        const contenedor = document.getElementById('contenedor-requisitos-soportes');
        if (!numeroVenta) {
            contenedor.innerHTML = '<p class="text-slate-500 text-xs">Seleccione una orden de venta.</p>';
            return;
        }

        const venta = todasLasVentas.find(v => v.numero === numeroVenta);
        if (!venta) {
            contenedor.innerHTML = '<p class="text-slate-500 text-xs">Orden no encontrada.</p>';
            return;
        }

        const badgeColor = venta.estado === 'PAGADA' ? 'bg-emerald-50 text-emerald-800 border-emerald-300' :
                          'bg-amber-50 text-amber-800 border-amber-300';

        let html = `
            <div class="bg-slate-50 p-3.5 rounded border border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs mb-3">
                <div class="flex items-center gap-3">
                    <div>
                        <span class="text-slate-500">Orden:</span> <strong class="font-mono text-slate-900">${venta.numero}</strong>
                    </div>
                    <span class="text-slate-300">|</span>
                    <div>
                        <span class="text-slate-500">Cliente:</span> <span class="font-medium text-slate-800">${venta.cliente?.nombre_razon_social || venta.cliente_num_doc}</span>
                    </div>
                </div>
                <div>
                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold border ${badgeColor}">${venta.estado}</span>
                </div>
            </div>
            <div class="space-y-2">
        `;

        if (!venta.requisitos || venta.requisitos.length === 0) {
            html += '<p class="text-slate-500 text-xs p-3">Esta orden no tiene requisitos de crédito asignados.</p>';
        } else {
            venta.requisitos.forEach(r => {
                const reqBadge = r.estado === 'VALIDADO' ? 'bg-emerald-50 text-emerald-800 border-emerald-300' :
                                 r.estado === 'RECHAZADO' ? 'bg-red-50 text-red-800 border-red-300' :
                                 'bg-amber-50 text-amber-800 border-amber-300';

                html += `
                    <div class="p-3 rounded border border-slate-200 bg-white hover:bg-slate-50/50 transition flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border ${reqBadge}">${r.estado}</span>
                                ${r.requisito_credito?.obligatorio ? '<span class="text-[11px] text-red-600 font-semibold">* Obligatorio</span>' : '<span class="text-[11px] text-slate-400">Opcional</span>'}
                            </div>
                            <p class="font-semibold text-slate-800">${r.requisito_credito?.descripcion || 'Requisito #' + r.requisito_id}</p>
                            ${r.soporte_adjunto ? `<p class="text-[11px] text-emerald-700 font-mono flex items-center gap-1"><i data-lucide="check" class="w-3 h-3 text-emerald-600"></i> Archivo cargado: ${r.soporte_adjunto}</p>` : '<p class="text-[11px] text-slate-400">Sin archivo adjunto</p>'}
                        </div>

                        <div class="shrink-0">
                            <form onsubmit="subirSoporte(event, '${venta.numero}', ${r.requisito_id})" class="flex items-center gap-2">
                                <label class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium cursor-pointer border border-slate-300 transition flex items-center gap-1.5">
                                    <i data-lucide="paperclip" class="w-3 h-3 text-slate-500"></i>
                                    <span>Examinar...</span>
                                    <input type="file" name="archivo" required accept=".pdf,.png,.jpg,.jpeg" class="hidden" onchange="this.parentElement.querySelector('span').innerText = this.files[0]?.name || 'Examinar...'">
                                </label>
                                <button type="submit" class="px-3 py-1 rounded bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold shadow-2xs transition">
                                    Subir
                                </button>
                            </form>
                        </div>
                    </div>
                `;
            });
        }

        html += `</div>`;
        contenedor.innerHTML = html;
        if (window.createLucideIcons) window.createLucideIcons();
    }

    // Subida de soporte
    async function subirSoporte(e, ventaNumero, requisitoId) {
        e.preventDefault();
        const form = e.target;
        const fileInput = form.querySelector('input[type="file"]');
        if (!fileInput.files || fileInput.files.length === 0) {
            window.showToast('Seleccione un archivo primero', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('archivo', fileInput.files[0]);

        try {
            const res = await fetch(`/api/creditos/solicitudes/${ventaNumero}/requisitos/${requisitoId}/soporte`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            const data = await res.json();
            if (res.ok && data.success) {
                window.showToast('Documento soporte recibido y archivado', 'success');
                setTimeout(() => window.location.reload(), 900);
            } else {
                window.showToast(data.message || 'Error al archivar documento', 'error');
            }
        } catch (err) {
            window.showToast('Error de red al transmitir archivo', 'error');
        }
    }

    // Cargar Detalle para Evaluación / Mesa de Control
    function cargarDetalleEvaluacion(numeroVenta) {
        const contenedor = document.getElementById('contenedor-evaluacion');
        if (!numeroVenta) {
            contenedor.innerHTML = '<p class="text-slate-500 text-xs">Seleccione una orden para evaluar.</p>';
            return;
        }

        const venta = todasLasVentas.find(v => v.numero === numeroVenta);
        if (!venta) {
            contenedor.innerHTML = '<p class="text-slate-500 text-xs">Orden no encontrada.</p>';
            return;
        }

        let requisitosHtml = '';

        if (venta.requisitos && venta.requisitos.length > 0) {
            venta.requisitos.forEach(r => {
                const esObligatorio = r.requisito_credito?.obligatorio ?? true;
                const reqBadge = r.estado === 'VALIDADO' ? 'bg-emerald-50 text-emerald-800 border-emerald-300' :
                                 r.estado === 'RECHAZADO' ? 'bg-red-50 text-red-800 border-red-300' :
                                 'bg-amber-50 text-amber-800 border-amber-300';

                requisitosHtml += `
                    <div class="p-3 rounded border border-slate-200 bg-white flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border ${reqBadge}">${r.estado}</span>
                                ${esObligatorio ? '<span class="text-[11px] text-red-600 font-semibold">Obligatorio</span>' : '<span class="text-[11px] text-slate-400">Opcional</span>'}
                                ${r.validado_por ? `<span class="text-[11px] text-slate-500 font-mono">(Dictaminado por: ${r.validado_por})</span>` : ''}
                            </div>
                            <p class="font-semibold text-slate-800">${r.requisito_credito?.descripcion || 'Requisito #' + r.requisito_id}</p>
                            ${r.soporte_adjunto ? `<p class="text-[11px] text-sky-700 font-mono">Archivo: ${r.soporte_adjunto}</p>` : '<p class="text-[11px] text-amber-700/80 font-medium">Pendiente por adjuntar soporte</p>'}
                        </div>

                        ${venta.estado === 'PAGADA' ? '<span class="text-xs text-slate-400 font-medium">Trámite Cerrado</span>' : `
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button onclick="evaluarRequisito('${venta.numero}', ${r.requisito_id}, 'VALIDAR')" class="px-2.5 py-1 rounded bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 text-xs font-bold transition flex items-center gap-1">
                                    <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i>
                                    <span>Validar</span>
                                </button>
                                <button onclick="evaluarRequisito('${venta.numero}', ${r.requisito_id}, 'RECHAZAR')" class="px-2.5 py-1 rounded bg-red-50 hover:bg-red-100 text-red-800 border border-red-300 text-xs font-bold transition flex items-center gap-1">
                                    <i data-lucide="x" class="w-3.5 h-3.5 text-red-600"></i>
                                    <span>Rechazar</span>
                                </button>
                            </div>
                        `}
                    </div>
                `;
            });
        }

        const motoInfo = venta.motocicletas && venta.motocicletas.length > 0
            ? `${venta.motocicletas[0].modelo_moto?.linea || venta.motocicletas[0].modelo_referencia} (VIN: ${venta.motocicletas[0].vin})`
            : 'Sin motocicleta asignada';

        contenedor.innerHTML = `
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 p-3.5 rounded bg-slate-50 border border-slate-200 text-xs">
                <div>
                    <span class="text-slate-500 font-medium">Cliente:</span>
                    <p class="font-bold text-slate-900 mt-0.5">${venta.cliente?.nombre_razon_social || venta.cliente_num_doc}</p>
                </div>
                <div>
                    <span class="text-slate-500 font-medium">Motocicleta Reservada:</span>
                    <p class="font-bold text-sky-800 mt-0.5">${motoInfo}</p>
                </div>
                <div>
                    <span class="text-slate-500 font-medium">Estado de la Venta:</span>
                    <p class="font-bold ${venta.estado === 'PAGADA' ? 'text-emerald-700' : 'text-amber-700'} mt-0.5">${venta.estado}</p>
                </div>
            </div>

            <div class="space-y-2">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">Documentación en Dictamen</span>
                ${requisitosHtml}
            </div>

            <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                <div class="text-slate-500">
                    ${venta.estado === 'PAGADA' ? 'Esta orden cuenta con crédito aprobado y pago formalizado.' : 'Todos los soportes obligatorios deben encontrarse en estado VALIDADO para emitir dictamen aprobatorio.'}
                </div>

                ${venta.estado === 'PAGADA' ? `
                    <span class="px-4 py-2 rounded bg-emerald-50 text-emerald-800 border border-emerald-300 font-bold text-xs flex items-center gap-1.5">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                        <span>CRÉDITO APROBADO Y FORMALIZADO</span>
                    </span>
                ` : `
                    <button onclick="aprobarCredito('${venta.numero}')" class="px-5 py-2.5 rounded bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5 active:translate-y-px">
                        <i data-lucide="award" class="w-4 h-4"></i>
                        <span>Emitir Aprobación Final de Crédito</span>
                    </button>
                `}
            </div>
        `;

        if (window.createLucideIcons) window.createLucideIcons();
    }

    // Evaluar requisito
    async function evaluarRequisito(ventaNumero, requisitoId, decision) {
        try {
            const res = await fetch(`/api/creditos/solicitudes/${ventaNumero}/requisitos/${requisitoId}/evaluar`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    decision: decision,
                    validador_username: 'analista_credito_motocons'
                })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                window.showToast(`Requisito dictaminado como ${decision}`, 'success');
                setTimeout(() => window.location.reload(), 700);
            } else {
                window.showToast(data.message || 'Error al dictaminar requisito', 'error');
            }
        } catch (err) {
            window.showToast('Error de conexión', 'error');
        }
    }

    // Aprobación final
    async function aprobarCredito(ventaNumero) {
        if (!confirm(`¿Confirma la emisión del dictamen de aprobación final para la orden ${ventaNumero}?`)) return;

        try {
            const res = await fetch(`/api/creditos/solicitudes/${ventaNumero}/aprobar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    aprobado_por: 'gerencia_credito_motocons'
                })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                window.showToast('Crédito aprobado exitosamente. Orden en estado PAGADA.', 'success');
                setTimeout(() => window.location.reload(), 1400);
            } else {
                window.showToast(data.message || 'La solicitud no cumple los requisitos para aprobación', 'error');
            }
        } catch (err) {
            window.showToast('Error de comunicación con el servicio', 'error');
        }
    }

    // Iniciar en la primera pestaña
    document.addEventListener('DOMContentLoaded', () => {
        switchTab('tab-nueva');
    });
</script>
@endpush
