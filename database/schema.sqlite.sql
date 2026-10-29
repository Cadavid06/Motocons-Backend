-- MOTOCONS - Esquema SQLite (PHP puro / PDO)

CREATE TABLE cliente (
    tipo_doc TEXT NOT NULL,
    num_doc TEXT NOT NULL,
    nombre_razon_social TEXT NOT NULL,
    email_facturacion TEXT,
    telefono TEXT,
    direccion TEXT,
    municipio TEXT,
    autorizacion_datos_fecha TEXT,
    autorizacion_datos_version TEXT,
    PRIMARY KEY (tipo_doc, num_doc)
);

CREATE TABLE empleado (
    tipo_doc TEXT NOT NULL,
    num_doc TEXT NOT NULL,
    nombre TEXT NOT NULL,
    tipo_vinculacion TEXT,
    PRIMARY KEY (tipo_doc, num_doc)
);

CREATE TABLE proveedor (
    nit TEXT PRIMARY KEY,
    razon_social TEXT NOT NULL,
    email_ordenes TEXT
);

CREATE TABLE usuario (
    username TEXT PRIMARY KEY,
    password_hash TEXT NOT NULL,
    rol TEXT NOT NULL,
    activo INTEGER NOT NULL DEFAULT 1,
    cliente_tipo_doc TEXT,
    cliente_num_doc TEXT,
    empleado_tipo_doc TEXT,
    empleado_num_doc TEXT,
    UNIQUE (cliente_tipo_doc, cliente_num_doc),
    UNIQUE (empleado_tipo_doc, empleado_num_doc),
    FOREIGN KEY (cliente_tipo_doc, cliente_num_doc)
        REFERENCES cliente (tipo_doc, num_doc)
        ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (empleado_tipo_doc, empleado_num_doc)
        REFERENCES empleado (tipo_doc, num_doc)
        ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE auditoria (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    fecha_hora TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    accion TEXT NOT NULL,
    entidad TEXT NOT NULL,
    registro_id TEXT NOT NULL,
    datos_antes TEXT,
    datos_despues TEXT,
    ip_origen TEXT,
    username TEXT,
    FOREIGN KEY (username) REFERENCES usuario (username)
        ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE producto (
    referencia TEXT PRIMARY KEY,
    nombre TEXT NOT NULL,
    tipo TEXT NOT NULL,
    precio_vigente NUMERIC(14, 2) NOT NULL,
    tarifa_iva NUMERIC(5, 2) NOT NULL DEFAULT 0,
    tarifa_inc NUMERIC(5, 2) NOT NULL DEFAULT 0
);

CREATE TABLE modelo_moto (
    referencia TEXT PRIMARY KEY,
    marca TEXT NOT NULL,
    linea TEXT NOT NULL,
    anio INTEGER NOT NULL,
    gama TEXT NOT NULL,
    propulsion TEXT NOT NULL,
    cilindraje_cc INTEGER,
    ficha_tecnica TEXT,
    FOREIGN KEY (referencia) REFERENCES producto (referencia)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE promocion (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tipo TEXT NOT NULL,
    valor NUMERIC(14, 2) NOT NULL,
    fecha_inicio TEXT NOT NULL,
    fecha_fin TEXT NOT NULL,
    gama_objetivo TEXT,
    propulsion_objetivo TEXT
);

CREATE TABLE orden_compra (
    numero TEXT PRIMARY KEY,
    fecha TEXT NOT NULL,
    estado TEXT NOT NULL,
    proveedor_nit TEXT NOT NULL,
    FOREIGN KEY (proveedor_nit) REFERENCES proveedor (nit)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE orden_compra_producto (
    orden_compra_numero TEXT NOT NULL,
    producto_referencia TEXT NOT NULL,
    cantidad INTEGER NOT NULL,
    costo NUMERIC(14, 2) NOT NULL,
    PRIMARY KEY (orden_compra_numero, producto_referencia),
    FOREIGN KEY (orden_compra_numero) REFERENCES orden_compra (numero)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (producto_referencia) REFERENCES producto (referencia)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE recepcion (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    fecha TEXT NOT NULL,
    cufe_factura_proveedor TEXT,
    orden_compra_numero TEXT NOT NULL,
    FOREIGN KEY (orden_compra_numero) REFERENCES orden_compra (numero)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE recepcion_producto (
    recepcion_id INTEGER NOT NULL,
    producto_referencia TEXT NOT NULL,
    cantidad_recibida INTEGER NOT NULL,
    PRIMARY KEY (recepcion_id, producto_referencia),
    FOREIGN KEY (recepcion_id) REFERENCES recepcion (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (producto_referencia) REFERENCES producto (referencia)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE requisito_credito (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    medio_pago TEXT NOT NULL,
    descripcion TEXT NOT NULL,
    obligatorio INTEGER NOT NULL DEFAULT 1
);

CREATE TABLE resolucion_dian (
    numero_resolucion TEXT PRIMARY KEY,
    prefijo TEXT,
    rango_desde INTEGER NOT NULL,
    rango_hasta INTEGER NOT NULL,
    vigencia_desde TEXT NOT NULL,
    vigencia_hasta TEXT NOT NULL
);

CREATE TABLE cierre (
    tipo TEXT NOT NULL,
    fecha_inicio TEXT NOT NULL,
    fecha_fin TEXT NOT NULL,
    fecha_cierre TEXT NOT NULL,
    PRIMARY KEY (tipo, fecha_inicio)
);

CREATE TABLE liquidacion_comision (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    periodo TEXT NOT NULL,
    estado TEXT NOT NULL,
    empleado_tipo_doc TEXT NOT NULL,
    empleado_num_doc TEXT NOT NULL,
    FOREIGN KEY (empleado_tipo_doc, empleado_num_doc)
        REFERENCES empleado (tipo_doc, num_doc)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE venta (
    numero TEXT PRIMARY KEY,
    fecha TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    canal TEXT NOT NULL,
    estado TEXT NOT NULL,
    aceptacion_terminos TEXT,
    cliente_tipo_doc TEXT NOT NULL,
    cliente_num_doc TEXT NOT NULL,
    vendedor_tipo_doc TEXT,
    vendedor_num_doc TEXT,
    promocion_id INTEGER,
    cierre_tipo TEXT,
    cierre_fecha_inicio TEXT,
    liquidacion_id INTEGER,
    comision_porcentaje NUMERIC(5, 2),
    comision_valor NUMERIC(14, 2),
    FOREIGN KEY (cliente_tipo_doc, cliente_num_doc)
        REFERENCES cliente (tipo_doc, num_doc)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (vendedor_tipo_doc, vendedor_num_doc)
        REFERENCES empleado (tipo_doc, num_doc)
        ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (promocion_id) REFERENCES promocion (id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (cierre_tipo, cierre_fecha_inicio)
        REFERENCES cierre (tipo, fecha_inicio)
        ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (liquidacion_id) REFERENCES liquidacion_comision (id)
        ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE motocicleta (
    vin TEXT PRIMARY KEY,
    numero_motor TEXT NOT NULL,
    color TEXT NOT NULL,
    placa TEXT,
    estado TEXT NOT NULL,
    garantia_hasta TEXT,
    modelo_referencia TEXT NOT NULL,
    recepcion_id INTEGER,
    venta_numero TEXT,
    precio_lista NUMERIC(14, 2) NOT NULL,
    descuento NUMERIC(14, 2) NOT NULL DEFAULT 0,
    FOREIGN KEY (modelo_referencia) REFERENCES modelo_moto (referencia)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (recepcion_id) REFERENCES recepcion (id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (venta_numero) REFERENCES venta (numero)
        ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE venta_requisito (
    venta_numero TEXT NOT NULL,
    requisito_id INTEGER NOT NULL,
    estado TEXT NOT NULL DEFAULT 'PENDIENTE',
    soporte_adjunto TEXT,
    validado_por TEXT,
    PRIMARY KEY (venta_numero, requisito_id),
    FOREIGN KEY (venta_numero) REFERENCES venta (numero)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (requisito_id) REFERENCES requisito_credito (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE orden_servicio (
    numero TEXT PRIMARY KEY,
    fecha_ingreso TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    kilometraje INTEGER NOT NULL,
    tipo TEXT NOT NULL,
    nro_mant_garantia INTEGER,
    diagnostico TEXT,
    motocicleta_vin TEXT NOT NULL,
    mecanico_tipo_doc TEXT,
    mecanico_num_doc TEXT,
    FOREIGN KEY (motocicleta_vin) REFERENCES motocicleta (vin)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (mecanico_tipo_doc, mecanico_num_doc)
        REFERENCES empleado (tipo_doc, num_doc)
        ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE orden_servicio_producto (
    orden_servicio_numero TEXT NOT NULL,
    producto_referencia TEXT NOT NULL,
    cantidad NUMERIC(10, 2) NOT NULL,
    valor_unit NUMERIC(14, 2) NOT NULL,
    PRIMARY KEY (orden_servicio_numero, producto_referencia),
    FOREIGN KEY (orden_servicio_numero) REFERENCES orden_servicio (numero)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (producto_referencia) REFERENCES producto (referencia)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE pago (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    medio TEXT NOT NULL,
    valor NUMERIC(14, 2) NOT NULL,
    fecha TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    referencia_autorizacion TEXT,
    estado TEXT NOT NULL,
    venta_numero TEXT,
    orden_servicio_numero TEXT,
    cierre_tipo TEXT,
    cierre_fecha_inicio TEXT,
    FOREIGN KEY (venta_numero) REFERENCES venta (numero)
        ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (orden_servicio_numero) REFERENCES orden_servicio (numero)
        ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (cierre_tipo, cierre_fecha_inicio)
        REFERENCES cierre (tipo, fecha_inicio)
        ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE documento_fiscal (
    prefijo TEXT NOT NULL,
    consecutivo INTEGER NOT NULL,
    tipo TEXT NOT NULL,
    fecha_hora_emision TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cufe_cude TEXT UNIQUE,
    xml_firmado TEXT,
    qr TEXT,
    estado_dian TEXT NOT NULL,
    forma_pago TEXT NOT NULL,
    cliente_tipo_doc TEXT NOT NULL,
    cliente_num_doc TEXT NOT NULL,
    venta_numero TEXT,
    orden_servicio_numero TEXT,
    numero_resolucion TEXT,
    corrige_prefijo TEXT,
    corrige_consecutivo INTEGER,
    PRIMARY KEY (prefijo, consecutivo),
    FOREIGN KEY (cliente_tipo_doc, cliente_num_doc)
        REFERENCES cliente (tipo_doc, num_doc)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (venta_numero) REFERENCES venta (numero)
        ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (orden_servicio_numero) REFERENCES orden_servicio (numero)
        ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (numero_resolucion) REFERENCES resolucion_dian (numero_resolucion)
        ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (corrige_prefijo, corrige_consecutivo)
        REFERENCES documento_fiscal (prefijo, consecutivo)
        ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE documento_fiscal_detalle (
    prefijo TEXT NOT NULL,
    consecutivo INTEGER NOT NULL,
    producto_referencia TEXT NOT NULL,
    cantidad NUMERIC(10, 2) NOT NULL,
    valor_unit NUMERIC(14, 2) NOT NULL,
    descuento NUMERIC(14, 2) NOT NULL DEFAULT 0,
    iva NUMERIC(14, 2) NOT NULL DEFAULT 0,
    inc NUMERIC(14, 2) NOT NULL DEFAULT 0,
    es_gratuita INTEGER NOT NULL DEFAULT 0,
    PRIMARY KEY (prefijo, consecutivo, producto_referencia),
    FOREIGN KEY (prefijo, consecutivo) REFERENCES documento_fiscal (prefijo, consecutivo)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (producto_referencia) REFERENCES producto (referencia)
        ON UPDATE CASCADE ON DELETE RESTRICT
);
