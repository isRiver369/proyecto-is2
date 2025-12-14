
import sys
sys.path.insert(0, '/content/qa')

from src.pago_service import PagoService

def flujo_completo_pago():
    """
    Simula el flujo completo:
    1. Registrar un pago
    2. Obtener el comprobante
    3. Validar que todo funcione en cadena
    """
    servicio = PagoService()
    
    # Paso 1: Registrar pago
    resultado_pago = servicio.registrar_pago(101, 50.0, "tarjeta")
    if not resultado_pago["success"]:
        raise RuntimeError(f"Fallo en registro de pago: {resultado_pago.get('message')}")
    
    # Paso 2: Obtener comprobante
    comprobante = servicio.obtener_comprobante(101, 501)
    if comprobante is None:
        raise RuntimeError("No se generó el comprobante después del pago")
    
    print("Flujo de integración completado con éxito")
    print(f"- Pago procesado: ${comprobante['monto']}")
    print(f"- Servicio: {comprobante['nombre_servicio']}")
    print(f"- Usuario: {comprobante['nombre']}")
    return comprobante

if __name__ == "__main__":
    flujo_completo_pago()

#añadiendo comentario
