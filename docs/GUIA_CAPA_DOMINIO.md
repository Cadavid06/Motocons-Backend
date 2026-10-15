# 🧠 Guía de la Capa de Dominio en MOTOCONS (PHP 8.3+)

Esta guía explica en detalle cómo fue desarrollada la **Capa de Dominio** de MOTOCONS, las decisiones de arquitectura limpia tomadas y los fundamentos de la sintaxis moderna de PHP 8.x utilizada en las 24 entidades puras.

---

## 💎 1. ¿Por qué el Dominio está Aislado y sin ORMs?

En muchos tutoriales básicos de Laravel se suele mezclar todo en los modelos de Eloquent (Active Record). Sin embargo, en arquitecturas profesionales y limpias (Hexagonal / DDD):

* **El Dominio es agnóstico a la tecnología:** Las reglas de venta, comisiones, garantías y precios de MOTOCONS existen independientemente de si los datos se guardan en PostgreSQL, MySQL o archivos de texto.
* **Separación de Responsabilidades:** 
  * Una entidad de dominio (`App\Domain\Model\Motocicleta`) representa el **objeto del negocio**, sus invariantes y su comportamiento.
  * Un modelo de persistencia (`App\Infrastructure\Adapters\Out\Persistence\Eloquent\Models\Motocicleta`) solo sabe cómo hablar con las tablas SQL.
* **Testeabilidad Extrema:** Puedes probar todas las reglas de negocio con tests unitarios ultrarrápidos sin necesidad de levantar una base de datos ni configurar conexión en memoria.

---

## ⚡ 2. Sintaxis Moderna de PHP 8.x Explicada

Si vienes de Java, Spring Boot o versiones antiguas de PHP (PHP 7 o inferior), notarás características sintácticas muy potentes implementadas en este proyecto:

---

### A. Promoción de Propiedades en el Constructor (Constructor Property Promotion)

#### ❓ ¿Por qué definimos el constructor con los atributos directamente sin declarar las variables antes en la clase?

En versiones anteriores de PHP y en Java tradicional, definir una clase requería escribir lo mismo **4 veces**:
1. Declarar la variable de clase (`private string $nombre;`).
2. Recibirla como parámetro en el constructor (`string $nombre`).
3. Asignarla manualmente en el cuerpo (`$this->nombre = $nombre;`).
4. Crear el getter.

```php
// ❌ FORMA ANTIGUA (Verbosa y propensa a desincronizaciones):
class Cliente {
    private string $tipoDoc;
    private string $numDoc;
    private string $nombre;

    public function __construct(string $tipoDoc, string $numDoc, string $nombre) {
        $this->tipoDoc = $tipoDoc;
        $this->numDoc = $numDoc;
        $this->nombre = $nombre;
    }
}
```

Desde **PHP 8.0**, el lenguaje introdujo **Constructor Property Promotion**. Al agregar la visibilidad (`private`, `protected` o `public`) directamente en el parámetro del constructor, PHP automáticamente:
1. Declara la variable de instancia en la clase con ese tipo y visibilidad.
2. Recibe el valor por parámetro.
3. Lo asigna a `$this->propiedad`.

```php
// ✅ FORMA MODERNA (Constructor Property Promotion de PHP 8+):
class Cliente {
    public function __construct(
        private string $tipoDoc,
        private string $numDoc,
        private string $nombre
    ) {}
}
```

> **Ventajas:**
> * Reduce el código repetitivo (*boilerplate*) en más de un 65%.
> * Es exactamente equivalente a lo que hace **Kotlin** con sus constructores primarios o **TypeScript**.
> * Es imposible olvidar asignar una variable en el constructor.

---

### B. Tipos Nulables (`?string`, `?int`, `?DateTimeImmutable`)

#### ❓ ¿Por qué usamos `?string` si se supone que es fuertemente tipado?

En tipado estricto, la sintaxis `?Tipo` se denomina **Nullable Type** (Tipo Nulable), y es una de las mayores fortalezas de la seguridad de tipos moderna (**Null Safety**):

1. **`string` (No Nulable):** Obliga a que la variable contenga **siempre una cadena de texto**. Si alguien intenta pasarle `null`, el motor de PHP lanzará un error fatal en tiempo de ejecución.
   ```php
   private string $nombre // NUNCA puede ser null. Siempre debe tener texto.
   ```
2. **`?string` (Nulable):** Es un alias para la unión de tipos `string|null`. Indica formalmente que el campo es **opcional por naturaleza del negocio**.
   ```php
   private ?string $emailFacturacion = null // Puede ser un texto o puede ser null.
   ```

