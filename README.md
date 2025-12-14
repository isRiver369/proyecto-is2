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

Para ejecutar la aplicación, se debe instalar MySQL y AMPPS (servidor web local). En donde se debe ejecutar o importar el query completo en una base de datos.
Se debe clonar el repositorio dentro de la carpeta "www" del Ampps, más el uso de un editor de código fuente (VsCode). Para realizar los diferente  cambios o ediciones de cada uno de los archivos .php. 


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
































