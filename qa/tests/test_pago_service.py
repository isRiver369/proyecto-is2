
import pytest
from src.pago_service import PagoService, NotificadorMock

@pytest.fixture
def servicio_pago():
    return PagoService()

def test_registro_pago_exitoso(servicio_pago):
    resultado = servicio_pago.registrar_pago(101, 50.0, "tarjeta")
    assert resultado["success"] is True
    assert servicio_pago.db["reservas"][101]["estado"] == "pagada"
    assert len(servicio_pago.notificador.enviados) == 1
    assert "Ana" in servicio_pago.notificador.enviados[0]["mensaje"]

def test_registro_pago_reserva_inexistente(servicio_pago):
    resultado = servicio_pago.registrar_pago(999, 10.0, "efectivo")
    assert resultado["success"] is False
    assert "Reserva no encontrada" in resultado["message"]

def test_comprobante_valido(servicio_pago):
    servicio_pago.registrar_pago(102, 20.0, "efectivo")
    comprobante = servicio_pago.obtener_comprobante(102, 502)
    assert comprobante is not None
    assert comprobante["monto"] == 20.0
    assert comprobante["nombre"] == "Luis"

def test_comprobante_reserva_no_pagada_404():
    servicio = PagoService()
    comprobante = servicio.obtener_comprobante(101, 501)
    assert comprobante is None

def test_comprobante_usuario_no_autorizado(servicio_pago):
    servicio_pago.registrar_pago(101, 50.0, "tarjeta")
    comprobante = servicio_pago.obtener_comprobante(101, 999)
    assert comprobante is None