#### 🛡️ ¿Por qué esto protege el sistema contra errores?
* **Evita el temido error "NullPointerException" o "Call to a member function on null":** Quien lea la clase sabe con certeza matemática qué campos están garantizados de tener valor y qué campos requieren una verificación previa antes de operar sobre ellos.
* **Documentación viva en el código:** Un compañero que consuma la entidad sabe de inmediato que la placa (`?string $placa`) puede ser `null` porque una moto nueva recién llegada de fábrica aún no está matriculada.

---

### C. Fechas Inmutables (`DateTimeImmutable`)

En todas las entidades del dominio usamos `DateTimeImmutable` en lugar de `DateTime`:

* **`DateTime` tradicional es mutable:** Si tienes un objeto fecha y le sumas 12 meses (`$fecha->modify('+12 months')`), muta el objeto original en memoria y puede causar efectos secundarios difíciles de rastrear.
* **`DateTimeImmutable` es inmutable:** Al sumarle tiempo, **devuelve una nueva instancia** sin alterar la original. Esto garantiza la integridad histórica de auditorías, fechas de venta y vigencias de garantía.

---

## 🏛️ 3. Modelo de Dominio Rico vs Modelo de Dominio Anémico

#### ❓ ¿Por qué se definen métodos de negocio dentro de las entidades de dominio?

En sistemas mal diseñados, las entidades son simples contenedores de datos pasivos llenos de *getters* y *setters*, y toda la lógica se dispersa en controladores o servicios gigantescos. Esto se conoce como el antipatrón de **Modelo de Dominio Anémico (Anemic Domain Model)**.

En MOTOCONS aplicamos un **Modelo de Dominio Rico (Rich Domain Model)**:

### Ejemplos Reales de Reglas de Negocio en las Entidades:

#### 1. Validación de Límite de Garantías en `Motocicleta`:
La regla de negocio dice: *"Una moto nueva tiene garantizados hasta 3 mantenimientos oficiales gratuitos, siempre que la garantía esté vigente"* (**HU-08 / RF-05**).
La entidad `Motocicleta` contiene este método que protege su propia regla:
```php
public function validarAccesoAGarantia(int $revisionesUsadas): void
{
    if ($revisionesUsadas >= 3) {
        throw GarantiaExcedidaException::limiteRevisionesAlcanzado($this->vin);
    }

    if ($this->garantiaHasta !== null && (new DateTimeImmutable()) > $this->garantiaHasta) {
        throw GarantiaExcedidaException::garantiaVencidaPorFecha($this->vin);
    }
}
```

#### 2. Cálculo de Descuento según Tipo en `Promocion`:
La promoción sabe cómo calcular su propio descuento según si es porcentual o monto fijo (**HU-03**):
```php
public function calcularDescuento(float $precioBase): float
{
    if (strcasecmp($this->tipo, 'PORCENTAJE') === 0) {
        return round($precioBase * ($this->valor / 100), 2);
    }
    return min($this->valor, $precioBase);
}
```

#### 3. Mano de Obra Gratuita en `OrdenServicio`:
La orden de servicio sabe si debe cobrar mano de obra o si es gratuita por garantía oficial (**HU-09 / RF-06**):
```php
public function aplicaManoDeObraGratis(): bool
{
    return $this->nroMantGarantia !== null && $this->nroMantGarantia <= 3;
}
```

---

## 📊 4. Mapa de las 24 Entidades Creadas

Todas ubicadas en `app/Domain/Model/`:

| Módulo | Entidades Puras |
| :--- | :--- |
| **Terceros** | `Cliente`, `Empleado`, `Proveedor` |
| **Usuarios y Auditorías** | `Usuario`, `Auditoria` |
| **Catálogos y Promociones** | `Producto`, `ModeloMoto`, `Promocion` |
| **Compras e Inventario** | `OrdenCompra`, `OrdenCompraProducto`, `Recepcion`, `RecepcionProducto` |
| **Financiero y Créditos** | `RequisitoCredito`, `ResolucionDian`, `Cierre`, `LiquidacionComision` |
| **Ventas y Motocicletas** | `Venta`, `Motocicleta`, `VentaRequisito` |
| **Taller y Pagos** | `OrdenServicio`, `OrdenServicioProducto`, `Pago` |
| **Facturación DIAN** | `DocumentoFiscal`, `DocumentoFiscalDetalle` |

¡Con esta base sólida, cualquier compañero puede construir los casos de uso y adaptadores con total seguridad y claridad técnica!