@extends('layouts.app')

@section('title', 'MOTOCONS | Gestión de Créditos')

@section('content')
<div class="space-y-8">
    <!-- Encabezado y KPIs -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-white flex items-center gap-3">
                Módulo de Créditos de Vehículos
            </h1>
            <p class="text-slate-400 mt-1 text-sm">
                Gestión comercial, recepción de soportes y estudio financiero para convenios con 
                <span class="text-amber-400 font-semibold">Brilla</span> y 
                <span class="text-sky-400 font-semibold">ServiCrédito</span>.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button onclick="window.location.reload()" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-sm font-medium transition border border-slate-700 flex items-center gap-2">
                🔄 Refrescar Datos
            </button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-sm">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Trámites</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-black text-white">{{ $solicitudes->count() }}</span>
                <span class="text-xs text-slate-500">solicitudes</span>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-sm">
            <span class="text-xs font-semibold text-amber-400 uppercase tracking-wider">En Proceso Crédito</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-black text-amber-400">{{ $solicitudes->where('estado', 'EN_PROCESO_CREDITO')->count() }}</span>
                <span class="text-xs text-slate-500">por evaluar</span>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-sm">
            <span class="text-xs font-semibold text-emerald-400 uppercase tracking-wider">Créditos Aprobados</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-black text-emerald-400">{{ $solicitudes->where('estado', 'PAGADA')->count() }}</span>
                <span class="text-xs text-slate-500">listas entrega</span>
            </div>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 shadow-sm">
            <span class="text-xs font-semibold text-sky-400 uppercase tracking-wider">Motos en Patio</span>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="text-3xl font-black text-sky-400">{{ $motocicletasDisponibles->count() }}</span>
                <span class="text-xs text-slate-500">disponibles</span>
            </div>
        </div>
    </div>

    <!-- Navegación por Pestañas -->
    <div class="border-b border-slate-800">
        <nav class="flex gap-2 sm:gap-6 overflow-x-auto" aria-label="Tabs">
            <button onclick="switchTab('tab-nueva')" id="btn-tab-nueva" class="tab-button py-3 px-4 border-b-2 font-medium text-sm border-red-500 text-red-400 whitespace-nowrap flex items-center gap-2">
                <span>➕</span> Iniciar Solicitud
            </button>
            <button onclick="switchTab('tab-soportes')" id="btn-tab-soportes" class="tab-button py-3 px-4 border-b-2 font-medium text-sm border-transparent text-slate-400 hover:text-slate-300 hover:border-slate-700 whitespace-nowrap flex items-center gap-2">
                <span>📎</span> Cargar Soportes
            </button>
            <button onclick="switchTab('tab-evaluar')" id="btn-tab-evaluar" class="tab-button py-3 px-4 border-b-2 font-medium text-sm border-transparent text-slate-400 hover:text-slate-300 hover:border-slate-700 whitespace-nowrap flex items-center gap-2">
                <span>⚖️</span> Mesa de Control y Aprobación
            </button>
            <button onclick="switchTab('tab-catalogo')" id="btn-tab-catalogo" class="tab-button py-3 px-4 border-b-2 font-medium text-sm border-transparent text-slate-400 hover:text-slate-300 hover:border-slate-700 whitespace-nowrap flex items-center gap-2">
                <span>📋</span> Catálogo Financiero
            </button>
        </nav>
    </div>

    <!-- PESTAÑA 1: INICIAR SOLICITUD -->
    <div id="tab-nueva" class="tab-content block">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Formulario -->
            <div class="lg:col-span-2 bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
                <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-red-600/20 text-red-400 flex items-center justify-center text-sm font-bold">1</span>
                    Formulario de Radicación de Crédito
                </h2>

                <form id="form-iniciar-solicitud" onsubmit="iniciarSolicitud(event)" class="space-y-6">
                    @csrf
                    <!-- Selección de Cliente -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                            Cliente Comprador *
                        </label>
                        <select id="cliente_select" onchange="actualizarClienteDocs(this)" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">-- Seleccionar cliente registrado o escribir manualmente --</option>
                            @foreach($clientes as $cli)
                                <option value="{{ $cli->tipo_doc }}:{{ $cli->num_doc }}" data-tipo="{{ $cli->tipo_doc }}" data-num="{{ $cli->num_doc }}">
                                    {{ $cli->nombre_razon_social }} ({{ $cli->tipo_doc }} {{ $cli->num_doc }}) - {{ $cli->municipio }}
                                </option>
                            @endforeach
                        </select>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label class="block text-xs text-slate-400 mb-1">Tipo Documento</label>
                                <select id="cliente_tipo_doc" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    <option value="CC">Cédula de Ciudadanía (CC)</option>
                                    <option value="CE">Cédula de Extranjería (CE)</option>
                                    <option value="NIT">NIT Persona Jurídica</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-slate-400 mb-1">Número de Documento</label>
                                <input type="text" id="cliente_num_doc" required placeholder="Ej: 1065123456" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-red-500">
                            </div>
                        </div>
                    </div>

                    <!-- Selección de Entidad Financiera -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                            Convenio / Financiera *
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="relative flex items-center p-4 rounded-xl border border-slate-700 bg-slate-950/60 cursor-pointer hover:border-amber-500/50 transition">
                                <input type="radio" name="medio_pago" value="BRILLA" checked class="text-red-500 focus:ring-red-500 mr-3">
                                <div>
                                    <span class="block text-sm font-bold text-amber-400">Crédito Brilla</span>
                                    <span class="block text-xs text-slate-400">Gases del Caribe / Surtigas (Requiere recibo de gas)</span>
                                </div>
                            </label>

                            <label class="relative flex items-center p-4 rounded-xl border border-slate-700 bg-slate-950/60 cursor-pointer hover:border-sky-500/50 transition">
                                <input type="radio" name="medio_pago" value="SERVICREDITO" class="text-red-500 focus:ring-red-500 mr-3">
                                <div>
                                    <span class="block text-sm font-bold text-sky-400">ServiCrédito</span>
                                    <span class="block text-xs text-slate-400">Crédito por libranza o extractos bancarios</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Motocicleta a Financiar -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                            Motocicleta a Adquirir (Opcional - Reserva de Unidad)
                        </label>
                        <select id="vin_moto" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-slate-100 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">-- Sin moto preseleccionada (Trámite abierto) --</option>
                            @foreach($motocicletasDisponibles as $moto)
                                <option value="{{ $moto->vin }}">
                                    {{ $moto->modeloMoto->marca ?? '' }} {{ $moto->modeloMoto->linea ?? $moto->modelo_referencia }} 
                                    - Color: {{ $moto->color }} 
                                    - VIN: {{ $moto->vin }} 
                                    (${{ number_format($moto->precio_lista - $moto->descuento, 0, ',', '.') }} COP)
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Al seleccionarla, la motocicleta pasará a estado <strong>RESERVADA</strong> para el cliente.</p>
                    </div>

                    <!-- Canal y Asesor -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Canal de Venta</label>
                            <select id="canal" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2 text-sm text-slate-100">
                                <option value="PRESENCIAL">Presencial (Sala de Ventas)</option>
                                <option value="WEB">Canal Digital / Web</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-slate-400 mb-1">Asesor Responsable</label>
                            <select id="vendedor_select" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2 text-sm text-slate-100">
                                <option value="">-- Sin asesor asignado --</option>
                                @foreach($empleados as $emp)
                                    <option value="{{ $emp->tipo_doc }}:{{ $emp->num_doc }}">{{ $emp->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Botón de Envío -->
                    <button type="submit" id="btn-submit-solicitud" class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-400 text-white font-bold shadow-lg shadow-red-900/40 transition transform active:scale-95 flex items-center justify-center gap-2">
                        <span>🚀</span> Radicar Solicitud y Generar Requisitos
                    </button>
                </form>
            </div>

            <!-- Panel Lateral Informativo -->
            <div class="space-y-6">
                <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-5">
                    <h3 class="text-sm font-bold text-white mb-2 flex items-center gap-2">
                        <span>ℹ️</span> ¿Cómo funciona el crédito?
                    </h3>
                    <ul class="text-xs text-slate-400 space-y-2.5 leading-relaxed">
                        <li>1. Se registra la venta en estado <span class="px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 font-mono">EN_PROCESO_CREDITO</span>.</li>
                        <li>2. El sistema autogenera los requisitos en estado <span class="px-1.5 py-0.5 rounded bg-slate-800 text-slate-300 font-mono">PENDIENTE</span> según la financiera.</li>
                        <li>3. Se cargan los soportes PDF o imágenes en la pestaña de <strong>Cargar Soportes</strong>.</li>
                        <li>4. El analista aprueba los requisitos y el sistema emite la aprobación final.</li>
                    </ul>
                </div>

                <div class="bg-gradient-to-br from-amber-950/30 to-slate-900 border border-amber-900/30 rounded-2xl p-5">
                    <h3 class="text-sm font-bold text-amber-300 mb-2">🔥 Regla de Dominio: Brilla</h3>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Para todo crédito con <strong>Brilla</strong>, es mandatario contar con el recibo de gas cancelado y validado. De lo contrario, la arquitectura dispara la excepción de negocio <code>RequisitoCreditoInvalidoException</code> impidiendo la aprobación.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- PESTAÑA 2: CARGAR SOPORTES -->
    <div id="tab-soportes" class="tab-content hidden space-y-6">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-sky-600/20 text-sky-400 flex items-center justify-center text-sm font-bold">2</span>
                        Adjuntar Evidencias y Documentos
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Selecciona una venta en estudio para cargar los documentos solicitados.</p>
                </div>

                <!-- Selector de Venta Activa -->
                <div class="min-w-[280px]">
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Seleccionar Trámite de Venta:</label>
                    <select id="selector_venta_soportes" onchange="cargarDetalleVenta(this.value)" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2 text-sm text-slate-100 font-mono focus:ring-2 focus:ring-sky-500">
                        @forelse($solicitudes as $sol)
                            <option value="{{ $sol->numero }}">
                                {{ $sol->numero }} ({{ $sol->cliente->nombre_razon_social ?? $sol->cliente_num_doc }}) - {{ $sol->estado }}
                            </option>
                        @empty
                            <option value="">No hay ventas registradas</option>
                        @endforelse
                    </select>
                </div>
            </div>

            <!-- Listado de Requisitos para Subida -->
            <div class="mt-6">
                <div id="contenedor-requisitos-soportes" class="space-y-4">
                    <!-- Se rellena dinámicamente vía JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- PESTAÑA 3: MESA DE CONTROL Y APROBACIÓN -->
    <div id="tab-evaluar" class="tab-content hidden space-y-6">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-emerald-600/20 text-emerald-400 flex items-center justify-center text-sm font-bold">3</span>
                        Mesa de Control Financiero (Analista de Crédito)
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Revisa los soportes adjuntos, valida/rechaza requisitos y emite la aprobación final.</p>
                </div>

                <!-- Selector de Venta Activa -->
                <div class="min-w-[280px]">
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Trámite a Evaluar:</label>
                    <select id="selector_venta_evaluar" onchange="cargarDetalleEvaluacion(this.value)" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2 text-sm text-slate-100 font-mono focus:ring-2 focus:ring-emerald-500">
                        @forelse($solicitudes as $sol)
                            <option value="{{ $sol->numero }}">
                                {{ $sol->numero }} ({{ $sol->cliente->nombre_razon_social ?? $sol->cliente_num_doc }}) - {{ $sol->estado }}
                            </option>
                        @empty
                            <option value="">No hay ventas registradas</option>
                        @endforelse
                    </select>
                </div>
            </div>

            <!-- Panel de Evaluación Dinámico -->
            <div id="contenedor-evaluacion" class="mt-6 space-y-6">
                <!-- Se rellena dinámicamente vía JavaScript -->
            </div>
        </div>
    </div>

    <!-- PESTAÑA 4: CATÁLOGO DE REQUISITOS -->
    <div id="tab-catalogo" class="tab-content hidden space-y-6">
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <h2 class="text-lg font-bold text-white mb-2 flex items-center gap-2">
                <span>📚</span> Catálogo Paramétrico de Requisitos de Crédito
            </h2>
            <p class="text-xs text-slate-400 mb-6">Parametrización por entidad financiera registrada en PostgreSQL.</p>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-800 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Convenio / Entidad</th>
                            <th class="py-3 px-4">Documento / Descripción</th>
                            <th class="py-3 px-4 text-center">Obligatorio</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        @forelse($requisitosCatalogo as $req)
                            <tr class="hover:bg-slate-800/30 transition">
                                <td class="py-3.5 px-4 font-mono text-xs text-slate-500">#{{ $req->id }}</td>
                                <td class="py-3.5 px-4 font-semibold {{ $req->medio_pago === 'BRILLA' ? 'text-amber-400' : 'text-sky-400' }}">
                                    {{ $req->medio_pago }}
                                </td>
                                <td class="py-3.5 px-4">{{ $req->descripcion }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($req->obligatorio)
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">Si (Mandatorio)</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-slate-800 text-slate-400 border border-slate-700">Opcional</span>
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
@endsection

@push('scripts')
<script>
    // Gestión de Pestañas
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('border-red-500', 'text-red-400');
            btn.classList.add('border-transparent', 'text-slate-400');
        });

        const activeTab = document.getElementById(tabId);
        const activeBtn = document.getElementById('btn-' + tabId);
        if (activeTab) activeTab.classList.remove('hidden');
        if (activeBtn) {
            activeBtn.classList.remove('border-transparent', 'text-slate-400');
            activeBtn.classList.add('border-red-500', 'text-red-400');
        }

        if (tabId === 'tab-soportes') {
            const v = document.getElementById('selector_venta_soportes').value;
            if (v) cargarDetalleVenta(v);
        } else if (tabId === 'tab-evaluar') {
            const v = document.getElementById('selector_venta_evaluar').value;
            if (v) cargarDetalleEvaluacion(v);
        }
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
        btn.innerHTML = '⏳ Procesando en Backend...';

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
                window.showToast(`¡Solicitud ${data.data.venta_numero} iniciada con éxito!`, 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                window.showToast(data.message || 'Error al iniciar la solicitud', 'error');
            }
        } catch (err) {
            window.showToast('Error de conexión con el servidor', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<span>🚀</span> Radicar Solicitud y Generar Requisitos';
        }
    }

    // Datos precargados de ventas para interactividad rápida
    const todasLasVentas = @json($solicitudes);

    // Cargar Detalle para Subida de Soportes
    function cargarDetalleVenta(numeroVenta) {
        const contenedor = document.getElementById('contenedor-requisitos-soportes');
        if (!numeroVenta) {
            contenedor.innerHTML = '<p class="text-slate-500 text-sm">Selecciona una venta.</p>';
            return;
        }

        const venta = todasLasVentas.find(v => v.numero === numeroVenta);
        if (!venta) {
            contenedor.innerHTML = '<p class="text-slate-500 text-sm">Venta no encontrada.</p>';
            return;
        }

        let html = `
            <div class="bg-slate-950/60 p-4 rounded-xl border border-slate-800 flex flex-wrap items-center justify-between gap-4 mb-4">
                <div>
                    <span class="text-xs text-slate-400">Venta:</span> <strong class="font-mono text-white">${venta.numero}</strong>
                    <span class="mx-2">•</span>
                    <span class="text-xs text-slate-400">Cliente:</span> <span class="text-slate-200">${venta.cliente?.nombre_razon_social || venta.cliente_num_doc}</span>
                </div>
                <div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold ${
                        venta.estado === 'PAGADA' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' :
                        'bg-amber-500/20 text-amber-400 border border-amber-500/30'
                    }">${venta.estado}</span>
                </div>
            </div>
            <div class="space-y-3">
        `;

        if (!venta.requisitos || venta.requisitos.length === 0) {
            html += '<p class="text-slate-500 text-xs">Esta venta no tiene requisitos de crédito asignados.</p>';
        } else {
            venta.requisitos.forEach(r => {
                const badgeColor = r.estado === 'VALIDADO' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' :
                                  r.estado === 'RECHAZADO' ? 'bg-red-500/10 text-red-400 border-red-500/30' :
                                  'bg-amber-500/10 text-amber-400 border-amber-500/30';

                html += `
                    <div class="p-4 rounded-xl border border-slate-800 bg-slate-950/40 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="space-y-1 max-w-xl">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold ${badgeColor} border">${r.estado}</span>
                                ${r.requisito_credito?.obligatorio ? '<span class="text-xs text-red-400 font-medium">* Obligatorio</span>' : ''}
                            </div>
                            <p class="text-sm font-semibold text-slate-200">${r.requisito_credito?.descripcion || 'Requisito #' + r.requisito_id}</p>
                            ${r.soporte_adjunto ? `<p class="text-xs text-emerald-400 font-mono">📁 Archivo: ${r.soporte_adjunto}</p>` : '<p class="text-xs text-slate-500">Sin archivo adjunto todavía.</p>'}
                        </div>

                        <div class="flex items-center gap-2">
                            <form onsubmit="subirSoporte(event, '${venta.numero}', ${r.requisito_id})" class="flex items-center gap-2">
                                <label class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-medium cursor-pointer border border-slate-700 transition">
                                    <span>Elegir Archivo</span>
                                    <input type="file" name="archivo" required accept=".pdf,.png,.jpg,.jpeg" class="hidden" onchange="this.parentElement.querySelector('span').innerText = this.files[0]?.name || 'Elegir Archivo'">
                                </label>
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold transition">
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
    }

    // Subir soporte vía AJAX
    async function subirSoporte(e, ventaNumero, requisitoId) {
        e.preventDefault();
        const form = e.target;
        const fileInput = form.querySelector('input[type="file"]');
        if (!fileInput.files || fileInput.files.length === 0) {
            window.showToast('Por favor selecciona un archivo', 'error');
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
                window.showToast('¡Archivo soporte adjuntado con éxito!', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                window.showToast(data.message || 'Error al subir archivo', 'error');
            }
        } catch (err) {
            window.showToast('Error de red al subir archivo', 'error');
        }
    }

    // Cargar Detalle para Evaluación / Mesa de Control
    function cargarDetalleEvaluacion(numeroVenta) {
        const contenedor = document.getElementById('contenedor-evaluacion');
        if (!numeroVenta) {
            contenedor.innerHTML = '<p class="text-slate-500 text-sm">Selecciona una venta.</p>';
            return;
        }

        const venta = todasLasVentas.find(v => v.numero === numeroVenta);
        if (!venta) {
            contenedor.innerHTML = '<p class="text-slate-500 text-sm">Venta no encontrada.</p>';
            return;
        }

        let requisitosHtml = '';
        let todosObligatoriosValidados = true;

        if (venta.requisitos && venta.requisitos.length > 0) {
            venta.requisitos.forEach(r => {
                const esObligatorio = r.requisito_credito?.obligatorio ?? true;
                const estaValidado = r.estado === 'VALIDADO';
                if (esObligatorio && !estaValidado) {
                    todosObligatoriosValidados = false;
                }

                const badgeColor = r.estado === 'VALIDADO' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' :
                                  r.estado === 'RECHAZADO' ? 'bg-red-500/10 text-red-400 border-red-500/30' :
                                  'bg-amber-500/10 text-amber-400 border-amber-500/30';

                requisitosHtml += `
                    <div class="p-4 rounded-xl border border-slate-800 bg-slate-950/40 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold ${badgeColor} border">${r.estado}</span>
                                ${esObligatorio ? '<span class="text-xs text-red-400 font-medium">Obligatorio</span>' : '<span class="text-xs text-slate-500">Opcional</span>'}
                                ${r.validado_por ? `<span class="text-xs text-slate-400 font-mono">(Evaluado por: ${r.validado_por})</span>` : ''}
                            </div>
                            <p class="text-sm font-semibold text-slate-200">${r.requisito_credito?.descripcion || 'Requisito #' + r.requisito_id}</p>
                            ${r.soporte_adjunto ? `<p class="text-xs text-sky-400 font-mono">📄 Soporte: ${r.soporte_adjunto}</p>` : '<p class="text-xs text-amber-400/80">⚠️ Sin documento adjunto</p>'}
                        </div>

                        ${venta.estado === 'PAGADA' ? '<span class="text-xs text-slate-500 font-medium">Trámite Finalizado</span>' : `
                            <div class="flex items-center gap-2">
                                <button onclick="evaluarRequisito('${venta.numero}', ${r.requisito_id}, 'VALIDAR')" class="px-3 py-1.5 rounded-lg bg-emerald-600/20 hover:bg-emerald-600 text-emerald-300 hover:text-white border border-emerald-500/30 text-xs font-bold transition">
                                    ✅ Validar
                                </button>
                                <button onclick="evaluarRequisito('${venta.numero}', ${r.requisito_id}, 'RECHAZAR')" class="px-3 py-1.5 rounded-lg bg-red-600/20 hover:bg-red-600 text-red-300 hover:text-white border border-red-500/30 text-xs font-bold transition">
                                    ❌ Rechazar
                                </button>
                            </div>
                        `}
                    </div>
                `;
            });
        }

        const motoInfo = venta.motocicletas && venta.motocicletas.length > 0 
            ? `${venta.motocicletas[0].modelo_moto?.linea || venta.motocicletas[0].modelo_referencia} (${venta.motocicletas[0].vin})`
            : 'Sin moto asignada';

        contenedor.innerHTML = `
            <!-- Resumen del Crédito -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-xl bg-slate-950/70 border border-slate-800">
                <div>
                    <span class="text-xs text-slate-400">Cliente:</span>
                    <p class="text-sm font-bold text-white">${venta.cliente?.nombre_razon_social || venta.cliente_num_doc}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400">Moto Reservada:</span>
                    <p class="text-sm font-bold text-sky-400">${motoInfo}</p>
                </div>
                <div>
                    <span class="text-xs text-slate-400">Estado Actual de la Venta:</span>
                    <p class="text-sm font-bold ${venta.estado === 'PAGADA' ? 'text-emerald-400' : 'text-amber-400'}">${venta.estado}</p>
                </div>
            </div>

            <!-- Listado para Evaluar -->
            <div class="space-y-3">
                <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Documentación a Revisar</h4>
                ${requisitosHtml}
            </div>

            <!-- Botón de Aprobación Final -->
            <div class="pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-400">
                    ${venta.estado === 'PAGADA' ? '🎉 Esta venta ya ha sido aprobada y registrada como PAGADA.' : 'Todos los requisitos obligatorios deben estar en estado VALIDADO para poder aprobar.'}
                </div>

                ${venta.estado === 'PAGADA' ? `
                    <span class="px-6 py-3 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 font-bold text-sm">
                        ✅ CRÉDITO APROBADO Y PAGADO
                    </span>
                ` : `
                    <button onclick="aprobarCredito('${venta.numero}')" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-extrabold shadow-lg shadow-emerald-900/40 transition transform active:scale-95 flex items-center gap-2">
                        <span>🏛️</span> Emitir Aprobación Final de Crédito
                    </button>
                `}
            </div>
        `;
    }

    // Evaluar Requisito Individual (Validar/Rechazar)
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
                window.showToast(`Requisito evaluado exitosamente: ${decision}`, 'success');
                setTimeout(() => window.location.reload(), 800);
            } else {
                window.showToast(data.message || 'Error al evaluar requisito', 'error');
            }
        } catch (err) {
            window.showToast('Error de conexión', 'error');
        }
    }

    // Aprobación Final del Crédito
    async function aprobarCredito(ventaNumero) {
        if (!confirm(`¿Confirmas la aprobación final del crédito para la venta ${ventaNumero}?`)) return;

        try {
            const res = await fetch(`/api/creditos/solicitudes/${ventaNumero}/aprobar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    aprobado_por: 'gerente_credito_motocons'
                })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                window.showToast('🎉 ¡CRÉDITO APROBADO EXITOSAMENTE! Venta en estado PAGADA.', 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                // Alerta explicativa de regla de negocio
                window.showToast(data.message || 'No cumple las condiciones para aprobación', 'error');
            }
        } catch (err) {
            window.showToast('Error de conexión', 'error');
        }
    }

    // Inicializar pestaña por defecto al cargar
    document.addEventListener('DOMContentLoaded', () => {
        switchTab('tab-nueva');
    });
</script>
@endpush
