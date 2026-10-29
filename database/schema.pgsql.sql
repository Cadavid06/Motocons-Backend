-- MOTOCONS - Esquema PostgreSQL (PHP puro / PDO)

CREATE TABLE cliente (
    tipo_doc VARCHAR(20) NOT NULL,
    num_doc VARCHAR(30) NOT NULL,
    nombre_razon_social VARCHAR(255) NOT NULL,
    email_facturacion VARCHAR(255),
    telefono VARCHAR(50),
    direccion VARCHAR(255),
    municipio VARCHAR(255),
    autorizacion_datos_fecha TIMESTAMP,
    autorizacion_datos_version VARCHAR(255),
    PRIMARY KEY (tipo_doc, num_doc)
);

CREATE TABLE empleado (
    tipo_doc VARCHAR(20) NOT NULL,
    num_doc VARCHAR(30) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    tipo_vinculacion VARCHAR(255),
    PRIMARY KEY (tipo_doc, num_doc)
);

CREATE TABLE proveedor (
    nit VARCHAR(50) PRIMARY KEY,
    razon_social VARCHAR(255) NOT NULL,
    email_ordenes VARCHAR(255)
);

CREATE TABLE usuario (
    username VARCHAR(255) PRIMARY KEY,
    password_hash VARCHAR(255) NOT NULL,
    rol VARCHAR(50) NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    cliente_tipo_doc VARCHAR(20),
    cliente_num_doc VARCHAR(30),
    empleado_tipo_doc VARCHAR(20),
    empleado_num_doc VARCHAR(30),
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
    id BIGSERIAL PRIMARY KEY,
    fecha_hora TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    accion VARCHAR(50) NOT NULL,
    entidad VARCHAR(100) NOT NULL,
    registro_id VARCHAR(255) NOT NULL,
    datos_antes TEXT,
    datos_despues TEXT,
    ip_origen VARCHAR(50),
    username VARCHAR(255),
    FOREIGN KEY (username) REFERENCES usuario (username)
        ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE producto (
    referencia VARCHAR(100) PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    precio_vigente NUMERIC(14, 2) NOT NULL,
    tarifa_iva NUMERIC(5, 2) NOT NULL DEFAULT 0,
    tarifa_inc NUMERIC(5, 2) NOT NULL DEFAULT 0
);

CREATE TABLE modelo_moto (
    referencia VARCHAR(100) PRIMARY KEY,
    marca VARCHAR(100) NOT NULL,
    linea VARCHAR(100) NOT NULL,
    anio INTEGER NOT NULL,
    gama VARCHAR(50) NOT NULL,
    propulsion VARCHAR(50) NOT NULL,
    cilindraje_cc INTEGER,
    ficha_tecnica TEXT,
    FOREIGN KEY (referencia) REFERENCES producto (referencia)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE promocion (
    id BIGSERIAL PRIMARY KEY,
    tipo VARCHAR(50) NOT NULL,
    valor NUMERIC(14, 2) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    gama_objetivo VARCHAR(50),
    propulsion_objetivo VARCHAR(50)
);

CREATE TABLE orden_compra (
    numero VARCHAR(100) PRIMARY KEY,
    fecha DATE NOT NULL,
    estado VARCHAR(50) NOT NULL,
    proveedor_nit VARCHAR(50) NOT NULL,
    FOREIGN KEY (proveedor_nit) REFERENCES proveedor (nit)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE orden_compra_producto (
    orden_compra_numero VARCHAR(100) NOT NULL,
    producto_referencia VARCHAR(100) NOT NULL,
    cantidad INTEGER NOT NULL,
    costo NUMERIC(14, 2) NOT NULL,
    PRIMARY KEY (orden_compra_numero, producto_referencia),
    FOREIGN KEY (orden_compra_numero) REFERENCES orden_compra (numero)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (producto_referencia) REFERENCES producto (referencia)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE recepcion (
    id BIGSERIAL PRIMARY KEY,
    fecha DATE NOT NULL,
    cufe_factura_proveedor VARCHAR(255),
    orden_compra_numero VARCHAR(100) NOT NULL,
    FOREIGN KEY (orden_compra_numero) REFERENCES orden_compra (numero)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE recepcion_producto (
    recepcion_id BIGINT NOT NULL,
    producto_referencia VARCHAR(100) NOT NULL,
    cantidad_recibida INTEGER NOT NULL,
    PRIMARY KEY (recepcion_id, producto_referencia),
    FOREIGN KEY (recepcion_id) REFERENCES recepcion (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (producto_referencia) REFERENCES producto (referencia)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE requisito_credito (
    id BIGSERIAL PRIMARY KEY,
    medio_pago VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    obligatorio BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE resolucion_dian (
    numero_resolucion VARCHAR(100) PRIMARY KEY,
    prefijo VARCHAR(50),
    rango_desde BIGINT NOT NULL,
    rango_hasta BIGINT NOT NULL,
    vigencia_desde DATE NOT NULL,
    vigencia_hasta DATE NOT NULL
);

CREATE TABLE cierre (
    tipo VARCHAR(50) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    fecha_cierre TIMESTAMP NOT NULL,
    PRIMARY KEY (tipo, fecha_inicio)
);

CREATE TABLE liquidacion_comision (
    id BIGSERIAL PRIMARY KEY,
    periodo VARCHAR(50) NOT NULL,
    estado VARCHAR(50) NOT NULL,
    empleado_tipo_doc VARCHAR(20) NOT NULL,
    empleado_num_doc VARCHAR(30) NOT NULL,
    FOREIGN KEY (empleado_tipo_doc, empleado_num_doc)
        REFERENCES empleado (tipo_doc, num_doc)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE venta (
    numero VARCHAR(50) PRIMARY KEY,
    fecha TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    canal VARCHAR(50) NOT NULL,
    estado VARCHAR(50) NOT NULL,
    aceptacion_terminos TIMESTAMP,
    cliente_tipo_doc VARCHAR(20) NOT NULL,
    cliente_num_doc VARCHAR(30) NOT NULL,
    vendedor_tipo_doc VARCHAR(20),
    vendedor_num_doc VARCHAR(30),
    promocion_id BIGINT,
    cierre_tipo VARCHAR(50),
    cierre_fecha_inicio DATE,
    liquidacion_id BIGINT,
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
    vin VARCHAR(100) PRIMARY KEY,
    numero_motor VARCHAR(100) NOT NULL,
    color VARCHAR(100) NOT NULL,
    placa VARCHAR(50),
    estado VARCHAR(50) NOT NULL,
    garantia_hasta DATE,
    modelo_referencia VARCHAR(100) NOT NULL,
    recepcion_id BIGINT,
    venta_numero VARCHAR(50),
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
    venta_numero VARCHAR(50) NOT NULL,
    requisito_id BIGINT NOT NULL,
    estado VARCHAR(50) NOT NULL DEFAULT 'PENDIENTE',
    soporte_adjunto VARCHAR(255),
    validado_por VARCHAR(255),
    PRIMARY KEY (venta_numero, requisito_id),
    FOREIGN KEY (venta_numero) REFERENCES venta (numero)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (requisito_id) REFERENCES requisito_credito (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE orden_servicio (
    numero VARCHAR(100) PRIMARY KEY,
    fecha_ingreso TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    kilometraje INTEGER NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    nro_mant_garantia SMALLINT,
    diagnostico TEXT,
    motocicleta_vin VARCHAR(100) NOT NULL,
    mecanico_tipo_doc VARCHAR(20),
    mecanico_num_doc VARCHAR(30),
    FOREIGN KEY (motocicleta_vin) REFERENCES motocicleta (vin)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (mecanico_tipo_doc, mecanico_num_doc)
        REFERENCES empleado (tipo_doc, num_doc)
        ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE orden_servicio_producto (
    orden_servicio_numero VARCHAR(100) NOT NULL,
    producto_referencia VARCHAR(100) NOT NULL,
    cantidad NUMERIC(10, 2) NOT NULL,
    valor_unit NUMERIC(14, 2) NOT NULL,
    PRIMARY KEY (orden_servicio_numero, producto_referencia),
    FOREIGN KEY (orden_servicio_numero) REFERENCES orden_servicio (numero)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (producto_referencia) REFERENCES producto (referencia)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

CREATE TABLE pago (
    id BIGSERIAL PRIMARY KEY,
    medio VARCHAR(50) NOT NULL,
    valor NUMERIC(14, 2) NOT NULL,
    fecha TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    referencia_autorizacion VARCHAR(255),
    estado VARCHAR(50) NOT NULL,
    venta_numero VARCHAR(50),
    orden_servicio_numero VARCHAR(100),
    cierre_tipo VARCHAR(50),
    cierre_fecha_inicio DATE,
    FOREIGN KEY (venta_numero) REFERENCES venta (numero)
        ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (orden_servicio_numero) REFERENCES orden_servicio (numero)
        ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (cierre_tipo, cierre_fecha_inicio)
        REFERENCES cierre (tipo, fecha_inicio)
        ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE documento_fiscal (
    prefijo VARCHAR(50) NOT NULL,
    consecutivo BIGINT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    fecha_hora_emision TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cufe_cude VARCHAR(255) UNIQUE,
    xml_firmado TEXT,
    qr TEXT,
    estado_dian VARCHAR(50) NOT NULL,
    forma_pago VARCHAR(50) NOT NULL,
    cliente_tipo_doc VARCHAR(20) NOT NULL,
    cliente_num_doc VARCHAR(30) NOT NULL,
    venta_numero VARCHAR(50),
    orden_servicio_numero VARCHAR(100),
    numero_resolucion VARCHAR(100),
    corrige_prefijo VARCHAR(50),
    corrige_consecutivo BIGINT,
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
    prefijo VARCHAR(50) NOT NULL,
    consecutivo BIGINT NOT NULL,
    producto_referencia VARCHAR(100) NOT NULL,
    cantidad NUMERIC(10, 2) NOT NULL,
    valor_unit NUMERIC(14, 2) NOT NULL,
    descuento NUMERIC(14, 2) NOT NULL DEFAULT 0,
    iva NUMERIC(14, 2) NOT NULL DEFAULT 0,
    inc NUMERIC(14, 2) NOT NULL DEFAULT 0,
    es_gratuita BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (prefijo, consecutivo, producto_referencia),
    FOREIGN KEY (prefijo, consecutivo) REFERENCES documento_fiscal (prefijo, consecutivo)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (producto_referencia) REFERENCES producto (referencia)
        ON UPDATE CASCADE ON DELETE RESTRICT
);
