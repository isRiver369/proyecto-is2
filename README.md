# proyecto-so2

Descripción del proyecto: 

Este es un sistema de reservas de cursos de diferentes categorías o actividades en donde los principales actores son:

  Visitante 
  Cliente/Usuario
  Administrador
  Proveedor
   
Por lo cual estos actores tienen las siguientes actividades:

Visitante = Solo puede realizar las siguientes acciones: Menú principal / Login / Registrarse / Catálogos (filtrar). En caso de realizar una acción clave del cliente, no tendrá permisos y será redirigido a crearse una cuenta. 
Cliente/Usuario =  Puede realizar lo mismo que el visitante y además puede revisar la información de los cursos / Agregar a sus reservas / Registrar / Pagar reservas. 
Administrador = Es el encargado de moderar todo el contenido de la aplicación.  CRUD de clientes, proveedores, cursos. Y configuración de métricas parametrizables y pasarelas. 
Proveedor = Es el encargado de proveer los diferentes servicios: creación, edición, consultar reportes, etc.


Instrucciones de ejecución:

ASOS PARA EJECUTAR PRUEBAS UNITARIAS

1. Preparar el entorno 
   - Trabajar en Google Colab o un entorno con Python ≥3.11.
   - Instalar las herramientas necesarias:
     pip install pytest pytest-cov

2. Estructurar el código 
   - Lógica de negocio en una carpeta src/ (ej. src/pago_service.py).
   - Pruebas unitarias en tests/ (ej. tests/test_pago_service.py).

3. Escribir pruebas con pytest* 
   - Incluir al menos 2 pruebas unitarias.
   - Cubrir *casos extremos* (ej. reserva inexistente, monto negativo).
   - Usar @pytest.fixture para reutilizar objetos.

4. Ejecutar las pruebas y medir cobertura  
   cd tu_carpeta/
   PYTHONPATH=. pytest tests/ --cov=src --cov-report=term-missing

   - Verificar que la *cobertura sea ≥80%*.
   - Asegurar que *todas las pruebas pasen*.


PASOS PARA EJECUTAR PRUEBAS DE RENDIMIENTO (K6)

1.Instalar K6 en Google Colab

   wget -O k6.tar.gz "https://github.com/grafana/k6/releases/download/v0.51.0/k6-v0.51.0-linux-amd64.tar.gz"
   tar -xzf k6.tar.gz
   ln -s k6-v0.51.0-linux-amd64/k6 k6
   chmod +x k6
   ./k6 version


2. Crear el script de prueba** (ej. test_k6_10vus.js)  
   - Definir número de usuarios virtuales (vus) y duración.
   - Apuntar a un endpoint real o de prueba (ej. https://jsonplaceholder.typicode.com/posts/1).
   - Incluir check para validar respuestas 200 OK.
   - Añadir sleep(1) para simular pausa entre peticiones.

3. Ejecutar con 10 VUs 
   ./k6 run test_k6_10vus.js

   - Anotar métricas: http_reqs, req/s, http_req_duration, checks.

4. Repetir con 50 VUs
   - Modificar el script a vus: 50.
   - Ejecutar de nuevo y comparar resultados.

5. nterpretar los resultados*
   - Verificar si el sistema escala (más usuarios → más peticiones).
   - Evaluar si no se degrada el tiempo de respuesta.
   - Confirmar que checks = 100% (todas las respuestas son válidas)

Instrucciones para ejecutar las pruebas unitarias y de rendimiento:

Las pruebas unitarias y de rendimiento se ejecutan automáticamente en GitHub Actions mediante el archivo pruebas.yml, ubicado en la carpeta .github/workflows. Cada vez que se hace un push a una rama (como feature/htmls), se activa el flujo de trabajo, que ejecuta las pruebas y muestra el resultado en la pestaña “Actions”con estado verde si pasan o rojo si fallan. Esto permite validar el código antes de mergearlo, asegurando calidad y estabilidad del sistema.


Integrantes del equipo y roles

Israel Rivera       =  DevOps
Andy Figueroa       =  Front-End
Stalin Najera       =  Back-End
Matias Peñaherrera  =  Back-End


Aplicación de SOLID y Patrón de diseño:

Principio de Responsabilidad Única (SRP): Separamos la lógica de validación de pagos en clases específicas (ValidadorTarjeta, ValidadorTransferencia) para que cada clase tenga una única razón para cambiar.

Inversión de Dependencias (DIP) y Segregación de Interfaces (ISP): Creamos la interfaz IPagoValidador en PHP. Esto permite que nuestro controlador no dependa de validadores concretos, sino de la abstracción.

Patrón Factory: Implementamos una fábrica (ValidadorFactory) para instanciar dinámicamente el validador correcto según el método de pago seleccionado por el usuario, eliminando condicionales complejos en el controlador.
































