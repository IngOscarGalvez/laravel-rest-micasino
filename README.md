**Prueba Técnica Laravel: Integración de Sistemas de Pago**

### Descripción General:

En esta prueba deberás implementar la integración de dos sistemas de pago ficticios utilizando Laravel. Los dos sistemas de pago serán:

1. **Pago EasyMoney**
2. **Pago SuperWalletz**

### Requisitos

#### 1. Pago EasyMoney

- Deberás integrar el sistema de pago ficticio EasyMoney con Laravel. Esta es la documentación de la API de EasyMoney:
  
- Debes hacer una llamada POST a la siguiente URL:
  - URL: `/process`
  - Request Body:
    ```json
    {
      "amount": "<monto>",
      "currency": "<moneda>"
    }
    ```
- Lamentablemente el sistema de pago EasyMoney no puede procesar datos decimales, en ese caso nos devolverá un error que debemos manejar. Igualmente, maneja todos los casos de error que puedan ocurrir.

#### 2. Pago SuperWalletz

- Deberás integrar el sistema de pago ficticio SuperWalletz con Laravel. Esta es la documentación de la API de SuperWalletz:

- Debes hacer una llamada POST a la siguiente URL:
  - URL: `/pay`
  - Request Body:
    ```json
    {
      "amount": "<monto>",
      "currency": "<moneda>",
      "callback_url": "<tu_url_para_confirmacion>"
    }
    ```

- Al hacer la llamada, este nos devolverá un mensaje "success" con el id de la transacción en la plataforma de pago.
- Pasados unos segundos, la plataforma de pago nos mandará un webhook con la confirmación de pago a la URL que especificaste en el request body.

### Consideraciones Adicionales

- Se deben guardar todas las transacciones y su estado en la base de datos, independientemente de si fueron exitosas o no.
- También debemos guardar todas las requests/peticiones realizadas a la plataforma de pago, y también los webhooks que recibimos, para su posterior análisis por parte del equipo de BI.
- En la carpeta `/PAY-SERVERS` se encuentra un archivo `easy-money.js` y un archivo `super-walletz.js` que son simuladores de los servidores de pago. No debes modificarlos. Para que funcionen correctamente, debes ejecutar los siguientes comandos:

```bash
npm install
node easy-money.js # Ejecuta el servidor de Pago EasyMoney
node super-walletz.js # Ejecuta el servidor de Pago SuperWalletz
```

¡Buena suerte!



# Laravel Payment Integration

Este proyecto es una aplicación Laravel para integrar dos sistemas de pago ficticios: **EasyMoney** y **SuperWalletz**. Incluye funcionalidad para procesar pagos, manejar errores y registrar transacciones, además de recibir confirmaciones de pago a través de webhooks.

---

## **Requisitos**

- PHP >= 8.1
- Composer
- Laravel 11
- MySQL o SQLite
- Servidores de pago simulados (`easy-money.js` y `super-walletz.js`)

---

## **Instalación**

1. Clona este repositorio:

   ```bash
   git clone <url-del-repositorio>
   cd <nombre-del-repositorio>
   ```

2. Instala las dependencias con Composer:

   ```bash
   composer install
   ```

3. Copia el archivo `.env.example` a `.env` y configúralo:

   ```bash
   cp .env.example .env
   ```

4. Configura tu conexión a la base de datos en el archivo `.env`:

   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=payment_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Genera la clave de la aplicación:

   ```bash
   php artisan key:generate
   ```

6. Ejecuta las migraciones para crear las tablas necesarias:

   ```bash
   php artisan migrate
   ```

7. Arranca el servidor de desarrollo:

   ```bash
   php artisan serve
   ```

---

## **Simuladores de Pago**

1. Instala Node.js y las dependencias necesarias:

   ```bash
   npm install
   ```

2. Inicia los servidores simulados:

   - **EasyMoney**: Ejecuta `easy-money.js` en el puerto 3000:
     ```bash
     node easy-money.js
     ```

   - **SuperWalletz**: Ejecuta `super-walletz.js` en el puerto 3003:
     ```bash
     node super-walletz.js
     ```

---

## **Rutas API**

### **1. Procesar Pago con EasyMoney**

- **Endpoint**: `/api/easy-money`
- **Método**: `POST`
- **Body (JSON)**:
  ```json
  {
    "amount": 100,
    "currency": "USD"
  }
  ```
- **Respuesta**:
  - `200 OK`: Transacción exitosa.
  - `400 Bad Request`: Error en los datos enviados.
  - `500 Internal Server Error`: Fallo en el sistema.

---

### **2. Procesar Pago con SuperWalletz**

- **Endpoint**: `/api/super-walletz`
- **Método**: `POST`
- **Body (JSON)**:
  ```json
  {
    "amount": 100.50,
    "currency": "USD"
  }
  ```
- **Respuesta**:
  - `200 OK`: Transacción iniciada correctamente (esperar webhook).
  - `500 Internal Server Error`: Fallo en el sistema.

---

### **3. Webhook de Confirmación de SuperWalletz**

- **Endpoint**: `/api/webhook/superwalletz`
- **Método**: `POST`
- **Body (JSON)**:
  ```json
  {
    "transaction_id": "trx_12345",
    "status": "success"
  }
  ```
- **Respuesta**:
  - `200 OK`: Webhook procesado exitosamente.

---

## **Estructura de la Base de Datos**

La tabla `transactions` almacena todas las transacciones. Aquí están sus columnas principales:

| Campo            | Tipo       | Descripción                                      |
|-------------------|------------|--------------------------------------------------|
| `id`             | `bigint`   | ID único de la transacción.                     |
| `transaction_id` | `string`   | ID de la transacción en el sistema externo.     |
| `payment_gateway`| `string`   | Nombre del sistema de pago (`EasyMoney`, `SuperWalletz`). |
| `amount`         | `decimal`  | Monto de la transacción.                        |
| `currency`       | `string`   | Moneda de la transacción.                       |
| `status`         | `string`   | Estado de la transacción (`pending`, `success`, `failed`). |
| `request_data`   | `json`     | Datos enviados al sistema de pago.             |
| `response_data`  | `json`     | Respuesta recibida del sistema de pago.         |
| `created_at`     | `datetime` | Fecha de creación.                              |
| `updated_at`     | `datetime` | Fecha de última actualización.                  |

---

## **Pruebas con Postman**

1. Importa las siguientes rutas en Postman:

   - **EasyMoney**:
     - URL: `http://localhost/api/easy-money`
     - Método: `POST`
     - Body: `{ "amount": 100, "currency": "USD" }`

   - **SuperWalletz**:
     - URL: `http://localhost/api/super-walletz`
     - Método: `POST`
     - Body: `{ "amount": 100.50, "currency": "USD" }`

   - **Webhook SuperWalletz**:
     - URL: `http://localhost/api/webhook/superwalletz`
     - Método: `POST`
     - Body: `{ "transaction_id": "trx_12345", "status": "success" }`

---

## **Logs**

Puedes revisar los logs en `storage/logs/laravel.log` para más información sobre los errores.

---

## **Autores**

- Oscar Ledwing Ferneth Galvez Andrade
- Fecha: 2025-01-06


